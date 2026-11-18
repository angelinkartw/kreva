<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\StoreOrder;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    private function mine(Request $request, int $id): StoreOrder
    {
        $store = $request->user()->store ?? abort(404);

        return StoreOrder::where('store_id', $store->id)->findOrFail($id);
    }

    public function index(Request $request)
    {
        $store = $request->user()->store ?? abort(404);
        $request->validate(['status' => ['nullable', 'in:PAID,PROCESSING,SHIPPED,DELIVERED,COMPLETED,CANCELLED,DISPUTED']]);

        return StoreOrder::where('store_id', $store->id)
            ->whereNotIn('status', ['PENDING_PAYMENT', 'EXPIRED']) // belum dibayar tidak tampil ke penjual
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->with('order:id,code,address_snapshot,paid_at', 'items')
            ->latest('id')->cursorPaginate(30);
    }

    public function show(Request $request, int $id)
    {
        return $this->mine($request, $id)->load('order:id,code,address_snapshot,paid_at', 'items', 'shipment', 'dispute');
    }

    public function accept(Request $request, int $id)
    {
        $so = $this->mine($request, $id);
        $this->orders->accept($so);

        return $so->fresh();
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);
        $so = $this->mine($request, $id);
        $this->orders->refund($so, 'Ditolak penjual: '.strip_tags($data['reason']));

        return $so->fresh();
    }

    public function ship(Request $request, int $id)
    {
        $data = $request->validate(['tracking_number' => ['required', 'string', 'min:5', 'max:60', 'regex:/^[A-Za-z0-9\-]+$/']]);
        $so = $this->mine($request, $id);
        $this->orders->ship($so, $data['tracking_number']);

        return $so->fresh('shipment');
    }

    /** Data label pengiriman (frontend mencetaknya sebagai PDF/HTML). */
    public function label(Request $request, int $id)
    {
        $so = $this->mine($request, $id)->load('order:id,code,address_snapshot', 'store', 'items');

        return [
            'order_code' => $so->order->code, 'courier' => strtoupper($so->courier).' '.$so->service,
            'tracking_number' => $so->tracking_number,
            'from' => ['name' => $so->store->name, 'address' => $so->store->origin_address, 'city' => $so->store->city],
            'to' => $so->order->address_snapshot,
            'items' => $so->items->map(fn ($i) => ['name' => $i->name_snapshot, 'variant' => $i->variant_label_snapshot, 'qty' => $i->qty]),
        ];
    }
}
