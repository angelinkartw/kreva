<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $databaseUnavailable = false;

    try {
        $featuredProducts = Product::query()
            ->with('seller')
            ->where('stok', '>', 0)
            ->latest()
            ->limit(4)
            ->get();
    } catch (QueryException $exception) {
        Log::warning('Homepage could not load featured products because the database is unavailable.', [
            'exception' => $exception::class,
        ]);

        $featuredProducts = collect();
        $databaseUnavailable = true;
    }

    return view('welcome', compact('featuredProducts', 'databaseUnavailable'));
})->name('home');

Route::get('/marketplace', [ProductController::class, 'index'])->name('marketplace.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'add'])->name('cart.items.store');
Route::put('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{product}', [CartController::class, 'remove'])->name('cart.items.remove');

Route::middleware(['auth', 'account.type:buyer'])->group(function () {
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.create');
    Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.store');
    Route::get('/buyer/orders', [CartController::class, 'orders'])->name('buyer.orders.index');
    Route::get('/buyer/orders/{order}', [CartController::class, 'showOrder'])->name('buyer.orders.show');
});

Route::middleware(['auth', 'account.type:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'buyerDashboard'])->name('dashboard');
});

Route::middleware(['auth', 'account.type:seller', 'non.employee'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'dashboard'])->name('dashboard');

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'manage'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'account.type:seller', 'verified', 'non.employee'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/enam-faktor', [DashboardController::class, 'enamFaktor'])
        ->name('dashboard.faktor');

    Route::get('/faktor/{id}', [DashboardController::class, 'detailFaktor'])
        ->name('dashboard.faktor-detail');

    Route::get('/rekomendasi', [DashboardController::class, 'rekomendasi'])
        ->name('dashboard.rekomendasi');

    Route::get('/tambah-umkm', [DashboardController::class, 'tambahUmkm'])
        ->name('dashboard.tambah-umkm');

    Route::post('/tambah-umkm', [DashboardController::class, 'simpanUmkm'])
        ->name('dashboard.simpan-umkm');

    Route::get('/manajemen-umkm', [DashboardController::class, 'manajemenUmkm'])->name('dashboard.manajemen-umkm');
    Route::get('/manajemen-karyawan/{id}', [DashboardController::class, 'manajemenKaryawan'])->name('dashboard.manajemen-karyawan');
    Route::get('/tambah-karyawan', [DashboardController::class, 'tambahKaryawan'])->name('dashboard.tambah-karyawan');
    Route::post('/tambah-karyawan', [DashboardController::class, 'simpanKaryawan'])->name('dashboard.simpan-karyawan');
});

Route::middleware(['auth', 'account.type:seller', 'verified'])->group(function () {
    Route::get('/asesmen', [DashboardController::class, 'asesmenOrganisasi'])->name('dashboard.asesmen');
    Route::post('/asesmen', [DashboardController::class, 'simpanAsesmen'])->name('dashboard.simpan-asesmen');
    Route::get('/asesmen/ringkasan-pdf', [DashboardController::class, 'unduhRingkasanAsesmenPdf'])->name('dashboard.asesmen-ringkasan-pdf');
});

Route::middleware(['auth', 'non.employee'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
