<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

/** Gateway palsu untuk pengembangan lokal & test. Status diatur lewat cache (lihat DevPaymentController). */
class FakeGateway implements PaymentGateway
{
    public const KEY = 'fake-server-key';

    public function name(): string
    {
        return 'fake';
    }

    public function createTransaction(Order $order, array $customer): array
    {
        return [
            'reference' => 'FAKE-'.$order->code,
            'redirect_url' => rtrim(config('app.url'), '/').'/fake-pay/'.$order->code,
            'method' => 'fake',
        ];
    }

    public static function sign(string $orderId, string $statusCode, string $gross): string
    {
        return hash('sha512', $orderId.$statusCode.$gross.self::KEY);
    }

    public function verifySignature(array $p): bool
    {
        return hash_equals(self::sign($p['order_id'] ?? '', $p['status_code'] ?? '', $p['gross_amount'] ?? ''), (string) ($p['signature_key'] ?? ''));
    }

    public function fetchStatus(string $orderCode): array
    {
        $data = Cache::get("fake_gateway:$orderCode", ['status' => 'pending', 'gross_amount' => 0]);

        return ['status' => $data['status'], 'gross_amount' => (int) $data['gross_amount']];
    }
}
