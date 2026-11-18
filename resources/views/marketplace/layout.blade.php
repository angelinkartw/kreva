<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marketplace UMKM') - KREVA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: Poppins, sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#fbf9f4] text-stone-800">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ auth()->check() && auth()->user()->account_type === 'seller' ? route('seller.dashboard') : route('marketplace.index') }}" class="flex shrink-0 items-center">
                <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA" class="h-8 w-auto">
            </a>
            <nav class="flex flex-wrap items-center gap-3 text-sm font-medium sm:gap-5">
                @auth
                    @if (auth()->user()->account_type === 'buyer')
                        <a href="{{ route('marketplace.index') }}" class="text-stone-700 transition hover:text-[#26402e]">Katalog UMKM</a>
                        <a href="{{ route('cart.index') }}" class="text-stone-700 transition hover:text-[#26402e]">Keranjang ({{ auth()->user()->cart?->items()->sum('qty') ?? 0 }})</a>
                        <a href="{{ route('buyer.dashboard') }}" class="text-stone-700 hover:text-[#26402e]">Dashboard Pembeli</a>
                        <a href="{{ route('buyer.orders.index') }}" class="text-stone-700 hover:text-[#26402e]">Pesanan Saya</a>
                    @elseif (auth()->user()->account_type === 'seller' && auth()->user()->role !== 'employee')
                        <a href="{{ route('seller.dashboard') }}" class="text-stone-700 transition hover:text-[#26402e]">Beranda Penjual</a>
                        <a href="{{ route('marketplace.index') }}" class="text-stone-700 transition hover:text-[#26402e]">Lihat Katalog</a>
                    @endif
                    <span class="hidden text-stone-600 sm:inline">{{ auth()->user()->nama_user }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-stone-300 px-4 py-2 text-xs transition hover:border-[#cddbcd] hover:bg-[#f4f7f4] sm:text-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('marketplace.index') }}" class="text-stone-700 transition hover:text-[#26402e]">Katalog UMKM</a>
                    <a href="{{ route('login') }}" class="text-stone-700 transition hover:text-[#26402e]">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#26402e] px-4 py-2 text-white transition hover:bg-[#1e3325]">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
