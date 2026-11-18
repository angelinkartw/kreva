<?php

namespace App\Http\Controllers;

use App\Exceptions\DomainException;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\OrderService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BuyerCommerceController extends Controller
{
    public function __construct(
        private OrderService $orders,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $this->restoreBuyNowCart($request);

        try {
            $items = $this->cartItems($request);
        } catch (QueryException $exception) {
            Log::warning('Cart could not be loaded from the database.', ['exception' => $exception::class]);

            return view('cart.index', [
                'items' => collect(),
                'total' => 0,
                'databaseUnavailable' => true,
            ]);
        }

        $total = $items->sum(fn (CartItem $item) => $item->variant->price * $item->qty);

        return view('cart.index', compact('items', 'total') + ['databaseUnavailable' => false]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'min:1'],
            'qty' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                $variant = $this->purchasableVariant((int) $validated['variant_id'], true);
                $cart = $request->user()->cart()->firstOrCreate([]);
                $item = $cart->items()->where('variant_id', $variant->id)->lockForUpdate()->first();
                $quantity = ($item?->qty ?? 0) + $validated['qty'];

                if ($quantity > $variant->stock) {
                    throw ValidationException::withMessages([
                        'qty' => "Stok {$variant->product->name} tidak mencukupi.",
                    ]);
                }

                if ($item) {
                    $item->update(['qty' => $quantity]);
                } else {
                    $cart->items()->create(['variant_id' => $variant->id, 'qty' => $quantity]);
                }
            });
        } catch (QueryException $exception) {
            Log::error('Cart item could not be saved.', ['exception' => $exception::class]);

            return back()->with('database_error', 'Keranjang belum dapat diperbarui karena koneksi database bermasalah.');
        }

        return redirect()->route('cart.index')->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function buyNow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'min:1'],
            'qty' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $this->restoreBuyNowCart($request);

        try {
            $savedItems = DB::transaction(function () use ($request, $validated) {
                $variant = $this->purchasableVariant((int) $validated['variant_id'], true);
                if ($validated['qty'] > $variant->stock) {
                    throw ValidationException::withMessages([
                        'qty' => "Stok {$variant->product->name} tidak mencukupi.",
                    ]);
                }

                $cart = $request->user()->cart()->firstOrCreate([]);
                $saved = $cart->items()->get(['variant_id', 'qty'])
                    ->map(fn (CartItem $item) => ['variant_id' => $item->variant_id, 'qty' => $item->qty])
                    ->all();

                $cart->items()->delete();
                $cart->items()->create(['variant_id' => $variant->id, 'qty' => $validated['qty']]);

                return $saved;
            });
        } catch (QueryException $exception) {
            Log::error('Buy-now item could not be prepared.', ['exception' => $exception::class]);

            return back()->with('database_error', 'Checkout belum dapat dimulai karena koneksi database bermasalah.');
        }

        $request->session()->put('checkout.buy_now', true);
        $request->session()->put('checkout.saved_cart', $savedItems);

        return redirect()->route('checkout.create');
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $validated = $request->validate(['qty' => ['required', 'integer', 'min:1', 'max:100']]);
        $cartItem = $request->user()->cart()->firstOrFail()->items()->with('variant.product')->findOrFail($item);

        if ($validated['qty'] > $cartItem->variant->stock) {
            throw ValidationException::withMessages([
                'qty' => "Stok {$cartItem->variant->product->name} tidak mencukupi.",
            ]);
        }

        $cartItem->update(['qty' => $validated['qty']]);

        return redirect()->route('cart.index')->with('status', 'Jumlah produk diperbarui.');
    }

    public function remove(Request $request, int $item): RedirectResponse
    {
        $request->user()->cart()->firstOrFail()->items()->findOrFail($item)->delete();

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $items = $this->cartItems($request);
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang Anda masih kosong.');
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();
        $address = $request->query('address_id')
            ? $addresses->firstWhere('id', (int) $request->query('address_id'))
            : $addresses->first();

        if ($request->query('address_id') && ! $address) {
            abort(404);
        }

        $shippingOptions = $address
            ? $this->orders->shippingOptions($request->user(), $address)
            : [];
        $total = $items->sum(fn (CartItem $item) => $item->variant->price * $item->qty);

        return view('cart.checkout', compact('items', 'total', 'addresses', 'address', 'shippingOptions'));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'full_address' => ['required', 'string', 'max:2000'],
        ]);

        $address = $request->user()->addresses()->create($validated + [
            'label' => 'Alamat pengiriman',
            'is_default' => ! $request->user()->addresses()->exists(),
        ]);

        return redirect()->route('checkout.create', ['address_id' => $address->id])
            ->with('status', 'Alamat pengiriman berhasil disimpan.');
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'integer'],
            'shipping' => ['required', 'array', 'min:1'],
            'shipping.*.choice' => ['required', 'string', 'max:65'],
        ]);
        $validated['shipping'] = collect($validated['shipping'])
            ->map(function (array $selection, string|int $storeId): array {
                $parts = explode('|', $selection['choice'], 2);
                if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                    throw ValidationException::withMessages(['shipping' => 'Pilihan layanan pengiriman tidak valid.']);
                }

                return ['store_id' => (int) $storeId, 'courier' => $parts[0], 'service' => $parts[1]];
            })->values()->all();

        try {
            $order = $this->orders->create($request->user(), $validated);
        } catch (DomainException $exception) {
            return back()->withErrors(['checkout' => $exception->getMessage()])->withInput();
        } catch (QueryException $exception) {
            Log::error('Order could not be created from checkout.', ['exception' => $exception::class]);

            return back()->with('database_error', 'Pesanan belum dapat dibuat karena koneksi database bermasalah.');
        }

        $this->restoreBuyNowCart($request);

        return redirect()->route('buyer.orders.show', $order)
            ->with('status', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()->orders()->with('storeOrders.items')->latest()->paginate(10);

        return view('cart.orders', compact('orders'));
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless((int) $order->buyer_id === (int) $request->user()->getKey(), 404);
        $order->load('storeOrders.store', 'storeOrders.items', 'payment');

        return view('cart.order', compact('order'));
    }

    private function purchasableVariant(int $variantId, bool $lock): ProductVariant
    {
        $query = ProductVariant::with('product.store');
        if ($lock) {
            $query->lockForUpdate();
        }

        $variant = $query->findOrFail($variantId);
        if (! $variant->product || ! $variant->product->store
            || $variant->product->status !== 'active'
            || ! $variant->product->store->isVerified()) {
            throw ValidationException::withMessages(['variant_id' => 'Produk ini tidak tersedia untuk dibeli.']);
        }
        if ($variant->stock < 1) {
            throw ValidationException::withMessages(['variant_id' => 'Stok produk sedang habis.']);
        }

        return $variant;
    }

    private function cartItems(Request $request): Collection
    {
        return $request->user()->cart?->items()
            ->with(['variant.product.images', 'variant.product.store'])
            ->get() ?? collect();
    }

    private function restoreBuyNowCart(Request $request): void
    {
        if (! $request->session()->get('checkout.buy_now')) {
            return;
        }

        $cart = $request->user()->cart;
        if ($cart) {
            $savedItems = $request->session()->get('checkout.saved_cart', []);
            $cart->items()->delete();
            foreach ($savedItems as $savedItem) {
                if (ProductVariant::whereKey($savedItem['variant_id'])->exists()) {
                    $cart->items()->create($savedItem);
                }
            }
        }

        $request->session()->forget(['checkout.buy_now', 'checkout.saved_cart']);
    }
}
