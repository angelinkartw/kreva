@extends('marketplace.layout')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="mx-auto max-w-4xl">
        <p class="text-sm font-semibold uppercase tracking-wide text-[#558257]">Belanja UMKM</p>
        <h1 class="mt-1 text-3xl font-bold text-[#26402e]">Keranjang Belanja</h1>

        @if ($databaseUnavailable || session('database_error'))
            <div role="alert" class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ session('database_error', 'Keranjang tidak dapat dimuat karena koneksi database sedang bermasalah. Silakan coba kembali nanti.') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <h2 class="text-lg font-semibold">Keranjang Anda masih kosong</h2>
                <p class="mt-2 text-sm text-stone-500">Pilih produk UMKM yang ingin Anda beli.</p>
                <a href="{{ route('marketplace.index') }}" class="mt-5 inline-flex rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Jelajahi Produk</a>
            </div>
        @else
            <div class="mt-6 divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                @foreach ($items as $item)
                    <article class="flex flex-wrap items-center justify-between gap-4 p-5">
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-semibold text-stone-900">{{ $item['product']->nama_produk }}</h2>
                            <p class="mt-1 text-sm text-stone-500">Rp {{ number_format((float) $item['product']->harga, 0, ',', '.') }} · Stok {{ $item['product']->stok }}</p>
                        </div>
                        <form method="POST" action="{{ route('cart.items.update', $item['product']) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <label for="quantity-{{ $item['product']->id_produk }}" class="sr-only">Jumlah {{ $item['product']->nama_produk }}</label>
                            <input id="quantity-{{ $item['product']->id_produk }}" name="quantity" type="number" min="1" max="100" value="{{ $item['quantity'] }}" class="w-20 rounded-lg border border-stone-300 px-3 py-2 text-sm">
                            <button type="submit" class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-semibold hover:bg-stone-50">Perbarui</button>
                        </form>
                        <p class="w-32 text-right font-bold text-[#26402e]">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        <form method="POST" action="{{ route('cart.items.remove', $item['product']) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Hapus</button>
                        </form>
                    </article>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-white p-5">
                <a href="{{ route('marketplace.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Lanjut belanja</a>
                <div class="text-right">
                    <p class="text-sm text-stone-500">Total</p>
                    <p class="text-2xl font-bold text-[#26402e]">Rp {{ number_format($total, 0, ',', '.') }}</p>
                    <a href="{{ route('checkout.create') }}" class="mt-3 inline-flex rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Lanjut ke Checkout</a>
                </div>
            </div>
        @endif
    </div>
@endsection
