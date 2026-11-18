<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Payout;
use App\Models\Setting;
use App\Models\StoreOrder;
use App\Services\AuditLogger;
use App\Services\DisputeService;
use App\Services\LedgerService;
use App\Exceptions\DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function summary(LedgerService $ledger)
    {
        return [
            'escrow_balance' => $ledger->balance('escrow'),
            'platform_revenue' => $ledger->balance('platform'),
            'gmv_completed' => (int) StoreOrder::where('status', 'COMPLETED')->sum('subtotal'),
            'pending_payouts' => (int) Payout::where('status', 'pending')->sum('amount'),
            'commission_percent' => (float) Setting::get('commission_percent', config('kreva.commission_percent')),
        ];
    }

    public function disputes(Request $request)
    {
        return Dispute::with('storeOrder.order:id,code', 'storeOrder.items')->where('status', $request->query('status', 'open'))->latest('id')->cursorPaginate(30);
    }

    public function resolveDispute(Request $request, int $id, DisputeService $service)
    {
        $data = $request->validate(['decision' => ['required', 'in:refund,release'], 'note' => ['nullable', 'string', 'max:1000']]);
        $dispute = Dispute::findOrFail($id);
        $service->resolve($dispute, $data['decision'], $data['note'] ?? null, $request->user());

        return $dispute->fresh();
    }

    public function payouts(Request $request)
    {
        return Payout::with('store:id,name')->where('status', $request->query('status', 'pending'))->latest('id')->cursorPaginate(30);
    }

    /** Setelah transfer dilakukan (manual / disbursement API) tandai selesai; bila ditolak, dana dikembalikan ke saldo penjual. */
    public function processPayout(Request $request, int $id, LedgerService $ledger)
    {
        $data = $request->validate(['status' => ['required', 'in:processed,rejected'], 'note' => ['nullable', 'string', 'max:500']]);

        return DB::transaction(function () use ($id, $data, $ledger, $request) {
            $payout = Payout::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($payout->status !== 'pending') {
                throw new DomainException('Penarikan sudah diproses.', 'payout_done');
            }
            $payout->forceFill(['status' => $data['status'], 'note' => $data['note'] ?? null, 'processed_at' => now()])->save();

            if ($data['status'] === 'rejected') {
                $ledger->post('seller', $payout->store_id, 'payout_reversal', $payout->amount, $payout, 'Penarikan ditolak');
            }
            AuditLogger::log('admin.payout_'.$data['status'], $payout, ['amount' => $payout->amount], $request->user()->id);

            return $payout;
        });
    }

    public function setCommission(Request $request)
    {
        $data = $request->validate(['commission_percent' => ['required', 'numeric', 'between:0,30']]);
        Setting::put('commission_percent', $data['commission_percent']);
        AuditLogger::log('admin.commission_changed', null, $data);

        return $data;
    }
}
