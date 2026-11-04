<div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
    @if (session('database_error'))
        <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
    @endif

    <div>
        <label for="nama_produk" class="mb-2 block text-sm font-semibold">Nama Produk</label>
        <input id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk ?? '') }}" required maxlength="150"
            class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        @error('nama_produk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="kategori" class="mb-2 block text-sm font-semibold">Kategori</label>
        <input id="kategori" name="kategori" value="{{ old('kategori', $product->kategori ?? '') }}" required maxlength="100" placeholder="Contoh: Makanan, Kerajinan, Fashion"
            class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        @error('kategori') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="deskripsi" class="mb-2 block text-sm font-semibold">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="5000" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('deskripsi', $product->deskripsi ?? '') }}</textarea>
        @error('deskripsi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="harga" class="mb-2 block text-sm font-semibold">Harga (Rp)</label>
            <input id="harga" name="harga" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('harga', $product->harga ?? '') }}" required
                class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
            @error('harga') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="stok" class="mb-2 block text-sm font-semibold">Stok</label>
            <input id="stok" name="stok" type="number" min="0" max="4294967295" step="1" value="{{ old('stok', $product->stok ?? 0) }}" required
                class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
            @error('stok') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
