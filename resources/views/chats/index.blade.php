@extends('marketplace.layout')

@section('title', $chatRole === 'seller' ? 'Pesan Masuk' : 'Pesan Saya')

@section('content')
    <div class="mb-6">
        <a href="{{ $chatRole === 'seller' ? route('seller.dashboard') : route('buyer.dashboard') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke dashboard</a>
        <h1 class="mt-3 text-2xl font-bold text-[#26402e]">{{ $chatRole === 'seller' ? 'Pesan Masuk' : 'Pesan Saya' }}</h1>
        <p class="mt-1 text-sm text-stone-600">Percakapan buyer dan seller terkait produk.</p>
    </div>

    @if ($conversations->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold text-stone-800">Belum ada percakapan</h2>
            <p class="mt-2 text-sm text-stone-500">Percakapan tentang produk akan muncul di sini.</p>
        </div>
    @else
        <div class="divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white">
            @foreach ($conversations as $conversation)
                <a href="{{ route($chatRoutePrefix.'.show', $conversation) }}" class="flex items-center gap-4 p-4 transition hover:bg-[#f7faf6] sm:p-5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#f3f0e8]">
                        @if ($conversation->product?->images->isNotEmpty())
                            <img src="{{ $conversation->product->images->first()->url }}" alt="" class="h-full w-full object-cover">
                        @else
                            <span aria-hidden="true">🛍️</span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="truncate font-semibold text-stone-900">{{ $conversation->product?->name ?? 'Produk tidak tersedia' }}</h2>
                            <time class="text-xs text-stone-500">{{ $conversation->updated_at->format('d M Y, H:i') }}</time>
                        </div>
                        <p class="mt-1 text-sm text-stone-600">
                            {{ $chatRole === 'seller' ? 'Buyer: '.($conversation->buyer?->nama_user ?? 'Pengguna') : 'Seller: '.($conversation->seller?->nama_user ?? 'Penjual') }}
                        </p>
                    </div>
                    <span class="hidden text-sm font-semibold text-[#416743] sm:inline">Buka &rarr;</span>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $conversations->links() }}</div>
    @endif
@endsection
