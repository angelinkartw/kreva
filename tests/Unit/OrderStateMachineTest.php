<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStateMachineTest extends TestCase
{
    public function test_valid_transitions(): void
    {
        $this->assertTrue(OrderStatus::PendingPayment->canTransitionTo(OrderStatus::Paid));
        $this->assertTrue(OrderStatus::Paid->canTransitionTo(OrderStatus::Processing));
        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::Delivered));
        $this->assertTrue(OrderStatus::Delivered->canTransitionTo(OrderStatus::Completed));
        $this->assertTrue(OrderStatus::Delivered->canTransitionTo(OrderStatus::Disputed));
    }

    public function test_invalid_transitions_are_rejected(): void
    {
        $this->assertFalse(OrderStatus::PendingPayment->canTransitionTo(OrderStatus::Shipped));
        $this->assertFalse(OrderStatus::Paid->canTransitionTo(OrderStatus::Completed));
        $this->assertFalse(OrderStatus::Completed->canTransitionTo(OrderStatus::Cancelled));
        $this->assertFalse(OrderStatus::Expired->canTransitionTo(OrderStatus::Paid));
    }
}
