<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// User
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\KatalogController;
use App\Http\Controllers\User\DetailProdukController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\RiwayatController;
use App\Http\Controllers\User\ProfilUserController;

// Admin
use App\Http\Controllers\Admin\DashboardController   as AdminDashboard;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ProfilAdminController;

// ============================================================
// ROUTE PUBLIK — siapa saja bisa akses
// ============================================================

// Home / dashboard public
// Home
Route::get('/', function () {

    // Kalau sudah login
    if (Auth::check()) {

        // Kalau admin → dashboard admin
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Kalau user biasa → dashboard user
        return redirect()->route('home');
    }

    // Guest tetap lihat dashboard user/public
    return app(\App\Http\Controllers\User\DashboardController::class)->index();
});

// Dashboard user/public
Route::get('/home', [DashboardController::class, 'index'])
    ->name('home');
    
// Katalog produk
Route::get('/katalog', [KatalogController::class, 'index'])
    ->name('katalog');

// Detail produk
Route::get('/produk/{id}', [DetailProdukController::class, 'show'])
    ->name('produk.detail');


// ============================================================
// AUTH ROUTES
// ============================================================

Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',   [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// ============================================================
// USER ROUTES — harus login + role user
// ============================================================

Route::middleware(['auth', 'role:user'])->group(function () {

    // Keranjang
    Route::get('/keranjang',          [KeranjangController::class, 'index'])->name('keranjang');
    Route::post('/keranjang/tambah',  [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
    Route::post('/keranjang/update',  [KeranjangController::class, 'updateQty'])->name('keranjang.update');
    Route::post('/keranjang/hapus',   [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

    // Checkout
    Route::match(['get', 'post'], '/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/proses',   [CheckoutController::class, 'proses'])->name('checkout.proses');

    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    // Profil user
    Route::get('/profil', [ProfilUserController::class, 'index'])->name('profil.user');
});

// ============================================================
// ADMIN ROUTES — harus login + role admin
// ============================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard (termasuk hapus produk via GET)
    Route::get('/dashboard',              [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/produk/{id}/hapus',      [AdminDashboard::class, 'hapus'])->name('produk.hapus');

    // Tambah produk
    Route::get('/produk/tambah',          [ProdukController::class, 'create'])->name('produk.tambah');
    Route::post('/produk/tambah',         [ProdukController::class, 'store'])->name('produk.store');

    // Edit produk
    Route::get('/produk/{id}/edit',       [ProdukController::class, 'edit'])->name('produk.edit');
    Route::post('/produk/{id}/edit',      [ProdukController::class, 'update'])->name('produk.update');

    Route::delete('/produk/{id}/hapus',
    [AdminDashboard::class, 'hapus'])
    ->name('produk.hapus');

    // Profil admin
    Route::get('/profil',                 [ProfilAdminController::class, 'index'])->name('profil');
});