<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreOrder;
use App\Services\DisputeService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function shippingOptions(Request $request)
    {
        $data = $request->validate(['address_id' => ['required', 'integer']]);
        $address = $request->user()->addresses()->findOrFail($data['address_id']);

        return ['stores' => $this->orders->shippingOptions($request->user(), $address)];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
            'shipping' => ['required', 'array', 'min:1'],
            'shipping.*.store_id' => ['required', 'integer'],
            'shipping.*.courier' => ['required', 'string', 'max:30'],
            'shipping.*.service' => ['required', 'string', 'max:30'],
        ]);

        return response()->json($this->orders->create($request->user(), $data), 201);
    }

    public function index(Request $request)
    {
        return $request->user()->orders()->with('storeOrders.items', 'storeOrders.store:id,name,slug', 'payment')
            ->latest('id')->cursorPaginate(20);
    }

    public function show(Request $request, int $id)
    {
        return $request->user()->orders()
            ->with('storeOrders.items', 'storeOrders.store:id,name,slug', 'storeOrders.shipment', 'payment')
            ->findOrFail($id); // hanya milik pembeli (anti-IDOR)
    }

    public function cancel(Request $request, int $id)
    {
        $order = $request->user()->orders()->findOrFail($id);
        $this->orders->cancelByBuyer($order, $request->input('reason'));

        return $order->fresh('storeOrders');
    }

    public function confirmReceived(Request $request, int $storeOrderId)
    {
        $so = $this->mine($request, $storeOrderId);
        $this->orders->confirmReceived($so);

        return $so->fresh();
    }

    public function complaint(Request $request, int $storeOrderId, DisputeService $disputes)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000']]);

        return response()->json($disputes->open($this->mine($request, $storeOrderId), $request->user(), $data['reason']), 201);
    }

    private function mine(Request $request, int $id): StoreOrder
    {
        return StoreOrder::whereHas('order', fn ($q) => $q->where('buyer_id', $request->user()->id))->findOrFail($id);
    }
}
