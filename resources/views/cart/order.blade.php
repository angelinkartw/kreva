@extends('marketplace.layout')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-green-200 bg-white p-8 shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-800" aria-hidden="true">✓</div>
        <p class="mt-5 text-center text-sm font-semibold uppercase tracking-wide text-[#558257]">Pesanan berhasil dibuat</p>
        <h1 class="mt-2 text-center text-3xl font-bold text-[#26402e]">Terima kasih, {{ $order->address_snapshot['recipient'] ?? $order->buyer->nama_user }}!</h1>
        <p class="mt-3 text-center text-sm text-stone-600">Nomor pesanan {{ $order->code }} · {{ $order->status->label() }}</p>

        <div class="mt-6 space-y-4">
            @foreach ($order->storeOrders as $storeOrder)
                <section class="rounded-2xl border border-stone-200 p-5">
                    <h2 class="font-bold text-stone-900">{{ $storeOrder->store->name }}</h2>
                    <p class="mt-1 text-xs text-stone-500">{{ strtoupper($storeOrder->courier) }} {{ $storeOrder->service }}</p>
                    @foreach ($storeOrder->items as $item)
                        <div class="flex justify-between gap-3 py-2 text-sm">
                            <span>{{ $item->name_snapshot }}{{ $item->variant_label_snapshot ? ' · '.$item->variant_label_snapshot : '' }} × {{ $item->qty }}</span>
                            <span class="whitespace-nowrap font-semibold">Rp {{ number_format($item->price_snapshot * $item->qty, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="mt-2 flex justify-between border-t border-stone-100 pt-3 text-sm">
                        <span>Ongkir</span>
                        <span>Rp {{ number_format($storeOrder->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                </section>
            @endforeach
        </div>

        <div class="mt-5 flex justify-between border-t border-stone-200 pt-4 font-bold">
            <span>Total pembayaran</span>
            <span class="text-[#26402e]">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
        </div>

        @if ($order->payment?->redirect_url && $order->payment->gateway !== 'fake')
            <a href="{{ $order->payment->redirect_url }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Lanjutkan pembayaran</a>
        @elseif ($order->payment?->gateway === 'fake' && ! app()->isProduction())
            <a href="{{ route('buyer.fake-payment.show', $order->code) }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Bayar pesanan (simulasi lokal)</a>
        @endif

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('marketplace.index') }}" class="rounded-xl border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-50">Kembali belanja</a>
            <a href="{{ route('buyer.orders.index') }}" class="rounded-xl border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-50">Pesanan saya</a>
        </div>
    </div>
@endsection
