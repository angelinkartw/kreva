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
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('marketplace.index') }}" class="text-xl font-bold tracking-tight text-[#26402e]">KREVA</a>
            <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                <a href="{{ route('marketplace.index') }}" class="text-stone-700 hover:text-[#26402e]">Katalog UMKM</a>
                <a href="{{ route('cart.index') }}" class="text-stone-700 hover:text-[#26402e]">Keranjang ({{ collect(session('cart', []))->sum() }})</a>
                @auth
                    @if (auth()->user()->account_type === 'buyer')
                        <a href="{{ route('buyer.dashboard') }}" class="text-stone-700 hover:text-[#26402e]">Dashboard Pembeli</a>
                        <a href="{{ route('buyer.orders.index') }}" class="text-stone-700 hover:text-[#26402e]">Pesanan Saya</a>
                    @elseif (auth()->user()->account_type === 'seller' && auth()->user()->role !== 'employee')
                        <a href="{{ route('seller.dashboard') }}" class="text-stone-700 hover:text-[#26402e]">Dashboard Penjual</a>
                        <a href="{{ route('seller.products.index') }}" class="text-stone-700 hover:text-[#26402e]">Kelola Produk</a>
                    @endif
                    <span class="text-stone-600">{{ auth()->user()->nama_user }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-stone-300 px-4 py-2 hover:bg-stone-50">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-stone-700 hover:text-[#26402e]">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#26402e] px-4 py-2 text-white hover:bg-[#1e3325]">Daftar</a>
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
