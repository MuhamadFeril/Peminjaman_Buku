<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

// Halaman Utama
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard.index') : view('home');
});

// Authentication
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('logout', [AuthController::class, 'logout'])->name('logout.get'); // Shortcut biar gak pusing

// Public Routes (Bisa dilihat tanpa login)
Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
// Batasi {id_buku} ke pola angka atau UUID agar route statis seperti 'create' tidak tertangkap
Route::get('buku/{id_buku}', [BukuController::class, 'show'])
    ->where('id_buku', '[0-9a-fA-F\-]+')
    ->name('buku.show');

// Protected Routes (Harus Login)
Route::middleware('auth')->group(function () {
    
    // Dashboard & Profile
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

    // ANGGOTA (Pusat Perbaikan)
    // 1. Jalur Admin (Hanya Admin)
    Route::middleware('web_permission:anggota.manage')->group(function () {
        Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
        Route::get('anggota/{id}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
        Route::put('anggota/{id}', [AnggotaController::class, 'update'])->name('anggota.update');
        Route::delete('anggota/{id}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    });
    // 2. Jalur User Biasa (Buat Kartu Sendiri) - INI BIAR GAK 404
    Route::get('anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('anggota/create-self-form', [AnggotaController::class, 'createSelfForm'])->name('anggota.createSelfForm');
    Route::post('anggota/store-self', [AnggotaController::class, 'storeSelf'])->name('anggota.storeSelf');
    // User-facing edit for their own anggota record
    Route::get('anggota/self/edit', [AnggotaController::class, 'editSelf'])->name('anggota.editSelf');
    Route::put('anggota/self', [AnggotaController::class, 'updateSelf'])->name('anggota.updateSelf');

    // BUKU (Admin Only)
    Route::middleware('web_permission:buku.manage')->group(function () {
        Route::get('buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('buku/store', [BukuController::class, 'store'])->name('buku.store');
        Route::get('buku/{id_buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('buku/{id_buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('buku/{id_buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });
        // SINOPSIS (Admin only): tambah/edit/update sinopsis untuk setiap buku
        Route::get('buku/{id_buku}/sinopsis/create', [BukuController::class, 'createSinopsis'])->name('buku.sinopsis.create');
        Route::post('buku/{id_buku}/sinopsis', [BukuController::class, 'storeSinopsis'])->name('buku.sinopsis.store');
        Route::get('buku/{id_buku}/sinopsis/edit', [BukuController::class, 'editSinopsis'])->name('buku.sinopsis.edit');
        Route::put('buku/{id_buku}/sinopsis', [BukuController::class, 'updateSinopsis'])->name('buku.sinopsis.update');

    // PEMINJAMAN
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    // Admin-only management routes for peminjaman (edit/update/delete)
    Route::middleware('web_permission:peminjaman.manage')->group(function () {
        Route::get('peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
        Route::put('peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::delete('peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    });
});