<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified', 'non.employee'])->group(function () {
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

Route::middleware(['auth', 'verified'])->group(function () {
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
