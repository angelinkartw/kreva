<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        return $this->payload($request);
    }

    public function add(Request $request)
    {
        $data = $request->validate(['variant_id' => ['required', 'integer'], 'qty' => ['required', 'integer', 'between:1,99']]);
        $variant = $this->purchasable($data['variant_id']);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->firstOrNew(['variant_id' => $variant->id]);
        $newQty = ($item->exists ? $item->qty : 0) + $data['qty'];

        if ($newQty > $variant->stock) {
            throw new DomainException("Stok tidak mencukupi (tersisa {$variant->stock}).", 'out_of_stock');
        }
        $item->qty = $newQty;
        $item->save();

        return $this->payload($request);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate(['qty' => ['required', 'integer', 'between:1,99']]);
        $item = $this->myCart($request)->items()->with('variant')->findOrFail($id);

        if ($data['qty'] > $item->variant->stock) {
            throw new DomainException("Stok tidak mencukupi (tersisa {$item->variant->stock}).", 'out_of_stock');
        }
        $item->update(['qty' => $data['qty']]);

        return $this->payload($request);
    }

    public function remove(Request $request, int $id)
    {
        $this->myCart($request)->items()->findOrFail($id)->delete();

        return $this->payload($request);
    }

    /** Gabungkan keranjang tamu (localStorage klien) saat login. */
    public function merge(Request $request)
    {
        $data = $request->validate(['items' => ['required', 'array', 'max:100'], 'items.*.variant_id' => ['required', 'integer'], 'items.*.qty' => ['required', 'integer', 'between:1,99']]);
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        foreach ($data['items'] as $row) {
            $variant = ProductVariant::with('product.store')->find($row['variant_id']);
            if (! $variant || $variant->stock < 1 || $variant->product->status !== 'active') {
                continue;
            }
            $item = $cart->items()->firstOrNew(['variant_id' => $variant->id]);
            $item->qty = min(($item->exists ? $item->qty : 0) + $row['qty'], $variant->stock);
            $item->save();
        }

        return $this->payload($request);
    }

    private function myCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function purchasable(int $variantId): ProductVariant
    {
        $variant = ProductVariant::with('product.store')->find($variantId);
        if (! $variant || $variant->product->status !== 'active' || ! $variant->product->store->isVerified()) {
            throw new DomainException('Produk tidak tersedia.', 'product_unavailable');
        }

        return $variant;
    }

    /** Keranjang dikelompokkan per toko, harga & stok selalu terbaru dari DB. */
    private function payload(Request $request): array
    {
        $cart = $this->myCart($request)->load('items.variant.product.store:id,name,slug,city');
        $groups = $cart->items->filter(fn ($i) => $i->variant)->groupBy(fn ($i) => $i->variant->product->store_id)->map(function ($items) {
            $store = $items->first()->variant->product->store;

            return [
                'store' => ['id' => $store->id, 'name' => $store->name, 'slug' => $store->slug, 'city' => $store->city],
                'items' => $items->map(fn ($i) => [
                    'id' => $i->id, 'variant_id' => $i->variant_id, 'product_id' => $i->variant->product_id,
                    'name' => $i->variant->product->name, 'variant' => $i->variant->label,
                    'price' => $i->variant->price, 'qty' => $i->qty, 'stock' => $i->variant->stock,
                    'available' => $i->variant->product->status === 'active' && $i->variant->stock >= $i->qty,
                    'subtotal' => $i->variant->price * $i->qty,
                ])->values(),
            ];
        })->values();

        return ['groups' => $groups, 'total_items' => $groups->sum(fn ($g) => $g['items']->sum('subtotal'))];
    }
}
