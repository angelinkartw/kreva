<?php

namespace App\Http\Controllers\Seller;

use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Support\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function store(Request $request): Store
    {
        return $request->user()->store ?? abort(404, 'Toko belum dibuat.');
    }

    private function mine(Request $request, int $id): Product
    {
        return $this->store($request)->products()->findOrFail($id); // kepemilikan dicek lewat relasi toko
    }

    private function rules(bool $partial = false): array
    {
        $s = $partial ? 'sometimes' : 'required';
        $max = config('kreva.max_variants_per_product');

        return [
            'name' => [$s, 'string', 'max:150'],
            'category_id' => [$s, 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'craftsman_story' => ['nullable', 'string', 'max:5000'],
            'status' => ['sometimes', 'in:draft,active,archived'],
            'weight_gram' => [$s, 'integer', 'between:1,100000'],
            'length_cm' => ['nullable', 'integer', 'min:1'], 'width_cm' => ['nullable', 'integer', 'min:1'], 'height_cm' => ['nullable', 'integer', 'min:1'],
            'variants' => array_merge([$s, 'array', "max:$max"], $partial ? [] : ['min:1']),
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['nullable', 'string', 'max:60'],
            'variants.*.label' => ['nullable', 'string', 'max:100'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.price' => ['required_with:variants', 'integer', 'between:100,1000000000'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'between:0,1000000'],
        ];
    }

    public function index(Request $request)
    {
        return $this->store($request)->products()->with('images', 'variants')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest('id')->cursorPaginate(30);
    }

    public function show(Request $request, int $id)
    {
        return $this->mine($request, $id)->load('images', 'variants', 'category');
    }

    public function create(Request $request)
    {
        $store = $this->store($request);
        $data = $request->validate($this->rules());
        $this->guardStatus($store, $data['status'] ?? 'draft');

        return DB::transaction(function () use ($store, $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = $store->products()->create([
                ...$data,
                'description' => Html::clean($data['description'] ?? null),
                'craftsman_story' => Html::clean($data['craftsman_story'] ?? null),
                'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
                'status' => $data['status'] ?? 'draft',
            ]);
            foreach ($variants as $v) {
                $product->variants()->create(collect($v)->except('id')->all());
            }
            $product->refreshPriceMin();

            return response()->json($product->load('variants'), 201);
        });
    }

    public function update(Request $request, int $id)
    {
        $product = $this->mine($request, $id);
        $data = $request->validate($this->rules(true));
        if (isset($data['status'])) {
            $this->guardStatus($product->store, $data['status']);
        }
        if ($product->status === 'banned') {
            throw new DomainException('Produk diblokir moderator.', 'banned');
        }

        return DB::transaction(function () use ($product, $data) {
            $variants = $data['variants'] ?? null;
            unset($data['variants']);

            foreach (['description', 'craftsman_story'] as $k) {
                if (array_key_exists($k, $data)) {
                    $data[$k] = Html::clean($data[$k]);
                }
            }
            $product->update($data);

            foreach ($variants ?? [] as $v) {
                if (! empty($v['id'])) {
                    $product->variants()->findOrFail($v['id'])->update(collect($v)->except('id')->all());
                } else {
                    $product->variants()->create(collect($v)->except('id')->all());
                }
            }
            $product->refreshPriceMin();

            return $product->load('variants', 'images');
        });
    }

    /** Arsipkan (bukan hapus) agar riwayat pesanan tetap utuh. */
    public function destroy(Request $request, int $id)
    {
        $this->mine($request, $id)->update(['status' => 'archived']);

        return response()->noContent();
    }

    /** URL unggah langsung ke object storage (presigned). Klien lalu memanggil addImage dengan path-nya. */
    public function presign(Request $request, int $id)
    {
        $product = $this->mine($request, $id);
        $data = $request->validate(['content_type' => ['required', 'in:image/jpeg,image/png,image/webp']]);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$data['content_type']];
        $path = "stores/{$product->store_id}/products/{$product->id}/".Str::uuid().".$ext"; // nama file diacak

        try {
            $signed = Storage::temporaryUploadUrl($path, now()->addMinutes(10), ['ContentType' => $data['content_type']]);
        } catch (\Throwable) {
            abort(501, 'Penyimpanan saat ini tidak mendukung unggah langsung (presigned). Gunakan disk S3/R2.');
        }

        return ['path' => $path, 'upload_url' => $signed['url'], 'headers' => $signed['headers'], 'max_bytes' => 5 * 1024 * 1024];
    }

    public function addImage(Request $request, int $id)
    {
        $product = $this->mine($request, $id);
        $data = $request->validate(['path' => ['required', 'string', 'max:300']]);

        $prefix = "stores/{$product->store_id}/products/{$product->id}/";
        if (! str_starts_with($data['path'], $prefix) || str_contains($data['path'], '..')) {
            abort(422, 'Path gambar tidak valid.');
        }
        if ($product->images()->count() >= 8) {
            throw new DomainException('Maksimal 8 foto per produk.', 'too_many_images');
        }

        // TODO: dispatch(new ProcessProductImage($path)) -> resize, kompres, konversi WebP, hapus metadata EXIF.
        return response()->json($product->images()->create(['path' => $data['path'], 'sort_order' => $product->images()->count()]), 201);
    }

    public function deleteImage(Request $request, int $id, int $imageId)
    {
        $img = $this->mine($request, $id)->images()->findOrFail($imageId);
        Storage::delete($img->path);
        $img->delete();

        return response()->noContent();
    }

    private function guardStatus(Store $store, string $status): void
    {
        if ($status === 'active' && ! $store->isVerified()) {
            throw new DomainException('Produk baru bisa ditayangkan setelah toko diverifikasi admin.', 'store_not_verified');
        }
    }
}
