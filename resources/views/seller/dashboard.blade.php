@extends('marketplace.layout')

@section('title', 'Dashboard Penjual')

@section('content')
    @if ($databaseUnavailable)
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Data produk belum dapat dimuat karena koneksi database bermasalah. Silakan coba kembali nanti.
        </div>
    @endif

    <section class="mb-8 rounded-3xl bg-[#26402e] px-6 py-8 text-white sm:px-10 sm:py-10">
        <p class="text-sm font-semibold uppercase tracking-wider text-green-200">Dashboard Penjual</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Halo, {{ auth()->user()->nama_user }}!</h1>
        <p class="mt-3 max-w-2xl text-sm text-green-50 sm:text-base">Kelola produk UMKM Anda dan tampilkan kepada pembeli di katalog KREVA.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('seller.products.create') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#26402e] hover:bg-green-50">+ Tambah Produk</a>
            <a href="{{ route('marketplace.index') }}" class="rounded-xl border border-white/40 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">Lihat Katalog</a>
        </div>
    </section>

    <section class="mb-8 grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Total Produk</p>
            <p class="mt-2 text-3xl font-bold text-[#26402e]">{{ number_format($stats['total_products']) }}</p>
        </article>
        <article class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Produk Tersedia</p>
            <p class="mt-2 text-3xl font-bold text-[#26402e]">{{ number_format($stats['active_products']) }}</p>
        </article>
        <article class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm text-stone-500">Stok Habis</p>
            <p class="mt-2 text-3xl font-bold text-[#26402e]">{{ number_format($stats['out_of_stock']) }}</p>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-100 px-5 py-4">
            <div>
                <h2 class="font-bold text-stone-900">Produk Terbaru</h2>
                <p class="mt-1 text-sm text-stone-500">Produk yang terakhir Anda tambahkan atau perbarui.</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">Kelola Semua Produk</a>
        </div>

        @if ($recentProducts->isEmpty())
            <div class="px-5 py-10 text-center">
                <p class="font-semibold text-stone-800">Toko Anda belum memiliki produk.</p>
                <p class="mt-2 text-sm text-stone-500">Tambahkan produk pertama agar tampil di katalog pembeli.</p>
                <a href="{{ route('seller.products.create') }}" class="mt-5 inline-flex rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Tambah Produk</a>
            </div>
        @else
            <div class="divide-y divide-stone-100">
                @foreach ($recentProducts as $product)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div>
                            <p class="font-semibold text-stone-900">{{ $product->nama_produk }}</p>
                            <p class="mt-1 text-sm text-stone-500">{{ $product->kategori }} · Stok {{ $product->stok }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-[#26402e]">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</span>
                            <a href="{{ route('seller.products.edit', $product) }}" class="text-sm font-semibold text-[#416743] hover:underline">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
