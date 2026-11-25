@extends('marketplace.layout')

@section('title', 'Katalog Produk')

@section('content')
    @if (session('database_error'))
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    <section class="mb-8 rounded-3xl bg-[#26402e] px-6 py-8 text-white sm:px-10 sm:py-12">
        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-green-200">Dukung usaha lokal</p>
        <h1 class="text-3xl font-bold sm:text-4xl">Produk pilihan dari UMKM Indonesia</h1>
        <p class="mt-3 max-w-2xl text-sm text-green-50 sm:text-base">Jelajahi produk lokal yang dibuat oleh para pelaku usaha di sekitar kita.</p>
    </section>

    @if ($databaseUnavailable)
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Katalog belum dapat dimuat karena koneksi database bermasalah. Silakan coba kembali nanti.
        </div>
    @endif

    <form method="GET" action="{{ route('marketplace.index') }}" class="mb-6 grid gap-3 sm:grid-cols-[minmax(0,1fr)_minmax(12rem,0.6fr)_auto]">
        <div>
            <label for="q" class="sr-only">Cari produk</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Cari nama atau deskripsi produk"
                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        </div>
        <div>
            <label for="category_id" class="sr-only">Pilih kategori</label>
            <select id="category_id" name="category_id"
                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-[#26402e] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Tampilkan</button>
    </form>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold">Belum ada produk yang cocok</h2>
            <p class="mt-2 text-sm text-stone-500">Coba kata kunci lain atau kembali lagi nanti.</p>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <article class="flex flex-col rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <a href="{{ route('products.show', ['slug' => $product->slug]) }}" aria-label="Lihat detail {{ $product->name }}" class="mb-4 flex h-44 items-center justify-center overflow-hidden rounded-xl bg-[#f3f0e8]">
                        @if ($product->images->isNotEmpty())
                            <img src="{{ $product->images->first()->url }}" alt="Foto {{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-4xl" aria-hidden="true">🛍️</span>
                        @endif
                    </a>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#558257]">{{ $product->category?->name }}</p>
                    <h2 class="mt-1 text-lg font-bold text-stone-900"><a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="hover:text-[#416743]">{{ $product->name }}</a></h2>
                    <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ $product->description ?: 'Produk lokal pilihan dari pelaku UMKM.' }}</p>
                    <p class="mt-4 text-lg font-bold text-[#26402e]">Rp {{ number_format((float) $product->price_min, 0, ',', '.') }}</p>
                    <div class="mt-2 flex items-center justify-between border-t border-stone-100 pt-3 text-xs text-stone-500">
                        <span>{{ $product->store?->name ?? 'Penjual UMKM' }}</span>
                        <span>{{ $product->variants_sum_stock > 0 ? 'Stok '.$product->variants_sum_stock : 'Stok habis' }}</span>
                    </div>
                    @if ($product->variants->isNotEmpty())
                        @auth
                            @if (auth()->user()->account_type === 'buyer')
                                <form method="POST" action="{{ route('cart.items.store') }}" class="mt-4 space-y-3">
                                    @csrf
                                    <label for="variant-{{ $product->id }}" class="block text-xs font-semibold text-stone-600">Pilih varian</label>
                                    <select id="variant-{{ $product->id }}" name="variant_id" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                                        @foreach ($product->variants as $variant)
                                            <option value="{{ $variant->id }}">{{ $variant->label ?: 'Default' }} · Rp {{ number_format($variant->price, 0, ',', '.') }} · stok {{ $variant->stock }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="qty" value="1">
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="submit" class="rounded-xl border border-[#26402e] px-3 py-2.5 text-xs font-semibold text-[#26402e] hover:bg-green-50">Masukkan ke keranjang</button>
                                        <button type="submit" formaction="{{ route('checkout.buy-now') }}" class="rounded-xl bg-[#26402e] px-3 py-2.5 text-xs font-semibold text-white hover:bg-[#1e3325]">Beli langsung</button>
                                    </div>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="mt-4 inline-flex w-full justify-center rounded-xl bg-[#26402e] px-3 py-2.5 text-sm font-semibold text-white hover:bg-[#1e3325]">Masuk untuk membeli</a>
                        @endauth
                    @else
                        <p class="mt-4 rounded-xl bg-stone-100 px-3 py-2.5 text-center text-sm font-medium text-stone-500">Stok habis</p>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    @endif

    @guest
        <section class="mt-10 rounded-2xl border border-[#e5dac5] bg-white p-6 sm:flex sm:items-center sm:justify-between">
            <div class="max-w-2xl">
                <h2 class="text-lg font-bold">Bergabung dengan KREVA</h2>
                <p class="mt-1 text-sm text-stone-600">Buat akun pembeli untuk mengikuti katalog, atau akun penjual untuk menampilkan dan mengelola produk UMKM Anda.</p>
            </div>
            <div class="mt-4 flex flex-wrap gap-3 sm:mt-0">
                <a href="{{ route('register', ['account_type' => 'buyer']) }}" class="inline-flex rounded-full border border-[#26402e] px-5 py-3 text-sm font-semibold text-[#26402e] hover:bg-green-50">Daftar sebagai Pembeli</a>
                <a href="{{ route('register', ['account_type' => 'seller']) }}" class="inline-flex rounded-full bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Daftar sebagai Penjual</a>
            </div>
        </section>
    @endguest
@endsection
