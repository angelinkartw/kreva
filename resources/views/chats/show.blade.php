@extends('marketplace.layout')

@section('title', 'Percakapan Produk')

@section('content')
    @php
        $otherParticipant = $chatRole === 'seller' ? $conversation->buyer : $conversation->seller;
        $isOwnMessage = fn ($message) => (int) $message->sender_id === (int) auth()->id();
    @endphp

    <div class="mb-6">
        <a href="{{ route($chatRoutePrefix.'.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke pesan</a>
    </div>

    <section class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <header class="flex items-center gap-4 border-b border-stone-100 p-4 sm:p-5">
            <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#f3f0e8]">
                @if ($conversation->product?->images->isNotEmpty())
                    <img src="{{ $conversation->product->images->first()->url }}" alt="" class="h-full w-full object-cover">
                @else
                    <span aria-hidden="true">🛍️</span>
                @endif
            </div>
            <div class="min-w-0">
                <h1 class="truncate font-bold text-[#26402e]">{{ $conversation->product?->name ?? 'Produk tidak tersedia' }}</h1>
                <p class="mt-0.5 text-sm text-stone-500">{{ $otherParticipant?->nama_user ?? 'Pengguna' }}</p>
            </div>
        </header>

        <div role="log" aria-live="polite" aria-label="Pesan percakapan" class="flex max-h-[60vh] min-h-72 flex-col gap-3 overflow-y-auto bg-[#fbf9f4] p-4 sm:p-6">
            @forelse ($conversation->messages as $message)
                <div class="{{ $isOwnMessage($message) ? 'ml-auto' : 'mr-auto' }} max-w-[88%]">
                    <p class="{{ $isOwnMessage($message) ? 'rounded-2xl rounded-br-sm bg-[#26402e] text-white' : 'rounded-2xl rounded-bl-sm bg-white text-stone-800 shadow-sm' }} whitespace-pre-wrap px-4 py-3 text-sm leading-6">{{ $message->body }}</p>
                    <time class="{{ $isOwnMessage($message) ? 'text-right' : 'text-left' }} mt-1 block text-[11px] text-stone-500">{{ $message->created_at->format('d M Y, H:i') }}</time>
                </div>
            @empty
                <p class="my-auto text-center text-sm text-stone-500">Belum ada pesan. Mulai percakapan tentang produk ini.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route($chatRoutePrefix.'.messages.store', $conversation) }}" class="flex gap-3 border-t border-stone-100 p-4 sm:p-5">
            @csrf
            <label for="chat-message" class="sr-only">Tulis pesan</label>
            <textarea id="chat-message" name="message" rows="2" maxlength="5000" required placeholder="Tulis pesan..." class="min-h-12 flex-1 resize-y rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('message') }}</textarea>
            <button type="submit" class="self-end rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1e3325]">Kirim</button>
        </form>
    </section>
@endsection
