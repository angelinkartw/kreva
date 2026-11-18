<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Models\Order;
use App\Models\StoreOrder;

class OrderStateMachine
{
    public function transition(Order|StoreOrder $model, OrderStatus $to): void
    {
        $from = $model->status;

        if (! $from->canTransitionTo($to)) {
            throw new DomainException("Transisi status tidak valid: {$from->value} -> {$to->value}", 'invalid_transition');
        }

        $model->status = $to;

        if ($model instanceof StoreOrder) {
            match ($to) {
                OrderStatus::Shipped => $model->shipped_at = now(),
                OrderStatus::Delivered => $model->delivered_at = now(),
                OrderStatus::Completed => $model->completed_at = now(),
                default => null,
            };
        }

        $model->save();
    }
}
