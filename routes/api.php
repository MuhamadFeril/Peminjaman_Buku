<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnggotaController;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\AuthController;


// Route Publik
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// routes/api.php
Route::get('/test-queue', function () {
    App\Jobs\TestQueueJob::dispatch();
    return "Tugas masuk antrean!";
});
Route::get('/health-check', function () {
    try {
        return response()->json([
            'status' => 'Healthy',
            'database_connection' => 'Connected',
            'cache_driver' => config('cache.default')
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'Unhealthy', 'error' => $e->getMessage()], 500);
    }
});
// Route Terproteksi
Route::middleware('auth:api')->name('api.')->group(function () {

    // 1. Fitur Khusus Admin
    Route::middleware('admin')->name('api.')->group(function () {
        Route::apiResource('anggota', AnggotaController::class);
    });

    // 2. Fitur Buku & Peminjaman
    // Gunakan apiResource TANPA tambahan route manual di luar grup ini
    Route::apiResource('buku', BukuController::class);
    Route::apiResource('peminjaman', PeminjamanController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->get('/user-check', function (Request $request) {
    return $request->user();
});