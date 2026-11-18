<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\StoreOrder;
use App\Services\OrderService;
use Illuminate\Console\Command;

class AutoCompleteOrders extends Command
{
    protected $signature = 'kreva:auto-complete-orders';

    protected $description = 'Selesaikan otomatis pesanan yang tidak dikonfirmasi/dikomplain pembeli dan teruskan dana ke penjual';

    public function handle(OrderService $orders): int
    {
        $n = 0;

        // Barang sudah tercatat tiba + X hari tanpa komplain
        $delivered = StoreOrder::where('status', OrderStatus::Delivered)
            ->where('delivered_at', '<=', now()->subDays(config('kreva.auto_complete_days')));

        // Fallback bila pelacakan kurir belum terintegrasi: lama sejak dikirim
        $shipped = StoreOrder::where('status', OrderStatus::Shipped)
            ->where('shipped_at', '<=', now()->subDays(config('kreva.auto_complete_after_ship_days')));

        foreach ([$delivered, $shipped] as $query) {
            $query->orderBy('id')->chunkById(100, function ($chunk) use ($orders, &$n) {
                foreach ($chunk as $so) {
                    $orders->confirmReceived($so);
                    $n++;
                }
            });
        }

        $this->info("$n pesanan diselesaikan otomatis.");

        return self::SUCCESS;
    }
}
