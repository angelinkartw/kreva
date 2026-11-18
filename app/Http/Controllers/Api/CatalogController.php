<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function products(Request $request)
    {
        $f = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'kategori' => ['nullable', 'string', 'max:100'],
            'harga_min' => ['nullable', 'integer', 'min:0'], 'harga_max' => ['nullable', 'integer', 'min:0'],
            'kota' => ['nullable', 'string', 'max:80'], 'rating' => ['nullable', 'numeric', 'between:0,5'],
            'tersedia' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:terbaru,terlaris,harga_asc,harga_desc,rating'],
            'per_page' => ['nullable', 'integer', 'between:1,48'],
        ]);

        $query = Product::query()->visible()->with(['store:id,name,slug,city,rating_avg', 'images']);
        $this->applyFilters($query, $f);

        return $query->cursorPaginate($f['per_page'] ?? 24)->withQueryString();
    }

    private function applyFilters($query, array $f, bool $withSort = true): void
    {
        if (! empty($f['q'])) {
            $term = '%'.str_replace(['%', '_'], ['\%', '\_'], mb_strtolower($f['q'])).'%';
            $query->where(fn ($w) => $w->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(description) LIKE ?', [$term]));
        }
        if (! empty($f['kategori'])) {
            $cat = Category::where('slug', $f['kategori'])->first();
            $ids = $cat ? $cat->children()->pluck('id')->push($cat->id) : collect([0]);
            $query->whereIn('category_id', $ids);
        }
        if (isset($f['harga_min'])) {
            $query->where('price_min', '>=', $f['harga_min']);
        }
        if (isset($f['harga_max'])) {
            $query->where('price_min', '<=', $f['harga_max']);
        }
        if (! empty($f['kota'])) {
            $query->whereHas('store', fn ($s) => $s->whereRaw('LOWER(city) = ?', [mb_strtolower($f['kota'])]));
        }
        if (isset($f['rating'])) {
            $query->where('rating_avg', '>=', $f['rating']);
        }
        if (! empty($f['tersedia'])) {
            $query->whereHas('variants', fn ($v) => $v->where('stock', '>', 0));
        }

        if ($withSort) {
            match ($f['sort'] ?? 'terbaru') {
                'terlaris' => $query->orderByDesc('sold_count'),
                'harga_asc' => $query->orderBy('price_min'),
                'harga_desc' => $query->orderByDesc('price_min'),
                'rating' => $query->orderByDesc('rating_avg'),
                default => $query->orderByDesc('created_at'),
            };
            $query->orderByDesc('id'); // tie-breaker untuk cursor pagination
        }
    }

    public function product(string $slug)
    {
        return Product::visible()->where('slug', $slug)
            ->with(['store:id,name,slug,city,province,logo,story,rating_avg,rating_count', 'variants', 'images', 'category:id,name,slug'])
            ->firstOrFail();
    }

    public function categories()
    {
        return Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
    }

    public function store(string $slug)
    {
        return Store::verified()->where('slug', $slug)->firstOrFail();
    }

    public function storeProducts(Request $request, string $slug)
    {
        $store = Store::verified()->where('slug', $slug)->firstOrFail();

        return $store->products()->where('status', 'active')->with('images')->latest('id')->cursorPaginate(24);
    }

    public function reviews(int $productId)
    {
        return Review::where('product_id', $productId)->where('moderation_status', 'published')
            ->with('buyer')->latest('id')->cursorPaginate(20);
    }
}
