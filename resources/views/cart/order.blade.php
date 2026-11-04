@extends('marketplace.layout')

@section('title', 'Pesanan Dibuat')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-green-200 bg-white p-8 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-800" aria-hidden="true">✓</div>
        <p class="mt-5 text-sm font-semibold uppercase tracking-wide text-[#558257]">Pesanan berhasil dibuat</p>
        <h1 class="mt-2 text-3xl font-bold text-[#26402e]">Terima kasih, {{ $order->nama_penerima }}!</h1>
        <p class="mt-3 text-sm text-stone-600">Pesanan #{{ $order->id_order }} sedang menunggu konfirmasi.</p>

        <div class="mt-6 rounded-2xl bg-[#fbf9f4] p-5 text-left">
            @foreach ($order->items as $item)
                <div class="flex justify-between gap-3 py-2 text-sm">
                    <span>{{ $item->nama_produk }} × {{ $item->kuantitas }}</span>
                    <span class="whitespace-nowrap font-semibold">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="mt-2 flex justify-between border-t border-stone-200 pt-4 font-bold">
                <span>Total</span>
                <span class="text-[#26402e]">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('marketplace.index') }}" class="rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Kembali Belanja</a>
            <a href="{{ route('buyer.dashboard') }}" class="rounded-xl border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-50">Dashboard Pembeli</a>
        </div>
    </div>
@endsection
