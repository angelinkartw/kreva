@extends('marketplace.layout')

@section('title', $product->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('marketplace.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke katalog</a>
    </div>

    <article class="grid gap-8 lg:grid-cols-2 lg:gap-12">
        <div>
            <div class="overflow-hidden rounded-3xl bg-[#f3f0e8]">
                @if ($product->images->isNotEmpty())
                    <img src="{{ $product->images->first()->url }}" alt="Foto {{ $product->name }}" class="aspect-square h-full w-full object-cover">
                @else
                    <div class="grid aspect-square place-items-center text-6xl" aria-label="Foto produk tidak tersedia">🛍️</div>
                @endif
            </div>
            @if ($product->images->count() > 1)
                <div class="mt-3 grid grid-cols-4 gap-3">
                    @foreach ($product->images->skip(1)->take(4) as $image)
                        <img src="{{ $image->url }}" alt="Foto lain {{ $product->name }}" class="aspect-square w-full rounded-xl object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-[.14em] text-[#558257]">{{ $product->category?->name }}</p>
            <h1 class="mt-2 text-3xl font-bold leading-tight text-[#26402e] sm:text-4xl">{{ $product->name }}</h1>
            <a href="{{ route('marketplace.index') }}" class="mt-3 inline-flex text-sm font-medium text-stone-600 hover:text-[#416743]">{{ $product->store?->name ?? 'Penjual UMKM' }}</a>

            <p class="mt-6 text-3xl font-bold text-[#26402e]">Rp {{ number_format((float) $product->price_min, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-stone-500">Terjual {{ number_format($product->sold_count) }} · Stok {{ number_format($product->variants->sum('stock')) }}</p>

            @if (auth()->check() && auth()->user()->account_type === 'buyer')
                @php($availableVariants = $product->variants->filter(fn ($variant) => $variant->stock > 0))
                @if ($availableVariants->isNotEmpty())
                    <form method="POST" action="{{ route('cart.items.store') }}" class="mt-6 space-y-3">
                        @csrf
                        <label for="product-variant" class="block text-sm font-semibold text-stone-700">Pilih varian</label>
                        <select id="product-variant" name="variant_id" required class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100">
                            @foreach ($availableVariants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->label ?: 'Default' }} · Rp {{ number_format($variant->price, 0, ',', '.') }} · stok {{ number_format($variant->stock) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="qty" value="1">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="submit" class="rounded-xl border border-[#26402e] px-4 py-3 text-sm font-semibold text-[#26402e] transition hover:bg-green-50">Masukkan ke troli</button>
                            <button type="submit" formaction="{{ route('checkout.buy-now') }}" class="rounded-xl bg-[#26402e] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#1e3325]">Beli sekarang</button>
                        </div>
                    </form>
                @else
                    <p class="mt-6 rounded-xl bg-stone-100 px-4 py-3 text-center text-sm font-semibold text-stone-500">Stok produk sedang habis</p>
                @endif

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('buyer.products.chats.store', ['slug' => $product->slug]) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-[#cddbcd] bg-white px-4 py-3 text-sm font-semibold text-[#26402e] transition hover:bg-[#f4f7f4]">Chat Seller</button>
                    </form>
                    <button id="product-ai-toggle" type="button" aria-expanded="false" aria-controls="product-ai-chat" class="rounded-xl bg-[#e6ede6] px-4 py-3 text-sm font-semibold text-[#26402e] transition hover:bg-[#dce7dc]">Tanya AI</button>
                </div>
            @elseif (auth()->guest())
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Masuk untuk membeli atau bertanya</a>
                </div>
            @endif

            <div class="mt-8 grid grid-cols-2 gap-3 rounded-2xl bg-white p-4 text-sm">
                <div>
                    <p class="text-xs text-stone-500">Berat</p>
                    <p class="mt-1 font-semibold text-stone-800">{{ number_format($product->weight_gram) }} gram</p>
                </div>
                <div>
                    <p class="text-xs text-stone-500">Lokasi toko</p>
                    <p class="mt-1 font-semibold text-stone-800">{{ collect([$product->store?->city, $product->store?->province])->filter()->join(', ') ?: 'Indonesia' }}</p>
                </div>
            </div>
        </div>
    </article>

    <section class="mt-10 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
            <h2 class="text-lg font-bold text-[#26402e]">Deskripsi produk</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-600">{{ $product->description ?: 'Penjual belum menambahkan deskripsi produk.' }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
            <h2 class="text-lg font-bold text-[#26402e]">Cerita pembuat</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-600">{{ $product->craftsman_story ?: 'Cerita pembuat belum tersedia.' }}</p>
        </div>
    </section>

    @auth
        @if (auth()->user()->account_type === 'buyer')
            <section id="product-ai-chat" class="mt-8 hidden overflow-hidden rounded-2xl border border-[#dce7dc] bg-white shadow-sm">
                <div class="border-b border-[#edf1ea] px-5 py-5 sm:px-7">
                    <p class="text-xs font-semibold uppercase tracking-[.14em] text-[#558257]">Asisten AI KREVA</p>
                    <h2 class="mt-1 text-lg font-bold text-[#26402e]">Tanya tentang {{ $product->name }}</h2>
                    <p class="mt-1 text-sm text-stone-500">Jawaban berdasarkan informasi produk, varian, harga, dan stok yang tersedia.</p>
                </div>
                <div class="space-y-4 p-5 sm:p-7">
                    <div id="product-ai-messages" role="log" aria-live="polite" aria-label="Percakapan dengan AI" class="flex max-h-80 min-h-28 flex-col gap-3 overflow-y-auto rounded-xl bg-[#f7faf6] p-4">
                        @if (empty($chatHistory))
                            <p class="my-auto text-sm leading-6 text-stone-500">Halo! Tanyakan hal spesifik tentang produk ini. Jika informasinya belum tercantum, aku akan memberi tahu.</p>
                        @else
                            @foreach ($chatHistory as $chatMessage)
                                <p class="{{ $chatMessage['role'] === 'user' ? 'ml-auto max-w-[90%] whitespace-pre-wrap rounded-2xl rounded-br-sm bg-[#26402e] px-4 py-3 text-sm leading-6 text-white' : 'mr-auto max-w-[90%] whitespace-pre-wrap rounded-2xl rounded-bl-sm bg-white px-4 py-3 text-sm leading-6 text-stone-700 shadow-sm' }}">{{ $chatMessage['content'] }}</p>
                            @endforeach
                        @endif
                    </div>
                    <p id="product-ai-error" role="alert" class="hidden text-sm text-red-700"></p>
                    <form id="product-ai-form" class="flex flex-col gap-3 sm:flex-row">
                        <label for="product-ai-input" class="sr-only">Pertanyaan tentang produk</label>
                        <textarea id="product-ai-input" rows="2" maxlength="2000" required placeholder="Contoh: Bahan apa yang digunakan?" class="min-h-12 flex-1 resize-y rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-[#558257] focus:outline-none focus:ring-2 focus:ring-green-100"></textarea>
                        <button id="product-ai-send" type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1e3325] disabled:cursor-not-allowed disabled:opacity-60">Kirim pertanyaan</button>
                    </form>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs leading-5 text-stone-500">Informasi produk dan percakapan dikirim ke OpenAI. Jangan masukkan data rahasia.</p>
                        <button id="product-ai-clear" type="button" class="text-xs font-semibold text-[#416743] hover:underline">Hapus percakapan</button>
                    </div>
                </div>
            </section>
            <script>
                (() => {
                    const toggle = document.getElementById('product-ai-toggle');
                    const panel = document.getElementById('product-ai-chat');
                    const form = document.getElementById('product-ai-form');
                    const input = document.getElementById('product-ai-input');
                    const messages = document.getElementById('product-ai-messages');
                    const error = document.getElementById('product-ai-error');
                    const sendButton = document.getElementById('product-ai-send');
                    const clearButton = document.getElementById('product-ai-clear');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const replyUrl = @json(route('buyer.products.ai-chat.reply', ['slug' => $product->slug]));
                    const clearUrl = @json(route('buyer.products.ai-chat.clear', ['slug' => $product->slug]));

                    const appendMessage = (role, content) => {
                        const item = document.createElement('p');
                        item.className = role === 'user'
                            ? 'ml-auto max-w-[90%] whitespace-pre-wrap rounded-2xl rounded-br-sm bg-[#26402e] px-4 py-3 text-sm leading-6 text-white'
                            : 'mr-auto max-w-[90%] whitespace-pre-wrap rounded-2xl rounded-bl-sm bg-white px-4 py-3 text-sm leading-6 text-stone-700 shadow-sm';
                        item.textContent = content;
                        messages.appendChild(item);
                        messages.scrollTop = messages.scrollHeight;
                    };
                    const resetMessages = (content) => {
                        messages.replaceChildren();
                        const notice = document.createElement('p');
                        notice.className = 'my-auto text-sm leading-6 text-stone-500';
                        notice.textContent = content;
                        messages.appendChild(notice);
                    };

                    toggle.addEventListener('click', () => {
                        const isOpening = panel.classList.contains('hidden');
                        panel.classList.toggle('hidden');
                        toggle.setAttribute('aria-expanded', String(isOpening));
                        if (isOpening) {
                            messages.scrollTop = messages.scrollHeight;
                            input.focus();
                            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });

                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        const question = input.value.trim();
                        if (!question) return;

                        error.classList.add('hidden');
                        sendButton.disabled = true;
                        clearButton.disabled = true;
                        sendButton.textContent = 'Menjawab...';
                        appendMessage('user', question);

                        try {
                            const response = await fetch(replyUrl, {
                                method: 'POST',
                                headers: {
                                    Accept: 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ message: question }),
                            });
                            const result = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(result.message || 'AI belum dapat menjawab. Silakan coba kembali.');

                            appendMessage('assistant', result.answer);
                            input.value = '';
                        } catch (requestError) {
                            error.textContent = requestError.message || 'Pertanyaan gagal dikirim. Silakan coba kembali.';
                            error.classList.remove('hidden');
                        } finally {
                            sendButton.disabled = false;
                            clearButton.disabled = false;
                            sendButton.textContent = 'Kirim pertanyaan';
                        }
                    });

                    clearButton.addEventListener('click', async () => {
                        clearButton.disabled = true;
                        sendButton.disabled = true;
                        error.classList.add('hidden');
                        try {
                            const response = await fetch(clearUrl, {
                                method: 'DELETE',
                                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            });
                            if (!response.ok) throw new Error('Percakapan gagal dihapus. Silakan coba kembali.');

                            resetMessages('Percakapan dihapus. Silakan mulai bertanya tentang produk ini.');
                        } catch (requestError) {
                            error.textContent = requestError.message || 'Percakapan gagal dihapus.';
                            error.classList.remove('hidden');
                        } finally {
                            clearButton.disabled = false;
                            sendButton.disabled = false;
                        }
                    });
                })();
            </script>
        @endif
    @endauth
@endsection
