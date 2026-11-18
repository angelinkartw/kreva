<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGateway
{
    public function name(): string;

    /** @return array{reference: string, redirect_url: ?string, method: ?string} */
    public function createTransaction(Order $order, array $customer): array;

    /** Verifikasi signature webhook (wajib). */
    public function verifySignature(array $payload): bool;

    /**
     * Konfirmasi status langsung ke API gateway (jangan percaya payload webhook mentah).
     *
     * @return array{status: string, gross_amount: int} status: paid|pending|failed|expired
     */
    public function fetchStatus(string $orderCode): array;
}
