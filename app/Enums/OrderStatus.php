<?php

namespace App\Enums;

/**
 * Dipakai untuk Order (PENDING_PAYMENT, PAID, EXPIRED, CANCELLED)
 * dan StoreOrder (seluruh status).
 */
enum OrderStatus: string
{
    case PendingPayment = 'PENDING_PAYMENT';
    case Paid = 'PAID';
    case Processing = 'PROCESSING';
    case Shipped = 'SHIPPED';
    case Delivered = 'DELIVERED';
    case Completed = 'COMPLETED';
    case Expired = 'EXPIRED';
    case Cancelled = 'CANCELLED';
    case Disputed = 'DISPUTED';
    case Resolved = 'RESOLVED';

    /** @return array<int, self> */
    public function allowedNext(): array
    {
        return match ($this) {
            self::PendingPayment => [self::Paid, self::Expired, self::Cancelled],
            self::Paid => [self::Processing, self::Cancelled],
            self::Processing => [self::Shipped, self::Cancelled],
            self::Shipped => [self::Delivered, self::Disputed],
            self::Delivered => [self::Completed, self::Disputed],
            self::Disputed => [self::Resolved, self::Cancelled, self::Completed],
            default => [],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->allowedNext(), true);
    }

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu pembayaran',
            self::Paid => 'Dibayar (menunggu diproses)',
            self::Processing => 'Diproses penjual',
            self::Shipped => 'Dikirim',
            self::Delivered => 'Tiba / diterima',
            self::Completed => 'Selesai',
            self::Expired => 'Kedaluwarsa',
            self::Cancelled => 'Dibatalkan',
            self::Disputed => 'Komplain',
            self::Resolved => 'Komplain selesai',
        };
    }
}
