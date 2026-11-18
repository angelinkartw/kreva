<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Models\Order;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
        private OrderStateMachine $sm,
        private LedgerService $ledger,
        private OrderService $orders,
    ) {}

    public function verify(array $payload): bool
    {
        return $this->gateway->verifySignature($payload);
    }

    /**
     * Proses webhook secara idempotent. Dipanggil SETELAH signature diverifikasi.
     * Status selalu dikonfirmasi ulang ke API gateway sebelum mengubah pesanan.
     *
     * @return string processed|duplicate|ignored
     */
    public function handleWebhook(array $payload): string
    {
        $code = (string) ($payload['order_id'] ?? '');
        $order = Order::where('code', $code)->first();
        if (! $order) {
            return 'ignored';
        }

        $remote = $this->gateway->fetchStatus($code);

        if ($remote['status'] === 'paid' && $remote['gross_amount'] !== (int) $order->total_paid) {
            AuditLogger::log('payment.amount_mismatch', $order, ['expected' => $order->total_paid, 'got' => $remote['gross_amount']]);
            throw new DomainException('Nominal pembayaran tidak sesuai.', 'amount_mismatch');
        }

        return DB::transaction(function () use ($order, $remote, $payload) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();
            $payment = $order->payment;

            if ($payment->status === 'paid') {
                return 'duplicate'; // webhook ganda
            }

            if ($remote['status'] === 'paid') {
                $payment->forceFill(['status' => 'paid', 'paid_at' => now(), 'webhook_payload' => $payload])->save();

                if ($order->status !== OrderStatus::PendingPayment) {
                    // Dibayar setelah kedaluwarsa/dibatalkan: tandai agar admin melakukan refund.
                    AuditLogger::log('payment.late_paid', $order, ['status' => $order->status->value]);

                    return 'processed';
                }

                $this->sm->transition($order, OrderStatus::Paid);
                $order->forceFill(['paid_at' => now()])->save();

                foreach ($order->storeOrders as $so) {
                    $this->sm->transition($so, OrderStatus::Paid);
                    $this->ledger->post('escrow', 0, 'payment_in', $so->subtotal + $so->shipping_cost, $so, "Pembayaran {$order->code}");
                    NotificationService::notify($so->store->owner, 'order.new', "Pesanan baru {$order->code} menunggu diproses.");
                }
                NotificationService::notify($order->buyer, 'payment.success', "Pembayaran {$order->code} berhasil.");

                return 'processed';
            }

            if (in_array($remote['status'], ['failed', 'expired'], true)) {
                $this->orders->expire($order);

                return 'processed';
            }

            return 'ignored'; // pending
        });
    }
}
