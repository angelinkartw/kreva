@extends('marketplace.layout')

@section('title', 'Katalog Produk')

@section('content')
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

    <form method="GET" action="{{ route('marketplace.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
        <label for="q" class="sr-only">Cari produk</label>
        <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Cari nama, kategori, atau deskripsi produk"
            class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
        <button type="submit" class="rounded-xl bg-[#26402e] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Cari Produk</button>
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
                    <div class="mb-4 flex h-36 items-center justify-center rounded-xl bg-[#f3f0e8] text-4xl" aria-hidden="true">🛍️</div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#558257]">{{ $product->kategori }}</p>
                    <h2 class="mt-1 text-lg font-bold text-stone-900">{{ $product->nama_produk }}</h2>
                    <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ $product->deskripsi ?: 'Produk lokal pilihan dari pelaku UMKM.' }}</p>
                    <p class="mt-4 text-lg font-bold text-[#26402e]">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</p>
                    <div class="mt-2 flex items-center justify-between border-t border-stone-100 pt-3 text-xs text-stone-500">
                        <span>{{ $product->seller?->nama_user ?? 'Penjual UMKM' }}</span>
                        <span>{{ $product->stok > 0 ? 'Stok '.$product->stok : 'Stok habis' }}</span>
                    </div>
                    @if ($product->stok > 0)
                        <form method="POST" action="{{ route('cart.items.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id_produk }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full rounded-xl bg-[#26402e] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1e3325]">Tambah ke Keranjang</button>
                        </form>
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
