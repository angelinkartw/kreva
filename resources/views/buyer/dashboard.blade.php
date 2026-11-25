@extends('marketplace.layout')

@section('title', 'Dashboard Pembeli')

@section('content')
    @if (session('database_error'))
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    @if ($databaseUnavailable)
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Produk belum dapat dimuat karena koneksi database bermasalah. Silakan coba kembali nanti.
        </div>
    @endif

    <section class="mb-8 rounded-3xl bg-[#26402e] px-6 py-8 text-white sm:px-10 sm:py-10">
        <p class="text-sm font-semibold uppercase tracking-wider text-green-200">Dashboard Pembeli</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Halo, {{ auth()->user()->nama_user }}!</h1>
        <p class="mt-3 max-w-2xl text-sm text-green-50 sm:text-base">Temukan produk lokal pilihan dan dukung UMKM Indonesia.</p>
        <div class="mt-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('marketplace.index') }}" class="inline-flex rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#26402e] hover:bg-green-50">Jelajahi Semua Produk</a>
                <a href="{{ route('buyer.orders.index') }}" class="inline-flex rounded-xl border border-white/40 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">Pesanan Saya</a>
            </div>
        </div>
    </section>

    <section>
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#558257]">Pilihan untuk Anda</p>
                <h2 class="mt-1 text-2xl font-bold text-[#26402e]">Produk terbaru</h2>
            </div>
            <a href="{{ route('marketplace.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">Lihat katalog &rarr;</a>
        </div>

        @if ($products->isEmpty())
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <h3 class="text-lg font-semibold">Belum ada produk tersedia</h3>
                <p class="mt-2 text-sm text-stone-500">Silakan kembali lagi nanti untuk melihat produk UMKM terbaru.</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <article class="flex flex-col rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                        <a href="{{ route('products.show', ['slug' => $product->slug]) }}" aria-label="Lihat detail {{ $product->name }}" class="mb-4 flex h-36 items-center justify-center overflow-hidden rounded-xl bg-[#f3f0e8]">
                            @if ($product->images->isNotEmpty())
                                <img src="{{ $product->images->first()->url }}" alt="Foto {{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-4xl" aria-hidden="true">🛍️</span>
                            @endif
                        </a>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#558257]">{{ $product->category?->name }}</p>
                        <h3 class="mt-1 text-lg font-bold text-stone-900"><a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="hover:text-[#416743]">{{ $product->name }}</a></h3>
                        <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ $product->description ?: 'Produk lokal pilihan dari pelaku UMKM.' }}</p>
                        <p class="mt-4 text-lg font-bold text-[#26402e]">Rp {{ number_format((float) $product->price_min, 0, ',', '.') }}</p>
                        <div class="mt-2 flex items-center justify-between border-t border-stone-100 pt-3 text-xs text-stone-500">
                            <span>{{ $product->store?->name ?? 'Penjual UMKM' }}</span>
                            <span>Stok {{ $product->variants_sum_stock }}</span>
                        </div>
                        @if ($product->variants->isNotEmpty())
                            <form method="POST" action="{{ route('cart.items.store') }}" class="mt-4 space-y-3">
                                @csrf
                                <label for="buyer-variant-{{ $product->id }}" class="block text-xs font-semibold text-stone-600">Pilih varian</label>
                                <select id="buyer-variant-{{ $product->id }}" name="variant_id" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
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
                        @else
                            <p class="mt-4 rounded-xl bg-stone-100 px-3 py-2.5 text-center text-sm font-medium text-stone-500">Stok habis</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
