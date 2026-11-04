<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UmkmController;
use App\Http\Controllers\Api\AssessmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ==========================================
// Publik (Tanpa Token)
// ==========================================
Route::get('/', function () {
    return view('api-docs');
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Koneksi API SiSehat berhasil!',
        'environment' => app()->environment(),
        'timestamp' => now()->toIso8601String()
    ]);
});

// ==========================================
// Terproteksi (Wajib Token Bearer Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    // Autentikasi & User
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Manajemen UMKM
    Route::get('/umkm', [UmkmController::class, 'index']);
    Route::post('/umkm', [UmkmController::class, 'store']);
    Route::get('/umkm/karyawan', [UmkmController::class, 'getKaryawan']);
    Route::post('/umkm/karyawan', [UmkmController::class, 'storeKaryawan']);

    // Asesmen & Dashboard
    Route::get('/asesmen', [AssessmentController::class, 'getQuestions']);
    Route::post('/asesmen', [AssessmentController::class, 'submitAnswers']);
    Route::get('/asesmen/dashboard', [AssessmentController::class, 'getDashboard']);
});
