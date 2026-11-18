<?php

namespace App\Services\Payment;

use App\Exceptions\DomainException;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class MidtransGateway implements PaymentGateway
{
    private function key(): string
    {
        return (string) config('services.midtrans.server_key');
    }

    private function snapUrl(): string
    {
        return config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    private function apiBase(): string
    {
        return config('services.midtrans.is_production') ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2';
    }

    public function name(): string
    {
        return 'midtrans';
    }

    public function createTransaction(Order $order, array $customer): array
    {
        $res = Http::withBasicAuth($this->key(), '')->acceptJson()->timeout(15)->post($this->snapUrl(), [
            'transaction_details' => ['order_id' => $order->code, 'gross_amount' => $order->total_paid],
            'customer_details' => ['first_name' => $customer['name'] ?? '', 'email' => $customer['email'] ?? '', 'phone' => $customer['phone'] ?? ''],
            'expiry' => ['unit' => 'hours', 'duration' => (int) config('kreva.payment_expire_hours')],
        ]);

        if (! $res->successful()) {
            throw new DomainException('Gagal membuat transaksi pembayaran. Coba lagi.', 'gateway_error');
        }

        return ['reference' => $res->json('token'), 'redirect_url' => $res->json('redirect_url'), 'method' => null];
    }

    public function verifySignature(array $p): bool
    {
        $expected = hash('sha512', ($p['order_id'] ?? '').($p['status_code'] ?? '').($p['gross_amount'] ?? '').$this->key());

        return hash_equals($expected, (string) ($p['signature_key'] ?? ''));
    }

    public function fetchStatus(string $orderCode): array
    {
        $res = Http::withBasicAuth($this->key(), '')->acceptJson()->timeout(15)->get($this->apiBase()."/{$orderCode}/status");

        if (! $res->successful()) {
            throw new DomainException('Gagal memeriksa status pembayaran.', 'gateway_error');
        }

        $status = match ($res->json('transaction_status')) {
            'settlement' => 'paid',
            'capture' => $res->json('fraud_status') === 'accept' ? 'paid' : 'pending',
            'deny', 'cancel', 'failure' => 'failed',
            'expire' => 'expired',
            default => 'pending',
        };

        return ['status' => $status, 'gross_amount' => (int) round((float) $res->json('gross_amount'))];
    }
}
