<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\FakeGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/** Hanya aktif saat PAYMENT_DRIVER=fake dan bukan production. Mensimulasikan webhook gateway. */
class DevPaymentController extends Controller
{
    public function simulate(Request $request, string $code, PaymentService $payments)
    {
        abort_unless(config('kreva.payment_driver') === 'fake' && ! app()->isProduction(), 404);
        $status = $request->validate(['status' => ['required', 'in:paid,expired,failed']])['status'];
        $order = Order::where('code', $code)->firstOrFail();

        Cache::put("fake_gateway:$code", ['status' => $status, 'gross_amount' => $order->total_paid], 3600);

        $gross = (string) $order->total_paid.'.00';
        $payload = [
            'order_id' => $code, 'status_code' => '200', 'gross_amount' => $gross,
            'signature_key' => FakeGateway::sign($code, '200', $gross),
            'transaction_status' => $status,
        ];

        return ['result' => $payments->handleWebhook($payload)];
    }
}
