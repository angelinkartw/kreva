@extends('marketplace.layout')

@section('title', 'Pembayaran Simulasi')

@section('content')
    <div class="mx-auto max-w-xl rounded-3xl border border-amber-200 bg-white p-8 text-center shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-amber-700">Mode pengembangan</p>
        <h1 class="mt-2 text-3xl font-bold text-[#26402e]">Pembayaran simulasi</h1>
        <p class="mt-3 text-sm leading-6 text-stone-600">Ini bukan transaksi sungguhan. Gunakan tombol di bawah untuk menandai pembayaran pesanan sebagai berhasil di lingkungan lokal.</p>
        <div class="mt-6 rounded-2xl bg-[#fbf9f4] p-5 text-left">
            <p class="text-sm text-stone-500">Nomor pesanan</p>
            <p class="mt-1 font-semibold text-stone-900">{{ $order->code }}</p>
            <div class="mt-4 flex justify-between border-t border-stone-200 pt-4 font-bold">
                <span>Total</span>
                <span class="text-[#26402e]">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
            </div>
        </div>
        <form method="POST" action="{{ route('buyer.fake-payment.pay', $order->code) }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full rounded-xl bg-[#26402e] px-5 py-3 font-semibold text-white hover:bg-[#1e3325]">Tandai sudah dibayar</button>
        </form>
        <a href="{{ route('buyer.orders.show', $order) }}" class="mt-4 inline-flex text-sm font-semibold text-[#416743] hover:underline">Kembali ke pesanan</a>
    </div>
@endsection
