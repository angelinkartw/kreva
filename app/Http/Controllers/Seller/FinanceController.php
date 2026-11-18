<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use App\Models\Payout;
use App\Models\Store;
use App\Models\StoreOrder;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(private LedgerService $ledger) {}

    private function store(Request $request): Store
    {
        return $request->user()->store ?? abort(404);
    }

    public function balance(Request $request)
    {
        $store = $this->store($request);
        $held = StoreOrder::where('store_id', $store->id)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Disputed])
            ->get()->sum(fn ($s) => $s->subtotal + $s->shipping_cost);

        return [
            'available' => $this->ledger->balance('seller', $store->id),
            'pending_in_escrow' => $held,
            'min_payout' => (int) config('kreva.min_payout'),
        ];
    }

    public function transactions(Request $request)
    {
        $store = $this->store($request);

        return LedgerEntry::where('account_type', 'seller')->where('account_id', $store->id)->orderByDesc('id')->cursorPaginate(30);
    }

    public function payouts(Request $request)
    {
        return Payout::where('store_id', $this->store($request)->id)->latest('id')->cursorPaginate(30);
    }

    public function requestPayout(Request $request)
    {
        $data = $request->validate(['amount' => ['required', 'integer', 'min:1']]);
        $store = $this->store($request);

        return DB::transaction(function () use ($store, $data) {
            Store::whereKey($store->id)->lockForUpdate()->first(); // serialisasi penarikan per toko

            $bank = $store->bankAccount;
            if (! $store->isVerified() || ! $bank || ! $bank->verified) {
                throw new DomainException('Toko dan rekening harus terverifikasi sebelum penarikan.', 'not_verified');
            }
            if ($bank->changed_at && $bank->changed_at->gt(now()->subDays(config('kreva.payout_wait_days')))) {
                throw new DomainException('Rekening baru diubah. Penarikan tersedia setelah masa tunggu keamanan.', 'bank_cooldown');
            }
            if ($data['amount'] < config('kreva.min_payout')) {
                throw new DomainException('Penarikan minimal Rp '.number_format(config('kreva.min_payout'), 0, ',', '.').'.', 'below_min');
            }
            if ($data['amount'] > $this->ledger->balance('seller', $store->id)) {
                throw new DomainException('Saldo tidak mencukupi.', 'insufficient_balance');
            }

            $payout = Payout::forceCreate([
                'store_id' => $store->id, 'amount' => $data['amount'], 'status' => 'pending',
                'bank' => $bank->bank, 'account_number' => $bank->account_number, 'account_holder' => $bank->account_holder,
            ]);
            $this->ledger->post('seller', $store->id, 'payout', -$data['amount'], $payout, 'Penarikan dana');
            AuditLogger::log('seller.payout_requested', $payout, ['amount' => $data['amount']]);

            return response()->json($payout, 201);
        });
    }

    /** Laporan bulanan (CSV): /seller/reports/monthly?month=2026-10 */
    public function monthlyReport(Request $request)
    {
        $data = $request->validate(['month' => ['required', 'date_format:Y-m']]);
        $store = $this->store($request);
        $start = \Carbon\Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();

        $rows = StoreOrder::where('store_id', $store->id)->where('status', OrderStatus::Completed)
            ->whereBetween('completed_at', [$start, $start->copy()->endOfMonth()])->with('order:id,code')->orderBy('completed_at')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['kode_pesanan', 'selesai_pada', 'subtotal', 'ongkir', 'komisi', 'diterima']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->order->code, $r->completed_at, $r->subtotal, $r->shipping_cost, $r->commission, $r->subtotal + $r->shipping_cost - $r->commission]);
            }
            fclose($out);
        }, "laporan-kreva-{$data['month']}.csv", ['Content-Type' => 'text/csv']);
    }
}
