<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $databaseUnavailable = false;
        try {
            [$items, $total] = $this->cartContents($request);
        } catch (QueryException $exception) {
            Log::warning('Cart could not load products because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $items = collect();
            $total = 0;
            $databaseUnavailable = true;
        }

        return view('cart.index', compact('items', 'total', 'databaseUnavailable'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        try {
            $product = Product::findOrFail($validated['product_id']);
        } catch (QueryException $exception) {
            Log::error('Product could not be added to the cart because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->with('database_error', 'Keranjang belum dapat diperbarui karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }
        $cart = $request->session()->get('cart', []);
        $newQuantity = (int) ($cart[$product->id_produk] ?? 0) + $validated['quantity'];

        if ($product->stok < $newQuantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->nama_produk} tidak mencukupi.",
            ]);
        }

        $cart[$product->id_produk] = $newQuantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $productId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $product = Product::findOrFail($productId);
        } catch (QueryException $exception) {
            Log::error('Cart quantity could not be updated because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->with('database_error', 'Keranjang belum dapat diperbarui karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        if ($product->stok < $validated['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->nama_produk} tidak mencukupi.",
            ]);
        }

        $cart = $request->session()->get('cart', []);
        abort_unless(array_key_exists($product->id_produk, $cart), 404);

        $cart[$product->id_produk] = $validated['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Jumlah produk diperbarui.');
    }

    public function remove(Request $request, int $productId): RedirectResponse
    {
        try {
            Product::findOrFail($productId);
        } catch (QueryException $exception) {
            Log::error('Cart item could not be removed because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->with('database_error', 'Keranjang belum dapat diperbarui karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $cart = $request->session()->get('cart', []);
        unset($cart[$productId]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        try {
            [$items, $total] = $this->cartContents($request);
        } catch (QueryException $exception) {
            Log::error('Checkout could not load the cart because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return redirect()->route('cart.index')->with('database_error', 'Checkout belum dapat dimuat karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang Anda masih kosong.');
        }

        return view('cart.checkout', compact('items', 'total'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:2000'],
        ]);
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index')->with('status', 'Keranjang Anda masih kosong.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $validated, $request): Order {
                $products = Product::query()
                    ->whereIn('id_produk', array_keys($cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produk');

                if ($products->count() !== count($cart)) {
                    throw ValidationException::withMessages([
                        'cart' => 'Ada produk di keranjang yang sudah tidak tersedia. Periksa kembali keranjang Anda.',
                    ]);
                }

                $totalCents = 0;
                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);
                    if (! $product || $quantity < 1 || $quantity > $product->stok) {
                        throw ValidationException::withMessages([
                            'cart' => 'Stok produk berubah. Periksa kembali keranjang Anda sebelum checkout.',
                        ]);
                    }

                    $totalCents += (int) round((float) $product->harga * 100) * $quantity;
                }

                $order = Order::create([
                    'id_user' => $request->user()->id_user,
                    'nama_penerima' => $validated['nama_penerima'],
                    'telepon' => $validated['telepon'],
                    'alamat' => $validated['alamat'],
                    'total' => number_format($totalCents / 100, 2, '.', ''),
                    'status' => 'pending',
                ]);

                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);
                    $unitCents = (int) round((float) $product->harga * 100);

                    $order->items()->create([
                        'id_produk' => $product->id_produk,
                        'nama_produk' => $product->nama_produk,
                        'harga' => number_format($unitCents / 100, 2, '.', ''),
                        'kuantitas' => $quantity,
                        'subtotal' => number_format(($unitCents * $quantity) / 100, 2, '.', ''),
                    ]);

                    $product->decrement('stok', $quantity);
                }

                return $order;
            });
        } catch (QueryException $exception) {
            Log::error('Order could not be placed because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->withInput()->with('database_error', 'Pesanan belum dapat dibuat karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $request->session()->forget('cart');

        return redirect()->route('buyer.orders.show', $order);
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless($order->id_user === $request->user()->id_user, 404);
        $order->load('items');

        return view('cart.order', compact('order'));
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('cart.orders', compact('orders'));
    }

    /**
     * @return array{0: Collection<int, array{product: Product, quantity: int, subtotal: float}>, 1: float}
     */
    private function cartContents(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::query()
            ->whereIn('id_produk', array_keys($cart))
            ->get()
            ->keyBy('id_produk');

        $items = collect($cart)
            ->map(function ($quantity, $productId) use ($products): ?array {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $quantity = (int) $quantity;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->harga * $quantity,
                ];
            })
            ->filter()
            ->values();

        $validCart = $items->mapWithKeys(fn (array $item) => [
            $item['product']->id_produk => $item['quantity'],
        ])->all();

        if ($validCart !== $cart) {
            $request->session()->put('cart', $validCart);
            if ($validCart === []) {
                $request->session()->flash('status', 'Produk yang tidak lagi tersedia telah dihapus dari keranjang.');
            }
        }

        return [$items, $items->sum('subtotal')];
    }
}
