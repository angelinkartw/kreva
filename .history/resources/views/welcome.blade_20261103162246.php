<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KREVA - Discover Creativity, Support Local | Platform UMKM Indonesia</title>

    <!-- Meta SEO -->
    <meta name="description" content="Temukan dan dukung produk lokal terbaik dari UMKM Indonesia di KREVA! Dari kerajinan tangan, batik, makanan nusantara, hingga produk kreatif pilihan.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/kreva_logo.svg') }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN with complete custom config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        kreva: {
                            50: '#f4f7f4',
                            100: '#e6ede6',
                            200: '#cddbcd',
                            300: '#a6c1a7',
                            400: '#79a17b',
                            500: '#558257',
                            600: '#416743',
                            700: '#345236',
                            800: '#26402e', // Forest primary green
                            900: '#1e3325', // Dark green hover & footer
                            950: '#111d15',
                        },
                        warm: {
                            50: '#fdfbf7',
                            100: '#f9f6ef',
                            200: '#f5efe3',
                            300: '#eae0cc',
                            card: '#f8f5ee',
                            peach: '#f8ede1',
                        },
                        terracotta: {
                            DEFAULT: '#d96a28',
                            hover: '#c25819',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for interactive controls -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        [x-cloak] { display: none !important; }
        
        /* Subtle scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f7f4ee;
        }
        ::-webkit-scrollbar-thumb {
            background: #c7d2c8;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9ab09c;
        }
    </style>
</head>
<body class="bg-[#fbf9f4] text-stone-800 antialiased selection:bg-kreva-700 selection:text-white"
      x-data="{
          mobileMenuOpen: false,
          searchQuery: '',
          cartOpen: false,
          cartItems: [
              { id: 1, name: 'Batik Tulis Parang', price: 150000, img: '{{ asset('images/landing/product-batik.png') }}', qty: 1 },
              { id: 2, name: 'Cangkir Keramik Motif', price: 85000, img: '{{ asset('images/landing/product-cangkir.png') }}', qty: 1 }
          ],
          toastMsg: '',
          toastVisible: false,
          showToast(msg) {
              this.toastMsg = msg;
              this.toastVisible = true;
              setTimeout(() => { this.toastVisible = false }, 2500);
          },
          addToCart(name, price, img) {
              const existing = this.cartItems.find(item => item.name === name);
              if (existing) {
                  existing.qty++;
              } else {
                  this.cartItems.push({ id: Date.now(), name, price, img, qty: 1 });
              }
              this.showToast('Ditambahkan ke keranjang: ' + name);
          },
          get cartTotal() {
              return this.cartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
          },
          get cartCount() {
              return this.cartItems.reduce((sum, item) => sum + item.qty, 0);
          },
          formatRupiah(num) {
              return 'Rp ' + Number(num).toLocaleString('id-ID');
          }
      }">

    <!-- Toast Notification -->
    <div x-show="toastVisible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 bg-kreva-900 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 border border-kreva-700/50"
         x-cloak>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="text-sm font-medium" x-text="toastMsg"></span>
    </div>

    <!-- ================= NAVBAR ================= -->
    <header class="sticky top-0 z-40 bg-[#fbf9f4]/95 backdrop-blur-md border-b border-[#f0ebd9] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 sm:h-20 gap-4">
                
                <!-- Logo Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA Logo" class="h-8 sm:h-9 w-auto">
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium text-stone-700">
                    <a href="{{ url('/') }}" class="text-kreva-900 font-semibold border-b-2 border-kreva-800 pb-1 pt-1 transition">
                        Beranda
                    </a>

                    <!-- Dropdown Produk -->
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="flex items-center gap-1 hover:text-kreva-800 pb-1 pt-1 transition">
                            <span>Produk</span>
                            <svg class="w-4 h-4 text-stone-400 group-hover:text-kreva-800 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-stone-100 py-2 z-50"
                             x-cloak>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>👗</span> Fashion & Apparel
                            </a>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>🏺</span> Handmade & Crafts
                            </a>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>🍲</span> Food & Beverages
                            </a>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>💎</span> Accessories
                            </a>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>🏡</span> Home & Living
                            </a>
                            <a href="#kategori" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                <span>🎨</span> Art & Creative Products
                            </a>
                            <div class="border-t border-stone-100 my-1"></div>
                            <a href="#produk" class="flex items-center justify-between px-4 py-2 text-xs font-semibold text-kreva-800 hover:bg-[#f6f3ea]">
                                <span>Semua Produk</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <a href="#penjual" class="hover:text-kreva-800 pb-1 pt-1 transition">
                        Toko
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard.asesmen') }}" class="hover:text-kreva-800 pb-1 pt-1 transition">
                            Survey
                        </a>
                    @else
                        <a href="#survey" class="hover:text-kreva-800 pb-1 pt-1 transition">
                            Survey
                        </a>
                    @endauth

                    <a href="#tentang" class="hover:text-kreva-800 pb-1 pt-1 transition">
                        Tentang Kami
                    </a>
                </nav>

                <!-- Search, Cart & User Action Buttons -->
                <div class="flex items-center gap-3 sm:gap-4">
                    
                    <!-- Search Bar (Pill shape) -->
                    <div class="relative hidden sm:block w-48 md:w-56 lg:w-64">
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Cari produk lokal..."
                               class="w-full text-xs bg-[#f4efe4] text-stone-800 placeholder-stone-400 pl-4 pr-9 py-2.5 rounded-full border border-stone-200/60 focus:outline-none focus:border-kreva-600 focus:bg-white transition-all shadow-inner">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Cart Icon with Badge -->
                    <button @click="cartOpen = true"
                            class="relative p-2.5 rounded-full hover:bg-stone-200/50 text-stone-700 transition"
                            aria-label="Keranjang Belanja">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Badge -->
                        <span x-show="cartCount > 0"
                              x-text="cartCount"
                              class="absolute -top-0.5 -right-0.5 bg-[#c2410c] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-sm">
                        </span>
                    </button>

                    <!-- User Profile / Auth Links -->
                    @auth
                        <!-- Authenticated User Dropdown -->
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu"
                                    class="flex items-center gap-2 p-1.5 rounded-full hover:bg-stone-200/50 transition">
                                <div class="w-8 h-8 rounded-full bg-kreva-800 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </button>

                            <div x-show="userMenu"
                                 @click.outside="userMenu = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-52 rounded-2xl bg-white shadow-xl border border-stone-100 py-2 z-50"
                                 x-cloak>
                                <div class="px-4 py-2 border-b border-stone-100">
                                    <p class="text-xs font-semibold text-stone-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-stone-500 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard UMKM
                                </a>

                                <a href="{{ route('dashboard.asesmen') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-stone-700 hover:bg-[#f6f3ea] hover:text-kreva-800 transition">
                                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Asesmen & Survey
                                </a>

                                <div class="border-t border-stone-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest User Avatar / Login -->
                        <div class="relative" x-data="{ guestMenu: false }">
                            <button @click="guestMenu = !guestMenu"
                                    class="p-2 rounded-full hover:bg-stone-200/50 text-stone-700 transition"
                                    aria-label="Akun Pengguna">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>

                            <div x-show="guestMenu"
                                 @click.outside="guestMenu = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-xl border border-stone-100 p-2 z-50"
                                 x-cloak>
                                <a href="{{ route('login') }}" class="flex items-center justify-center w-full bg-kreva-800 hover:bg-kreva-900 text-white text-xs font-semibold py-2 px-3 rounded-xl transition">
                                    Masuk ke Akun
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="flex items-center justify-center w-full mt-1.5 text-xs font-semibold text-stone-700 hover:text-kreva-800 py-1.5 transition">
                                        Daftar Akun Baru
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Menu Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-xl text-stone-700 hover:bg-stone-200/50 transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-cloak />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Search Bar (under header on small screens) -->
            <div class="sm:hidden pb-3">
                <div class="relative w-full">
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Cari produk lokal..."
                           class="w-full text-xs bg-[#f4efe4] text-stone-800 placeholder-stone-400 pl-4 pr-9 py-2 rounded-full border border-stone-200 focus:outline-none focus:border-kreva-700">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu Dropdown -->
            <div x-show="mobileMenuOpen"
                 x-transition
                 class="md:hidden border-t border-stone-200 py-4 space-y-2 text-sm font-medium"
                 x-cloak>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-xl text-kreva-900 bg-[#ede6d5] font-semibold">Beranda</a>
                <a href="#kategori" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Kategori</a>
                <a href="#produk" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Produk Unggulan</a>
                <a href="#penjual" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Toko Terpilih</a>
                @auth
                    <a href="{{ route('dashboard.asesmen') }}" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Survey & Asesmen</a>
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Dashboard</a>
                @else
                    <a href="#survey" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Survey</a>
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-xl text-kreva-800 font-semibold hover:bg-stone-100">Masuk</a>
                @endauth
                <a href="#tentang" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-stone-700 hover:bg-stone-100">Tentang Kami</a>
            </div>

        </div>
    </header>


    <!-- ================= HERO SECTION ================= -->
    <section id="beranda" class="relative overflow-hidden pt-6 pb-12 md:pt-10 md:pb-16 lg:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                
                <!-- Left Hero Content -->
                <div class="lg:col-span-6 space-y-4 sm:space-y-6 text-left">
                    <h1 class="text-3xl sm:text-5xl lg:text-[3.4rem] font-extrabold text-kreva-900 tracking-tight leading-[1.15]">
                        Discover Creativity,<br>
                        Support Local.
                    </h1>

                    <p class="text-sm sm:text-base lg:text-lg text-stone-600 max-w-lg leading-relaxed font-normal">
                        Temukan dan dukung produk lokal terbaik dari UMKM Indonesia di KREVA!
                    </p>

                    <div class="pt-2">
                        <a href="#produk"
                           class="inline-flex items-center gap-2.5 bg-kreva-800 hover:bg-kreva-900 text-white font-medium text-sm sm:text-base px-6 sm:px-7 py-3 sm:py-3.5 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group">
                            <span>Belanja Sekarang</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Right Hero Artwork / Image Showcase -->
                <div class="lg:col-span-6 relative">
                    <!-- Artwork Container with Sage Blob Backdrop -->
                    <div class="relative w-full rounded-3xl sm:rounded-[36px] overflow-hidden bg-gradient-to-tr from-[#dce5dc] via-[#e5ede5] to-[#ece5d8] border border-[#d8e2d8] shadow-sm flex items-center justify-center p-3 sm:p-4">
                        
                        <!-- Background Monas & Motif Watermark Graphic -->
                        <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#243e2e_1px,transparent_1px)] [background-size:16px_16px]"></div>
                        
                        <!-- Hero Showcase Image -->
                        <div class="relative z-10 w-full overflow-hidden rounded-2xl">
                            <img src="{{ asset('images/landing/hero-composition.png') }}"
                                 alt="KREVA - Produk Lokal Indonesia: Anyaman, Batik, Keramik, dan Wayang"
                                 class="w-full h-auto object-cover object-center transform hover:scale-[1.02] transition-transform duration-500 rounded-2xl">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ================= SECTION 1: KATEGORI PILIHAN ================= -->
    <section id="kategori" class="py-8 sm:py-12 border-t border-[#f0ebd9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Title Header -->
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex items-center gap-2">
                    <!-- Leaf Sprout Icon -->
                    <svg class="w-5 h-5 text-kreva-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 3c0 0-8.5.5-13.5 5.5S2 22 2 22s8.5-.5 13.5-5.5S21 3 21 3z"/>
                        <path d="M8.5 15.5L15 9" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <h2 class="text-xl sm:text-2xl font-bold text-kreva-900">
                        Kategori Pilihan
                    </h2>
                </div>

                <a href="#produk" class="group inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-stone-700 hover:text-kreva-800 transition">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>

            <!-- 6 Category Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                
                <!-- 1. Fashion & Apparel -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3a2.5 2.5 0 0 1 2.5 2.5c0 1.2-.8 2-2 2.5L2.5 14.5A2 2 0 0 0 4 17.5h16a2 2 0 0 0 1.5-3L12.5 8" />
                            <line x1="4" y1="17.5" x2="20" y2="17.5" />
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Fashion & Apparel
                    </span>
                </a>

                <!-- 2. Handmade & Crafts -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 3h10v2H7z"/>
                            <path d="M8 5c-3 3-4 6-1 11 2 3.5 1 5 1 5h8s-1-1.5 1-5c3-5 2-8-1-11"/>
                            <path d="M6 11h12" stroke-dasharray="2 2"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Handmade & Crafts
                    </span>
                </a>

                <!-- 3. Food & Beverages -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2v8a3 3 0 0 1-3 3h-1m4-11v20" />
                            <path d="M6 2v7a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V2m-2 9v11M6 2h4" />
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Food & Beverages
                    </span>
                </a>

                <!-- 4. Accessories -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 3h12l4 6-10 12L2 9z"/>
                            <path d="M2 9h20M10 3l-2 6 4 12 4-12-2-6"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Accessories
                    </span>
                </a>

                <!-- 5. Home & Living -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 10.5L12 3l9 7.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20z" />
                            <path d="M9 21.5V12h6v9.5" />
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Home & Living
                    </span>
                </a>

                <!-- 6. Art & Creative Products -->
                <a href="#produk" class="group flex flex-col items-center justify-center p-4 sm:p-5 rounded-2xl bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 hover:shadow-md hover:-translate-y-1 transition-all duration-200 text-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[#937864] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/>
                            <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/>
                            <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>
                            <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/>
                            <path d="M12 2C6.5 2 2 6.5 2 12c0 5 4 8 9 8 1 0 1.5-.5 1.5-1.5 0-.5-.2-.9-.5-1.3-.3-.4-.5-.9-.5-1.5 0-1.1.9-2 2-2h2.4c3.6 0 6.6-3 6.6-6.7C22 5.8 17.5 2 12 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-[13px] font-semibold text-stone-800 group-hover:text-kreva-800 mt-2 leading-tight">
                        Art & Creative Products
                    </span>
                </a>

            </div>

        </div>
    </section>


    <!-- ================= SECTION 2: PRODUK UNGGULAN ================= -->
    <section id="produk" class="py-8 sm:py-12 border-t border-[#f0ebd9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Title Header -->
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-kreva-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 3c0 0-8.5.5-13.5 5.5S2 22 2 22s8.5-.5 13.5-5.5S21 3 21 3z"/>
                        <path d="M8.5 15.5L15 9" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <h2 class="text-xl sm:text-2xl font-bold text-kreva-900">
                        Produk Unggulan
                    </h2>
                </div>

                <a href="#produk" class="group inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-stone-700 hover:text-kreva-800 transition">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>

            <!-- 4 Product Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                
                <!-- Card 1: Batik Tulis Parang -->
                <div class="group bg-white rounded-2xl border border-[#ede7db] overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col"
                     x-show="!searchQuery || 'batik tulis parang'.includes(searchQuery.toLowerCase())">
                    
                    <!-- Product Image & Badge -->
                    <div class="relative aspect-[4/3] bg-stone-100 overflow-hidden">
                        <img src="{{ asset('images/landing/product-batik.png') }}"
                             alt="Batik Tulis Parang"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <!-- Badge Terlaris -->
                        <span class="absolute top-3 left-3 bg-[#3d7747] text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-md shadow-xs">
                            Terlaris
                        </span>
                    </div>

                    <!-- Product Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-stone-900 group-hover:text-kreva-800 transition line-clamp-1">
                                Batik Tulis Parang
                            </h3>
                            <p class="text-sm font-bold text-stone-900 mt-1">
                                Rp 150.000
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2">
                            <div class="flex items-center gap-1 text-xs text-stone-500 font-medium">
                                <span class="text-amber-500">★</span>
                                <span class="font-semibold text-stone-700">4.8</span>
                                <span>(1.2k terjual)</span>
                            </div>

                            <!-- Add to Cart Button -->
                            <button @click="addToCart('Batik Tulis Parang', 150000, '{{ asset('images/landing/product-batik.png') }}')"
                                    class="w-8 h-8 rounded-lg bg-[#ede7db] hover:bg-kreva-800 text-stone-700 hover:text-white flex items-center justify-center transition-colors duration-200"
                                    title="Tambah ke Keranjang">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Card 2: Tas Anyaman Rotan -->
                <div class="group bg-white rounded-2xl border border-[#ede7db] overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col"
                     x-show="!searchQuery || 'tas anyaman rotan'.includes(searchQuery.toLowerCase())">
                    
                    <!-- Product Image & Badge -->
                    <div class="relative aspect-[4/3] bg-stone-100 overflow-hidden">
                        <img src="{{ asset('images/landing/product-tas.png') }}"
                             alt="Tas Anyaman Rotan"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <!-- Badge Diskon 20% -->
                        <span class="absolute top-3 left-3 bg-[#d96a28] text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-md shadow-xs">
                            Diskon 20%
                        </span>
                    </div>

                    <!-- Product Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-stone-900 group-hover:text-kreva-800 transition line-clamp-1">
                                Tas Anyaman Rotan
                            </h3>
                            <div class="flex items-baseline gap-2 mt-1">
                                <p class="text-sm font-bold text-kreva-900">
                                    Rp 120.000
                                </p>
                                <p class="text-xs text-stone-400 line-through">
                                    Rp 150.000
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2">
                            <div class="flex items-center gap-1 text-xs text-stone-500 font-medium">
                                <span class="text-amber-500">★</span>
                                <span class="font-semibold text-stone-700">4.7</span>
                                <span>(856 terjual)</span>
                            </div>

                            <!-- Add to Cart Button -->
                            <button @click="addToCart('Tas Anyaman Rotan', 120000, '{{ asset('images/landing/product-tas.png') }}')"
                                    class="w-8 h-8 rounded-lg bg-[#ede7db] hover:bg-kreva-800 text-stone-700 hover:text-white flex items-center justify-center transition-colors duration-200"
                                    title="Tambah ke Keranjang">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Card 3: Keripik Tempe -->
                <div class="group bg-white rounded-2xl border border-[#ede7db] overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col"
                     x-show="!searchQuery || 'keripik tempe'.includes(searchQuery.toLowerCase())">
                    
                    <!-- Product Image -->
                    <div class="relative aspect-[4/3] bg-stone-100 overflow-hidden">
                        <img src="{{ asset('images/landing/product-keripik.png') }}"
                             alt="Keripik Tempe"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>

                    <!-- Product Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-stone-900 group-hover:text-kreva-800 transition line-clamp-1">
                                Keripik Tempe
                            </h3>
                            <p class="text-sm font-bold text-stone-900 mt-1">
                                Rp 25.000
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2">
                            <div class="flex items-center gap-1 text-xs text-stone-500 font-medium">
                                <span class="text-amber-500">★</span>
                                <span class="font-semibold text-stone-700">4.9</span>
                                <span>(2.4k terjual)</span>
                            </div>

                            <!-- Add to Cart Button -->
                            <button @click="addToCart('Keripik Tempe', 25000, '{{ asset('images/landing/product-keripik.png') }}')"
                                    class="w-8 h-8 rounded-lg bg-[#ede7db] hover:bg-kreva-800 text-stone-700 hover:text-white flex items-center justify-center transition-colors duration-200"
                                    title="Tambah ke Keranjang">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Card 4: Cangkir Keramik Motif -->
                <div class="group bg-white rounded-2xl border border-[#ede7db] overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col"
                     x-show="!searchQuery || 'cangkir keramik motif'.includes(searchQuery.toLowerCase())">
                    
                    <!-- Product Image -->
                    <div class="relative aspect-[4/3] bg-stone-100 overflow-hidden">
                        <img src="{{ asset('images/landing/product-cangkir.png') }}"
                             alt="Cangkir Keramik Motif"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>

                    <!-- Product Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-stone-900 group-hover:text-kreva-800 transition line-clamp-1">
                                Cangkir Keramik Motif
                            </h3>
                            <p class="text-sm font-bold text-stone-900 mt-1">
                                Rp 85.000
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2">
                            <div class="flex items-center gap-1 text-xs text-stone-500 font-medium">
                                <span class="text-amber-500">★</span>
                                <span class="font-semibold text-stone-700">4.8</span>
                                <span>(732 terjual)</span>
                            </div>

                            <!-- Add to Cart Button -->
                            <button @click="addToCart('Cangkir Keramik Motif', 85000, '{{ asset('images/landing/product-cangkir.png') }}')"
                                    class="w-8 h-8 rounded-lg bg-[#ede7db] hover:bg-kreva-800 text-stone-700 hover:text-white flex items-center justify-center transition-colors duration-200"
                                    title="Tambah ke Keranjang">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>


    <!-- ================= SECTION 3: BANNER DUKUNG UMKM & SURVEY ================= -->
    <section id="survey" class="py-8 sm:py-12 border-t border-[#f0ebd9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch">
                
                <!-- Left Banner: Dukung UMKM Lokal -->
                <div class="lg:col-span-7 bg-[#455f4a] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden flex flex-col justify-between shadow-sm">
                    
                    <div class="relative z-10 max-w-sm sm:max-w-md">
                        <!-- Storefront Icon -->
                        <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center mb-4 backdrop-blur-xs">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10M7 10V7a5 5 0 0110 0v3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6" />
                            </svg>
                        </div>

                        <!-- Banner Title -->
                        <h3 class="text-xl sm:text-2xl font-bold leading-snug">
                            Dukung UMKM Lokal,<br>
                            Bangun Ekonomi Bersama
                        </h3>

                        <!-- Banner Description -->
                        <p class="text-xs sm:text-sm text-stone-100/90 mt-2.5 leading-relaxed font-normal">
                            Bersama KREVA, mari dukung pelaku UMKM Indonesia agar semakin dikenal dan berkembang.
                        </p>

                        <!-- CTA Button -->
                        <div class="mt-6">
                            <a href="#penjual"
                               class="inline-flex items-center gap-2 bg-white text-stone-900 hover:bg-stone-100 font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-full shadow hover:shadow-md transition duration-200">
                                <span>Pelajari Lebih Lanjut</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Artisan Illustration/Photo inside banner -->
                    <div class="absolute right-0 bottom-0 top-0 w-2/5 sm:w-1/3 flex items-end justify-end pointer-events-none">
                        <img src="{{ asset('images/landing/banner-umkm.png') }}"
                             alt="Pelaku UMKM Indonesia"
                             class="h-full w-auto object-cover object-left-bottom">
                    </div>

                </div>

                <!-- Right Banner: Ikuti KREVA Survey! -->
                <div class="lg:col-span-5 bg-warm-peach border border-[#ebdccb] rounded-3xl p-6 sm:p-8 text-stone-900 relative overflow-hidden flex flex-col justify-between shadow-sm">
                    
                    <div class="relative z-10 max-w-xs">
                        <h3 class="text-xl sm:text-2xl font-bold text-stone-900 leading-snug">
                            Ikuti KREVA Survey!
                        </h3>

                        <p class="text-xs sm:text-sm text-stone-600 mt-2.5 leading-relaxed">
                            Suara kamu penting untuk pengembangan kami.
                        </p>

                        <!-- Button linked to /asesmen (or login for guests) -->
                        <div class="mt-6">
                            @auth
                                <a href="{{ route('dashboard.asesmen') }}"
                                   class="inline-flex items-center gap-2 bg-kreva-800 hover:bg-kreva-900 text-white font-medium text-xs sm:text-sm px-5 py-2.5 rounded-full shadow hover:shadow-md transition duration-200">
                                    <span>Isi Survey Sekarang</span>
                                    <span>&rarr;</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center gap-2 bg-kreva-800 hover:bg-kreva-900 text-white font-medium text-xs sm:text-sm px-5 py-2.5 rounded-full shadow hover:shadow-md transition duration-200">
                                    <span>Isi Survey Sekarang</span>
                                    <span>&rarr;</span>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Right Survey Clipboard graphic inside banner -->
                    <div class="absolute right-2 bottom-0 top-0 w-1/3 sm:w-2/5 flex items-end justify-end pointer-events-none">
                        <img src="{{ asset('images/landing/banner-survey.png') }}"
                             alt="KREVA Survey"
                             class="h-[88%] w-auto object-contain object-bottom-right">
                    </div>

                </div>

            </div>
        </div>
    </section>


    <!-- ================= SECTION 4: PENJUAL TERPILIH ================= -->
    <section id="penjual" class="py-8 sm:py-12 border-t border-[#f0ebd9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Title Header -->
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-kreva-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 3c0 0-8.5.5-13.5 5.5S2 22 2 22s8.5-.5 13.5-5.5S21 3 21 3z"/>
                        <path d="M8.5 15.5L15 9" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <h2 class="text-xl sm:text-2xl font-bold text-kreva-900">
                        Penjual Terpilih
                    </h2>
                </div>

                <a href="#penjual" class="group inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-stone-700 hover:text-kreva-800 transition">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>

            <!-- 4 Sellers Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Seller 1: Srikandi Craft -->
                <div class="bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                    <img src="{{ asset('images/landing/seller-srikandi.png') }}"
                         alt="Srikandi Craft"
                         class="w-11 h-11 rounded-full object-cover border border-[#e5d8c3] shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-stone-900 truncate">
                            Srikandi Craft
                        </h4>
                        <div class="flex items-center gap-1 text-xs text-stone-600 mt-0.5">
                            <span class="text-amber-500">★</span>
                            <span class="font-semibold text-stone-800">4.8</span>
                        </div>
                    </div>
                </div>

                <!-- Seller 2: Dapoer Nusantara -->
                <div class="bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                    <img src="{{ asset('images/landing/seller-dapoer.png') }}"
                         alt="Dapoer Nusantara"
                         class="w-11 h-11 rounded-full object-cover border border-[#e5d8c3] shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-stone-900 truncate">
                            Dapoer Nusantara
                        </h4>
                        <div class="flex items-center gap-1 text-xs text-stone-600 mt-0.5">
                            <span class="text-amber-500">★</span>
                            <span class="font-semibold text-stone-800">4.7</span>
                        </div>
                    </div>
                </div>

                <!-- Seller 3: Anyaman Kita -->
                <div class="bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                    <img src="{{ asset('images/landing/seller-anyaman.png') }}"
                         alt="Anyaman Kita"
                         class="w-11 h-11 rounded-full object-cover border border-[#e5d8c3] shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-stone-900 truncate">
                            Anyaman Kita
                        </h4>
                        <div class="flex items-center gap-1 text-xs text-stone-600 mt-0.5">
                            <span class="text-amber-500">★</span>
                            <span class="font-semibold text-stone-800">4.9</span>
                        </div>
                    </div>
                </div>

                <!-- Seller 4: Batik Lestari -->
                <div class="bg-warm-card hover:bg-white border border-[#ede5d6] hover:border-kreva-600/40 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                    <img src="{{ asset('images/landing/seller-lestari.png') }}"
                         alt="Batik Lestari"
                         class="w-11 h-11 rounded-full object-cover border border-[#e5d8c3] shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-stone-900 truncate">
                            Batik Lestari
                        </h4>
                        <div class="flex items-center gap-1 text-xs text-stone-600 mt-0.5">
                            <span class="text-amber-500">★</span>
                            <span class="font-semibold text-stone-800">4.8</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ================= SECTION 5: TENTANG KAMI ================= -->
    <section id="tentang" class="py-12 sm:py-16 bg-[#f7f2e7] border-t border-[#ede4d3]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-kreva-700 bg-kreva-100 px-3 py-1 rounded-full">Tentang KREVA</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-kreva-900 mt-3 leading-snug">
                        Menghubungkan Karya Lokal dengan Indonesia
                    </h3>
                    <p class="text-sm sm:text-base text-stone-600 mt-4 leading-relaxed">
                        KREVA hadir sebagai ekosistem digital untuk memajukan UMKM Indonesia. Kami memfasilitasi kurasi produk berkualitas, pendampingan asesmen kesiapan bisnis, serta akses pasar yang adil dan berkelanjutan bagi para perajin dan pelaku usaha lokal.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-semibold text-stone-700">100% Produk Asli UMKM</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-semibold text-stone-700">Pembayaran Terverifikasi & Aman</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-semibold text-stone-700">Pendampingan Asesmen Bisnis</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#e5dac5] shadow-sm">
                    <h4 class="text-base font-bold text-stone-900 mb-2">Ingin Mendaftarkan Usaha Anda?</h4>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-5">
                        Bergabunglah dengan ribuan pelaku usaha kreatif di seluruh Indonesia dan raih lebih banyak pelanggan bersama platform KREVA.
                    </p>
                    @auth
                        <a href="{{ route('dashboard.tambah-umkm') }}"
                           class="inline-flex items-center justify-center w-full bg-kreva-800 hover:bg-kreva-900 text-white font-semibold text-xs sm:text-sm py-3 rounded-full transition">
                            Tambah Data UMKM Anda &rarr;
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center w-full bg-kreva-800 hover:bg-kreva-900 text-white font-semibold text-xs sm:text-sm py-3 rounded-full transition">
                            Daftar Sebagai Penjual &rarr;
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>


    <!-- ================= FOOTER ================= -->
    <footer class="relative bg-[#263e2d] text-white overflow-hidden">
        
        <!-- Organic Wavy Curve Separator -->
        <div class="w-full overflow-hidden leading-none">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-8 sm:h-12 text-[#263e2d] fill-current">
                <path d="M0,0 C150,90 350,-40 500,40 C650,120 900,10 1200,50 L1200,120 L0,120 Z"></path>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-10 sm:pb-12">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10">
                
                <!-- Column 1: Brand & Slogan -->
                <div class="lg:col-span-3 space-y-3">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('images/kreva_logo_white.svg') }}" alt="KREVA Logo White" class="h-8 sm:h-9 w-auto">
                    </a>
                    <p class="text-xs sm:text-sm text-kreva-100/80 leading-relaxed">
                        Discover Creativity,<br>Support Local.
                    </p>
                </div>

                <!-- Column 2: Navigasi -->
                <div class="lg:col-span-2 space-y-2.5">
                    <h5 class="text-sm font-semibold text-white tracking-wide">Navigasi</h5>
                    <ul class="space-y-2 text-xs text-kreva-100/75 font-normal">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#produk" class="hover:text-white transition">Produk</a></li>
                        <li><a href="#penjual" class="hover:text-white transition">Toko</a></li>
                        <li><a href="#survey" class="hover:text-white transition">Survey</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- Column 3: Bantuan -->
                <div class="lg:col-span-2 space-y-2.5">
                    <h5 class="text-sm font-semibold text-white tracking-wide">Bantuan</h5>
                    <ul class="space-y-2 text-xs text-kreva-100/75 font-normal">
                        <li><a href="#tentang" class="hover:text-white transition">Pusat Bantuan</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Kontak Kami</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <!-- Column 4: Ikuti Kami (Social Media) -->
                <div class="lg:col-span-2 space-y-2.5">
                    <h5 class="text-sm font-semibold text-white tracking-wide">Ikuti Kami</h5>
                    <div class="flex items-center gap-2.5">
                        
                        <!-- Instagram -->
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="Instagram">
                            <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        <!-- YouTube -->
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="YouTube">
                            <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>

                        <!-- TikTok -->
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="TikTok">
                            <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>

                        <!-- Facebook -->
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="Facebook">
                            <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                    </div>
                </div>

                <!-- Column 5: Newsletter Input -->
                <div class="lg:col-span-3 space-y-2.5">
                    <h5 class="text-xs sm:text-sm font-medium text-kreva-100/90 leading-tight">
                        Dapatkan info terbaru dari KREVA!
                    </h5>
                    
                    <form @submit.prevent="showToast('Terima kasih! Email Anda telah terdaftar.'); $el.reset();"
                          class="flex items-center bg-white rounded-full p-1 pl-4 shadow-sm max-w-sm">
                        <input type="email"
                               required
                               placeholder="Email kamu"
                               class="w-full text-xs text-stone-800 placeholder-stone-400 bg-transparent focus:outline-none pr-2">
                        <button type="submit"
                                class="w-8 h-8 rounded-full bg-kreva-900 hover:bg-kreva-950 text-white flex items-center justify-center transition shrink-0"
                                aria-label="Kirim Email">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>
                </div>

            </div>

            <!-- Bottom Divider, Copyright & Batik Motif Watermark -->
            <div class="border-t border-white/10 pt-6 mt-8 sm:mt-10 flex flex-col sm:flex-row items-center justify-between text-xs text-kreva-100/60 relative">
                <p>&copy; 2025 KREVA. Semua hak dilindungi.</p>

                <!-- Batik Watermark in corner -->
                <div class="hidden sm:block absolute right-0 -bottom-6 opacity-25 pointer-events-none">
                    <img src="{{ asset('images/landing/footer-watermark.png') }}"
                         alt="Motif Batik Tradisional"
                         class="h-16 w-auto object-contain">
                </div>
            </div>

        </div>
    </footer>


    <!-- ================= SHOPPING CART DRAWER / MODAL ================= -->
    <div x-show="cartOpen"
         class="fixed inset-0 z-50 overflow-hidden"
         x-cloak>
        
        <!-- Backdrop -->
        <div x-show="cartOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="cartOpen = false"
             class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs"></div>

        <!-- Sliding Panel -->
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="cartOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                
                <!-- Drawer Header -->
                <div class="p-5 border-b border-stone-100 flex items-center justify-between bg-[#fbf9f4]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-kreva-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="font-bold text-stone-900 text-base">Keranjang Belanja (<span x-text="cartCount"></span>)</h3>
                    </div>
                    <button @click="cartOpen = false" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Items List -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <template x-for="(item, idx) in cartItems" :key="item.id">
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-[#faf7f0] border border-[#ede7d8]">
                            <img :src="item.img" :alt="item.name" class="w-16 h-14 object-cover rounded-xl bg-white border border-stone-200/60">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-stone-900 truncate" x-text="item.name"></h4>
                                <p class="text-xs font-semibold text-kreva-800 mt-0.5" x-text="formatRupiah(item.price)"></p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button @click="item.qty > 1 ? item.qty-- : cartItems.splice(idx, 1)" class="w-5 h-5 rounded bg-white border border-stone-300 text-stone-600 flex items-center justify-center text-xs hover:bg-stone-100">-</button>
                                    <span class="text-xs font-bold text-stone-800" x-text="item.qty"></span>
                                    <button @click="item.qty++" class="w-5 h-5 rounded bg-white border border-stone-300 text-stone-600 flex items-center justify-center text-xs hover:bg-stone-100">+</button>
                                </div>
                            </div>
                            <button @click="cartItems.splice(idx, 1)" class="text-stone-400 hover:text-red-500 p-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="cartItems.length === 0" class="py-12 text-center text-stone-500 text-sm">
                        Keranjang belanja Anda masih kosong.
                    </div>
                </div>

                <!-- Drawer Footer -->
                <div class="p-5 border-t border-stone-100 bg-[#fbf9f4] space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-stone-600">Subtotal</span>
                        <span class="font-extrabold text-stone-900 text-base" x-text="formatRupiah(cartTotal)"></span>
                    </div>
                    <button @click="showToast('Pesanan siap diproses!'); cartOpen = false"
                            class="w-full bg-kreva-800 hover:bg-kreva-900 text-white font-bold py-3 rounded-full text-sm shadow hover:shadow-md transition">
                        Checkout Sekarang &rarr;
                    </button>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
