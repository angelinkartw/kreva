<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $databaseUnavailable = false;

        try {
            $products = Product::query()
                ->with('seller')
                ->when($request->filled('q'), function ($query) use ($request) {
                    $search = $request->string('q')->trim()->toString();
                    $query->where(function ($query) use ($search) {
                        $query->where('nama_produk', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%")
                            ->orWhere('kategori', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString();
        } catch (QueryException $exception) {
            Log::warning('Marketplace could not load products because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $products = new LengthAwarePaginator([], 0, 12, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            $databaseUnavailable = true;
        }

        return view('marketplace.index', compact('products', 'databaseUnavailable'));
    }

    public function buyerDashboard(): View
    {
        $databaseUnavailable = false;

        try {
            $products = Product::query()
                ->with('seller')
                ->where('stok', '>', 0)
                ->latest()
                ->limit(8)
                ->get();
        } catch (QueryException $exception) {
            Log::warning('Buyer dashboard could not load products because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $products = collect();
            $databaseUnavailable = true;
        }

        return view('buyer.dashboard', compact('products', 'databaseUnavailable'));
    }

    public function dashboard(Request $request): View
    {
        $sellerId = $request->user()->id_user;
        $productQuery = Product::where('id_user', $sellerId);
        $databaseUnavailable = false;

        try {
            $stats = [
                'total_products' => (clone $productQuery)->count(),
                'active_products' => (clone $productQuery)->where('stok', '>', 0)->count(),
                'out_of_stock' => (clone $productQuery)->where('stok', 0)->count(),
            ];
            $recentProducts = (clone $productQuery)->latest()->limit(5)->get();
        } catch (QueryException $exception) {
            Log::warning('Seller dashboard could not load products because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $stats = ['total_products' => 0, 'active_products' => 0, 'out_of_stock' => 0];
            $recentProducts = collect();
            $databaseUnavailable = true;
        }

        return view('seller.dashboard', compact('stats', 'recentProducts', 'databaseUnavailable'));
    }

    public function manage(Request $request): View
    {
        $databaseUnavailable = false;

        try {
            $products = Product::where('id_user', $request->user()->id_user)
                ->latest()
                ->paginate(10);
        } catch (QueryException $exception) {
            Log::warning('Seller product list could not be loaded because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $products = new LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url()]);
            $databaseUnavailable = true;
        }

        return view('seller.products.index', compact('products', 'databaseUnavailable'));
    }

    public function create(): View
    {
        return view('seller.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->productRules());
        $validated['id_user'] = $request->user()->id_user;

        try {
            Product::create($validated);
        } catch (QueryException $exception) {
            Log::error('Product could not be saved because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->withInput()->with('database_error', 'Produk belum dapat disimpan karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Request $request, Product $product): View
    {
        $product = $this->ownedProduct($request, $product);

        return view('seller.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->productRules());

        try {
            $product = $this->ownedProduct($request, $product);
            $product->update($validated);
        } catch (QueryException $exception) {
            Log::error('Product could not be updated because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->withInput()->with('database_error', 'Produk belum dapat diperbarui karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->ownedProduct($request, $product)->delete();
        } catch (QueryException $exception) {
            Log::error('Product could not be deleted because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            return back()->with('database_error', 'Produk belum dapat dihapus karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    private function productRules(): array
    {
        return [
            'nama_produk' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stok' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }

    private function ownedProduct(Request $request, Product $product): Product
    {
        return Product::where('id_user', $request->user()->id_user)
            ->findOrFail($product->getKey());
    }
}
