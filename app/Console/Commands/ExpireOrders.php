<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    protected $signature = 'kreva:expire-orders';

    protected $description = 'Kedaluwarsakan pesanan yang melewati batas bayar dan kembalikan stok';

    public function handle(OrderService $orders): int
    {
        $n = 0;
        Order::where('status', OrderStatus::PendingPayment)->where('expired_at', '<=', now())
            ->orderBy('id')->chunkById(100, function ($chunk) use ($orders, &$n) {
                foreach ($chunk as $order) {
                    $orders->expire($order);
                    $n++;
                }
            });

        $this->info("$n pesanan dikedaluwarsakan.");

        return self::SUCCESS;
    }
}
