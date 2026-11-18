<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StoreOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $store = $request->user()->store ?? abort(404);
        $paidStatuses = [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Completed];

        $base = fn () => StoreOrder::where('store_orders.store_id', $store->id)
            ->join('orders', 'orders.id', '=', 'store_orders.order_id')
            ->whereIn('store_orders.status', array_map(fn ($s) => $s->value, $paidStatuses));

        $daily = $base()->where('orders.paid_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(orders.paid_at) as day'), DB::raw('SUM(store_orders.subtotal) as sales'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')->orderBy('day')->get();

        return [
            'sales_today' => (int) $base()->whereDate('orders.paid_at', today())->sum('store_orders.subtotal'),
            'new_orders' => StoreOrder::where('store_id', $store->id)->where('status', OrderStatus::Paid)->count(),
            'low_stock' => ProductVariant::whereHas('product', fn ($p) => $p->where('store_id', $store->id)->where('status', 'active'))->where('stock', '<=', 5)->count(),
            'rating' => ['average' => $store->rating_avg, 'count' => $store->rating_count],
            'daily_sales_30d' => $daily,
        ];
    }
}
