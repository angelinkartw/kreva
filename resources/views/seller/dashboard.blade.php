@extends('marketplace.layout')

@section('title', 'Beranda Penjual')

@section('content')
    @if ($databaseUnavailable)
        <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
            </svg>
            <div>
                <p class="font-semibold">Data produk belum dapat dimuat</p>
                <p class="mt-1">Koneksi database sedang bermasalah. Coba muat ulang halaman beberapa saat lagi.</p>
            </div>
        </div>
    @endif

    <section class="relative isolate mb-8 overflow-hidden rounded-[2rem] bg-[#26402e] shadow-lg shadow-[#26402e]/10">
        <div class="absolute inset-0 -z-10 bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(30,51,37,.98) 0%, rgba(38,64,46,.92) 50%, rgba(38,64,46,.48) 100%), url('{{ asset('images/landing/hero-composition.png') }}')"></div>
        <div class="absolute -right-20 -top-28 -z-10 h-80 w-80 rounded-full border border-white/10"></div>
        <div class="absolute -right-8 -top-16 -z-10 h-56 w-56 rounded-full border border-white/10"></div>

        <div class="grid items-center gap-8 px-6 py-9 sm:px-10 sm:py-12 lg:grid-cols-[minmax(0,1fr)_auto] lg:px-14">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-[#f5efe3]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#e9a46e]"></span>
                    BERANDA PENJUAL
                </span>
                <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Halo, {{ auth()->user()->nama_user }}!
                </h1>
                <p class="mt-3 max-w-xl text-sm leading-7 text-[#f4eee2] sm:text-base">
                    Ceritakan keunikan produk dan kembangkan usahamu bersama pembeli yang menghargai karya lokal.
                </p>
                <div class="mt-7 flex flex-wrap gap-3">
                    @if ($storeExists)
                        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-[#f8f5ee] px-5 py-3 text-sm font-semibold text-[#26402e] shadow-sm transition hover:-translate-y-0.5 hover:bg-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                            </svg>
                            Tambah produk
                        </a>
                    @else
                        <span class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-5 py-3 text-sm font-medium text-white">
                            Toko siap dibuat setelah profil usaha dilengkapi
                        </span>
                    @endif
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/35 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Lihat katalog
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 17 17 7M7 7h10v10" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="hidden lg:block">
                <img src="{{ asset('images/landing/logo-crop.png') }}" alt="" class="h-36 w-36 rounded-3xl border border-white/20 bg-white/10 object-contain p-4 shadow-2xl shadow-black/10 xl:h-44 xl:w-44">
            </div>
        </div>
    </section>

    <section aria-label="Ringkasan toko" class="mb-8 grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-[#ede8dc] bg-white p-5 shadow-sm shadow-stone-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-stone-500">Total produk</p>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#e6ede6] text-[#345236]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5" />
                    </svg>
                </span>
            </div>
            <p data-product-stat="total_products" class="mt-4 text-3xl font-bold tracking-tight text-[#26402e]">{{ number_format($stats['total_products']) }}</p>
            <p class="mt-1 text-xs text-stone-500">Semua produk di tokomu</p>
        </article>

        <article class="rounded-2xl border border-[#ede8dc] bg-white p-5 shadow-sm shadow-stone-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-stone-500">Produk tersedia</p>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6" />
                    </svg>
                </span>
            </div>
            <p data-product-stat="active_products" class="mt-4 text-3xl font-bold tracking-tight text-[#26402e]">{{ number_format($stats['active_products']) }}</p>
            <p class="mt-1 text-xs text-stone-500">Siap ditemukan pembeli</p>
        </article>

        <article class="rounded-2xl border border-[#ede8dc] bg-white p-5 shadow-sm shadow-stone-900/[0.02]">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-stone-500">Stok habis</p>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#f8ede1] text-[#b65b2d]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </span>
            </div>
            <p data-product-stat="out_of_stock" class="mt-4 text-3xl font-bold tracking-tight text-[#26402e]">{{ number_format($stats['out_of_stock']) }}</p>
            <p class="mt-1 text-xs text-stone-500">Perlu diperbarui stoknya</p>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-[#ede8dc] bg-white shadow-sm shadow-stone-900/[0.02]">
        <div id="product-delete-message" role="alert" aria-live="polite" class="hidden border-b border-red-100 bg-red-50 px-5 py-3 text-sm text-red-700 sm:px-7"></div>
        <div class="flex flex-col gap-3 border-b border-[#f0ebd9] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.14em] text-[#558257]">Etalase toko</p>
                <h2 class="mt-1 text-lg font-bold text-[#26402e]">Produk terbaru</h2>
                <p class="mt-1 text-sm text-stone-500">Pantau produk yang terakhir kamu tambahkan.</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1 self-start text-sm font-semibold text-[#416743] hover:text-[#26402e] sm:self-auto">
                Kelola semua produk
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6" />
                </svg>
            </a>
        </div>

        @if ($recentProducts->isEmpty())
            <div class="px-5 py-12 text-center sm:px-8">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f4f7f4] text-[#416743]">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5" />
                    </svg>
                </div>
                @if ($storeExists)
                    <h3 class="mt-4 text-base font-semibold text-[#26402e]">Mulai isi etalase tokomu</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Tambahkan produk pertama agar calon pembeli dapat menemukan karya lokalmu di katalog KREVA.</p>
                    <a href="{{ route('seller.products.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-[#26402e] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1e3325]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                        </svg>
                        Tambah produk pertama
                    </a>
                @else
                    <h3 class="mt-4 text-base font-semibold text-[#26402e]">Selamat datang di KREVA</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Akun penjualmu sudah aktif. Siapkan informasi toko dan usaha sebelum mulai menambahkan produk.</p>
                @endif
            </div>
        @else
            <div data-product-list class="divide-y divide-[#f3f0e9]">
                @foreach ($recentProducts as $product)
                    <article data-product-row="{{ $product->id }}" class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                        <div class="flex min-w-0 items-center gap-4">
                            @if ($product->image_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="Foto {{ $product->nama_produk }}" class="h-12 w-12 shrink-0 rounded-xl object-cover">
                            @else
                                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#f4f7f4] text-[#416743]">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5" />
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-stone-800">{{ $product->nama_produk }}</h3>
                                <p class="mt-1 truncate text-sm text-stone-500">{{ $product->kategori }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3 pl-16 sm:justify-end sm:pl-0">
                            <div class="sm:text-right">
                                <p class="font-semibold text-[#26402e]">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs {{ $product->stok > 0 ? 'text-emerald-700' : 'text-[#b65b2d]' }}">
                                    {{ $product->stok > 0 ? 'Stok ' . number_format($product->stok) : 'Stok habis' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('seller.products.edit', ['product' => $product->id]) }}" class="rounded-full border border-[#d9e3d9] px-4 py-2 text-xs font-semibold text-[#345236] transition hover:bg-[#f4f7f4]">Edit produk</a>
                                <form method="POST" action="{{ route('seller.products.destroy', ['product' => $product->id]) }}" data-delete-product>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <script>
        document.querySelectorAll('[data-delete-product]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (!window.confirm('Hapus produk ini?')) {
                    return;
                }

                const button = form.querySelector('button[type="submit"]');
                const message = document.getElementById('product-delete-message');
                button.disabled = true;
                button.textContent = 'Menghapus...';
                message.classList.add('hidden');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const result = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(result.message || 'Produk gagal dihapus. Silakan coba kembali.');
                    }

                    const row = form.closest('[data-product-row]');
                    const list = row.parentElement;
                    row.remove();
                    Object.entries(result.stats || {}).forEach(([name, value]) => {
                        const stat = document.querySelector(`[data-product-stat="${name}"]`);
                        if (stat) {
                            stat.textContent = new Intl.NumberFormat('id-ID').format(value);
                        }
                    });
                    if (list.matches('[data-product-list]') && list.children.length === 0) {
                        list.innerHTML = '<div class="px-5 py-12 text-center sm:px-8"><h3 class="text-base font-semibold text-[#26402e]">Belum ada produk terbaru</h3><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Produk berhasil dihapus dari etalase toko.</p></div>';
                    }
                } catch (error) {
                    message.textContent = error.message || 'Produk gagal dihapus. Silakan coba kembali.';
                    message.classList.remove('hidden');
                    button.disabled = false;
                    button.textContent = 'Hapus';
                }
            });
        });
    </script>
@endsection
