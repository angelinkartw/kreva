@extends('marketplace.layout')

@section('title', 'Checkout')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_360px]">
        <section>
            <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke keranjang</a>
            <h1 class="mt-3 text-3xl font-bold text-[#26402e]">Checkout</h1>
            <p class="mt-2 text-sm text-stone-600">Isi detail penerima untuk membuat pesanan Anda.</p>

            @if (session('database_error'))
                <div role="alert" class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.store') }}" class="mt-6 space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
                @csrf
                <div>
                    <label for="nama_penerima" class="mb-2 block text-sm font-semibold">Nama penerima</label>
                    <input id="nama_penerima" name="nama_penerima" value="{{ old('nama_penerima', auth()->user()->nama_user) }}" required maxlength="150" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                    @error('nama_penerima') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="telepon" class="mb-2 block text-sm font-semibold">Nomor telepon</label>
                    <input id="telepon" name="telepon" type="tel" value="{{ old('telepon') }}" required maxlength="30" autocomplete="tel" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                    @error('telepon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="alamat" class="mb-2 block text-sm font-semibold">Alamat pengiriman</label>
                    <textarea id="alamat" name="alamat" rows="4" required maxlength="2000" autocomplete="street-address" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-[#26402e] px-5 py-3 font-semibold text-white hover:bg-[#1e3325]">Buat Pesanan</button>
            </form>
        </section>

        <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5">
            <h2 class="font-bold text-stone-900">Ringkasan Pesanan</h2>
            <div class="mt-4 space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between gap-3 text-sm">
                        <span class="text-stone-600">{{ $item['product']->nama_produk }} × {{ $item['quantity'] }}</span>
                        <span class="whitespace-nowrap font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between border-t border-stone-100 pt-4 font-bold">
                <span>Total</span>
                <span class="text-[#26402e]">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <p class="mt-3 text-xs leading-5 text-stone-500">Pesanan akan disimpan dengan status menunggu konfirmasi.</p>
        </aside>
    </div>
@endsection
