<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $databaseUnavailable = false;

        try {
            $filters = $request->validate([
                'q' => ['nullable', 'string', 'max:100'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            ]);
            $categories = Category::query()->orderBy('name')->get(['id', 'name']);
            $products = Product::query()
                ->visible()
                ->with(['category', 'store.owner', 'images', 'variants' => fn ($query) => $query->where('stock', '>', 0)])
                ->withSum('variants', 'stock')
                ->when(! empty($filters['q']), function ($query) use ($filters) {
                    $search = trim($filters['q']);
                    $query->where(function ($query) use ($search) {
                        $query->where('products.name', 'like', "%{$search}%")
                            ->orWhere('products.description', 'like', "%{$search}%")
                            ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when(! empty($filters['category_id']), fn ($query) => $query->where('products.category_id', $filters['category_id']))
                ->latest('products.created_at')
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
            $categories = collect();
            $databaseUnavailable = true;
        }

        return view('marketplace.index', compact('products', 'categories', 'databaseUnavailable'));
    }

    public function buyerDashboard(): View
    {
        $databaseUnavailable = false;

        try {
            $products = Product::query()
                ->visible()
                ->with(['category', 'store.owner', 'images', 'variants' => fn ($query) => $query->where('stock', '>', 0)])
                ->withSum('variants', 'stock')
                ->latest('products.created_at')
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
        $databaseUnavailable = false;

        try {
            $store = $this->sellerStore($request->user());
            $productQuery = DB::table('products')
                ->where('store_id', $store->id)
                ->whereNull('deleted_at');
            $stats = $this->sellerProductStats($store->id);
            $recentProducts = (clone $productQuery)
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select([
                    'products.id',
                    'products.name as nama_produk',
                    'categories.name as kategori',
                    'products.price_min as harga',
                ])
                ->selectSub(function ($query) {
                    $query->from('product_variants')
                        ->selectRaw('COALESCE(SUM(stock), 0)')
                        ->whereColumn('product_variants.product_id', 'products.id');
                }, 'stok')
                ->selectSub(function ($query) {
                    $query->from('product_images')
                        ->select('path')
                        ->whereColumn('product_images.product_id', 'products.id')
                        ->orderBy('sort_order')
                        ->limit(1);
                }, 'image_path')
                ->orderByDesc('products.created_at')
                ->limit(5)
                ->get();
        } catch (QueryException $exception) {
            Log::warning('Seller dashboard could not load products because the database is unavailable.', [
                'exception' => $exception::class,
            ]);

            $stats = ['total_products' => 0, 'active_products' => 0, 'out_of_stock' => 0];
            $recentProducts = collect();
            $databaseUnavailable = true;
        }

        $storeExists = ! $databaseUnavailable;

        return view('seller.dashboard', compact('stats', 'recentProducts', 'databaseUnavailable', 'storeExists'));
    }

    public function manage(Request $request): View
    {
        $databaseUnavailable = false;

        try {
            $store = $this->sellerStore($request->user());
            $products = $store->products()
                ->with(['category', 'images'])
                ->withSum('variants', 'stock')
                ->withMin('variants', 'price')
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

    public function create(Request $request): View|RedirectResponse
    {
        try {
            $this->sellerStore($request->user());
        } catch (QueryException $exception) {
            Log::error('Seller store could not be prepared for product creation.', [
                'exception' => $exception::class,
            ]);

            return redirect()->route('seller.products.index')
                ->with('database_error', 'Toko belum dapat disiapkan karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->productRules(imageRequired: true));
        $imagePath = $this->storeProductImage($request->file('image'));

        if ($request->hasFile('image') && $imagePath === null) {
            return back()->withInput()->with('database_error', 'Foto produk gagal disimpan. Silakan coba kembali.');
        }

        try {
            $product = DB::transaction(function () use ($request, $validated, $imagePath) {
                $store = $this->sellerStore($request->user());
                $category = Category::findOrFail($validated['category_id']);
                $product = $store->products()->create([
                    'category_id' => $category->id,
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(8)),
                    'description' => $validated['description'] ?? null,
                    'status' => 'active',
                    'weight_gram' => $validated['weight_gram'] ?? 100,
                ]);
                $product->variants()->create([
                    'label' => 'Default',
                    'price' => $validated['price'],
                    'stock' => $validated['stock'],
                ]);
                $product->refreshPriceMin();
                if ($imagePath !== null) {
                    $product->images()->create(['path' => $imagePath, 'sort_order' => 0]);
                }

                return $product->load(['category', 'variants']);
            });
        } catch (QueryException $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }
            Log::error('Product could not be saved because the database operation failed.', [
                'exception' => $exception::class,
            ]);

            return back()->withInput()->with('database_error', 'Produk belum dapat disimpan karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $variant = $product->variants->first();

        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5678/webhook/upload-produk', [
                'id_produk' => $product->id,
                'nama_produk' => $product->name,
                'kategori' => $product->category->name,
                'harga' => $variant?->price,
                'stok' => $variant?->stock,
                'penjual' => $request->user()->name ?? 'Penjual UMKM',
                'waktu' => now()->toDateTimeString(),
            ]);

            dd($response->status(), $response->body());

            if ($response->failed()) {
                Log::warning('Webhook n8n returned an unsuccessful response.', [
                    'status' => $response->status(),
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            Log::warning('Webhook n8n could not be reached.', [
                'exception' => $exception::class,
            ]);
        }

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil ditambahkan dan tampil di katalog pembeli.');
    }

    public function edit(Request $request, Product $product): View
    {
        $product = $this->ownedProduct($request, $product)->load(['category', 'variants', 'images']);
        $primaryVariant = $product->variants->first();

        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('seller.products.edit', compact('product', 'primaryVariant', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->productRules());
        $product = $this->ownedProduct($request, $product);
        $imagePath = $this->storeProductImage($request->file('image'));

        if ($request->hasFile('image') && $imagePath === null) {
            return back()->withInput()->with('database_error', 'Foto produk gagal disimpan. Silakan coba kembali.');
        }
        $oldImagePaths = [];

        try {
            DB::transaction(function () use ($product, $validated, $imagePath, &$oldImagePaths) {
                $category = Category::findOrFail($validated['category_id']);
                $product->update([
                    'category_id' => $category->id,
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                ]);

                $variant = $product->variants()->first();
                if ($variant) {
                    $variant->update([
                        'price' => $validated['price'],
                        'stock' => $validated['stock'],
                    ]);
                } else {
                    $product->variants()->create([
                        'label' => 'Default',
                        'price' => $validated['price'],
                        'stock' => $validated['stock'],
                    ]);
                }

                $product->refreshPriceMin();
                if ($imagePath !== null) {
                    $oldImagePaths = $product->images()->pluck('path')->all();
                    $product->images()->delete();
                    $product->images()->create(['path' => $imagePath, 'sort_order' => 0]);
                }
            });
        } catch (QueryException $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }
            Log::error('Product could not be updated because the database operation failed.', [
                'exception' => $exception::class,
            ]);

            return back()->withInput()->with('database_error', 'Produk belum dapat diperbarui karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $this->deleteProductImages($oldImagePaths);

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, int $product): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $store = $this->sellerStore($request->user());
            $product = $store->products()->find($product);

            if ($product === null) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Produk sudah tidak tersedia di etalase.',
                        'stats' => $this->sellerProductStats($store->id),
                    ]);
                }

                abort(404);
            }

            $imagePaths = $product->images()->pluck('path')->all();
            DB::transaction(function () use ($product) {
                $product->images()->delete();
                $product->delete();
            });
        } catch (QueryException $exception) {
            Log::error('Product could not be deleted because the database operation failed.', [
                'exception' => $exception::class,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Produk belum dapat dihapus karena koneksi database bermasalah. Silakan coba kembali nanti.',
                ], 500);
            }

            return back()->with('database_error', 'Produk belum dapat dihapus karena koneksi database bermasalah. Silakan coba kembali nanti.');
        }

        $this->deleteProductImages($imagePaths);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk berhasil dihapus.',
                'stats' => $this->sellerProductStats($product->store_id),
            ]);
        }

        return redirect()->route('seller.dashboard')
            ->with('status', 'Produk berhasil dihapus.');
    }

    private function productRules(bool $imageRequired = false): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:1', 'max:9999999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'weight_gram' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    private function storeProductImage(?UploadedFile $image): ?string
    {
        if ($image === null) {
            return null;
        }

        $path = $image->storePublicly('products', 'public');

        if ($path === false) {
            Log::error('Product image could not be written to the public storage disk.');

            return null;
        }

        return $path;
    }

    private function deleteProductImages(array $paths): void
    {
        if ($paths !== [] && ! Storage::disk('public')->delete($paths)) {
            Log::warning('One or more product image files could not be removed from public storage.');
        }
    }

    private function sellerProductStats(int $storeId): array
    {
        $products = DB::table('products')
            ->where('store_id', $storeId)
            ->whereNull('deleted_at');

        return [
            'total_products' => (clone $products)->count(),
            'active_products' => (clone $products)->where('status', 'active')->count(),
            'out_of_stock' => (clone $products)
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('product_variants.stock', '>', 0);
                })
                ->count(),
        ];
    }

    private function sellerStore(User $seller): Store
    {
        return Store::firstOrCreate(
            ['user_id' => $seller->id_user],
            [
                'name' => $seller->nama_user,
                'slug' => Str::slug($seller->nama_user ?: 'toko').'-'.$seller->id_user,
                'verification_status' => 'verified',
                'verified_at' => now(),
            ],
        );
    }

    private function ownedProduct(Request $request, Product $product): Product
    {
        $store = $this->sellerStore($request->user());

        return $store->products()->findOrFail($product->getKey());
    }
}
