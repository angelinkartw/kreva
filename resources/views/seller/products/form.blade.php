<div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
    @if (session('database_error'))
        <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
    @endif

    <div>
        <label for="name" class="mb-2 block text-sm font-semibold">Nama Produk</label>
        <input id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required maxlength="255"
            class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="rounded-2xl border-2 border-dashed border-[#9db89d] bg-[#f7faf6] p-5 sm:p-6">
        <label for="image" class="mb-2 block text-base font-semibold text-[#26402e]">Upload Foto Produk <span class="text-red-600">*</span></label>
        <p class="mb-4 text-sm text-stone-600">Pilih foto yang ingin ditampilkan kepada pembeli di katalog dan halaman utama.</p>
        @if (isset($product) && $product->images->isNotEmpty())
            <img src="{{ $product->images->first()->url }}" alt="Foto {{ $product->name }}" class="mb-4 h-40 w-40 rounded-xl object-cover shadow-sm">
        @endif
        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            @if (! isset($product)) required @endif
            class="block w-full cursor-pointer rounded-xl border border-stone-300 bg-white px-3 py-3 text-sm text-stone-700 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[#26402e] file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-[#1e3325]">
        <p class="mt-3 text-xs text-stone-500">Format JPG, PNG, atau WEBP. Maksimal 5 MB. Saat mengedit, kosongkan jika tidak ingin mengganti foto.</p>
        @error('image') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="category_id" class="mb-2 block text-sm font-semibold">Kategori</label>
        <select id="category_id" name="category_id" required
            class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
            <option value="">Pilih kategori produk</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @if ($categories->isEmpty())
            <p class="mt-1 text-sm text-amber-700">Kategori belum tersedia. Silakan hubungi admin untuk menambahkan kategori.</p>
        @endif
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="description" class="mb-2 block text-sm font-semibold">Deskripsi</label>
        <textarea id="description" name="description" rows="4" maxlength="5000" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="mb-2 block text-sm font-semibold">Harga (Rp)</label>
            <input id="price" name="price" type="number" min="1" max="9999999999999" step="1" value="{{ old('price', $primaryVariant->price ?? $product->price_min ?? '') }}" required
                class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
            @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="stock" class="mb-2 block text-sm font-semibold">Stok</label>
            <input id="stock" name="stock" type="number" min="0" max="4294967295" step="1" value="{{ old('stock', $primaryVariant->stock ?? 0) }}" required
                class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
            @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div>
        <label for="weight_gram" class="mb-2 block text-sm font-semibold">Berat pengiriman (gram)</label>
        <input id="weight_gram" name="weight_gram" type="number" min="1" max="4294967295" step="1" value="{{ old('weight_gram', $product->weight_gram ?? 100) }}" required
            class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        @error('weight_gram') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
