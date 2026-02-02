<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
 use App\Http\Controllers\ProfileController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Authentication
use App\Http\Controllers\AuthController;

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Resource routes for CRUD (protected)
Route::middleware('auth')->group(function () {
    // Buku routes - GET untuk semua, CRUD hanya admin
    Route::get('buku', [BukuController::class, 'index'])->middleware('web_permission:buku.view')->name('buku.index');
    Route::get('buku/create', [BukuController::class, 'create'])->middleware('web_permission:buku.manage')->name('buku.create');
    Route::post('buku', [BukuController::class, 'store'])->middleware('web_permission:buku.manage')->name('buku.store');
    Route::get('buku/{id_buku}', [BukuController::class, 'show'])->middleware('web_permission:buku.view')->name('buku.show');
    Route::get('buku/{id_buku}/edit', [BukuController::class, 'edit'])->middleware('web_permission:buku.manage')->name('buku.edit');
    Route::put('buku/{id_buku}', [BukuController::class, 'update'])->middleware('web_permission:buku.manage')->name('buku.update');
    Route::delete('buku/{id_buku}', [BukuController::class, 'destroy'])->middleware('web_permission:buku.manage')->name('buku.destroy');

    // Anggota routes - hanya admin bisa akses
    Route::middleware('web_permission:anggota.manage')->group(function () {
        Route::resource('anggota', AnggotaController::class);
    });

    // Peminjaman routes - GET untuk semua, POST/PUT/DELETE untuk semua (dengan validasi di controller)
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->middleware('web_permission:peminjaman.view')->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->middleware('web_permission:peminjaman.manage')->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->middleware('web_permission:peminjaman.manage')->name('peminjaman.store');
    Route::get('peminjaman/{id_peminjaman}', [PeminjamanController::class, 'show'])->middleware('web_permission:peminjaman.view')->name('peminjaman.show');
    Route::get('peminjaman/{id_peminjaman}/edit', [PeminjamanController::class, 'edit'])->middleware('web_permission:peminjaman.manage')->name('peminjaman.edit');
    Route::put('peminjaman/{id_peminjaman}', [PeminjamanController::class, 'update'])->middleware('web_permission:peminjaman.manage')->name('peminjaman.update');
    Route::delete('peminjaman/{id_peminjaman}', [PeminjamanController::class, 'destroy'])->middleware('web_permission:peminjaman.manage')->name('peminjaman.destroy');

    // Convenience GET logout route (not recommended for production CSRF reasons)
    Route::get('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout.get');
    // Dashboard - single route
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profile routes
   
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
});