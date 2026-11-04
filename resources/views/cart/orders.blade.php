@extends('marketplace.layout')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="mx-auto max-w-4xl">
        <p class="text-sm font-semibold uppercase tracking-wide text-[#558257]">Akun pembeli</p>
        <h1 class="mt-1 text-3xl font-bold text-[#26402e]">Pesanan Saya</h1>

        @if ($orders->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <h2 class="text-lg font-semibold">Belum ada pesanan</h2>
                <p class="mt-2 text-sm text-stone-500">Produk yang Anda checkout akan muncul di sini.</p>
                <a href="{{ route('marketplace.index') }}" class="mt-5 inline-flex rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Jelajahi Produk</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($orders as $order)
                    <a href="{{ route('buyer.orders.show', $order) }}" class="block rounded-2xl border border-stone-200 bg-white p-5 hover:border-[#558257]">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-stone-900">Pesanan #{{ $order->id_order }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $order->created_at?->format('d M Y, H:i') }} · {{ $order->items->count() }} produk</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold capitalize text-[#416743]">{{ $order->status }}</p>
                                <p class="mt-1 font-bold text-[#26402e]">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
