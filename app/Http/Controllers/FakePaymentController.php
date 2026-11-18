<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Payment\FakeGateway;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FakePaymentController extends Controller
{
    public function show(Request $request, string $code): View|RedirectResponse
    {
        $this->ensureFakePaymentsEnabled();
        $order = $this->buyerOrder($request, $code)->load('payment');

        if (! $order->payment || $order->status !== OrderStatus::PendingPayment) {
            return redirect()->route('buyer.orders.show', $order);
        }

        return view('cart.fake-payment', compact('order'));
    }

    public function pay(Request $request, string $code, PaymentService $payments): RedirectResponse
    {
        $this->ensureFakePaymentsEnabled();
        $order = $this->buyerOrder($request, $code)->load('payment');
        abort_unless($order->payment && $order->status === OrderStatus::PendingPayment, 404);

        Cache::put("fake_gateway:$code", [
            'status' => 'paid',
            'gross_amount' => $order->total_paid,
        ], 3600);

        $gross = (string) $order->total_paid.'.00';
        $payload = [
            'order_id' => $code,
            'status_code' => '200',
            'gross_amount' => $gross,
            'signature_key' => FakeGateway::sign($code, '200', $gross),
        ];
        $payments->handleWebhook($payload);

        return redirect()->route('buyer.orders.show', $order)
            ->with('status', 'Pembayaran simulasi berhasil.');
    }

    private function buyerOrder(Request $request, string $code): Order
    {
        return Order::query()
            ->where('code', $code)
            ->where('buyer_id', $request->user()->getKey())
            ->firstOrFail();
    }

    private function ensureFakePaymentsEnabled(): void
    {
        abort_unless(config('kreva.payment_driver') === 'fake' && ! app()->isProduction(), 404);
    }
}
