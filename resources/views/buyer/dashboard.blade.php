@extends('marketplace.layout')

@section('title', 'Dashboard Pembeli')

@section('content')
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
                        <div class="mb-4 flex h-36 items-center justify-center rounded-xl bg-[#f3f0e8] text-4xl" aria-hidden="true">🛍️</div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#558257]">{{ $product->kategori }}</p>
                        <h3 class="mt-1 text-lg font-bold text-stone-900">{{ $product->nama_produk }}</h3>
                        <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ $product->deskripsi ?: 'Produk lokal pilihan dari pelaku UMKM.' }}</p>
                        <p class="mt-4 text-lg font-bold text-[#26402e]">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</p>
                        <div class="mt-2 flex items-center justify-between border-t border-stone-100 pt-3 text-xs text-stone-500">
                            <span>{{ $product->seller?->nama_user ?? 'Penjual UMKM' }}</span>
                            <span>Stok {{ $product->stok }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
