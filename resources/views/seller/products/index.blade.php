@extends('marketplace.layout')

@section('title', 'Kelola Produk')

@section('content')
    @if ($databaseUnavailable || session('database_error'))
        <div role="alert" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('database_error', 'Data produk belum dapat dimuat karena koneksi database bermasalah. Silakan coba kembali nanti.') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[#558257]">Area penjual</p>
            <h1 class="mt-1 text-3xl font-bold text-[#26402e]">Kelola Produk</h1>
            <p class="mt-2 text-sm text-stone-600">Tambah, perbarui, dan hapus produk yang tampil di katalog UMKM.</p>
            <a href="{{ route('seller.dashboard') }}" class="mt-3 inline-flex text-sm font-semibold text-[#416743] hover:underline">&larr; Dashboard Penjual</a>
        </div>
        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">+ Tambah Produk</a>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold">Anda belum memiliki produk</h2>
            <p class="mt-2 text-sm text-stone-500">Tambahkan produk pertama agar dapat ditemukan pembeli.</p>
            <a href="{{ route('seller.products.create') }}" class="mt-5 inline-flex rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white">Tambah Produk</a>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                    <thead class="bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th class="px-5 py-4">Produk</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Harga</th>
                            <th class="px-5 py-4">Stok</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($products as $product)
                            <tr>
                                <td class="max-w-sm px-5 py-4">
                                    <p class="font-semibold text-stone-900">{{ $product->nama_produk }}</p>
                                    <p class="mt-1 truncate text-xs text-stone-500">{{ $product->deskripsi }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $product->kategori }}</td>
                                <td class="whitespace-nowrap px-5 py-4">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</td>
                                <td class="px-5 py-4">{{ $product->stok }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('seller.products.edit', $product) }}" class="font-semibold text-[#416743] hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-stone-100 px-5 py-4">{{ $products->links() }}</div>
        </div>
    @endif
@endsection
