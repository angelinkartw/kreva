<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** Dipanggil gateway. Wajib: signature valid -> status dikonfirmasi ke API gateway -> idempotent. */
    public function webhook(Request $request, PaymentService $payments)
    {
        $payload = $request->all();

        if (! $payments->verify($payload)) {
            AuditLogger::log('payment.webhook_invalid_signature', null, ['order_id' => $payload['order_id'] ?? null]);
            abort(403, 'Invalid signature');
        }

        try {
            $result = $payments->handleWebhook($payload);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => $result]);
    }

    public function status(Request $request, int $orderId)
    {
        $order = $request->user()->orders()->with('payment')->findOrFail($orderId);

        return [
            'order_code' => $order->code,
            'order_status' => $order->status,
            'payment_status' => $order->payment->status,
            'amount' => $order->payment->amount,
            'redirect_url' => $order->payment->redirect_url,
            'expired_at' => $order->expired_at,
            'seconds_left' => max(0, now()->diffInSeconds($order->expired_at, false)),
        ];
    }
}
