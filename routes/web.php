<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\KatalogController;
use App\Http\Controllers\User\DetailProdukController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\RiwayatController;
use App\Http\Controllers\User\ProfilUserController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ProfilAdminController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\LaporanController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }

    return app(DashboardController::class)->index();
});

Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/produk/{id}', [DetailProdukController::class, 'show'])->name('produk.detail');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::post('/keranjang/tambah', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
    Route::post('/keranjang/update', [KeranjangController::class, 'updateQty'])->name('keranjang.update');
    Route::post('/keranjang/hapus', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

    Route::match(['get','post'], '/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/proses', [CheckoutController::class, 'proses'])->name('checkout.proses');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/profil', [ProfilUserController::class, 'index'])->name('profil.user');
    Route::get('/order-sukses/{id}', [CheckoutController::class, 'orderSukses'])->name('order.sukses');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (FIXED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ================= DASHBOARD =================
    Route::get('/dashboard', [AdminDashboard::class, 'index'])
        ->name('dashboard');

    // ================= PRODUK =================
    Route::get('/produk/tambah', [ProdukController::class, 'create'])
        ->name('produk.tambah');

    Route::post('/produk/tambah', [ProdukController::class, 'store'])
        ->name('produk.store');

    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])
        ->name('produk.edit');

    Route::put('/produk/{id}', [ProdukController::class, 'update'])
        ->name('produk.update');

    Route::delete('/produk/{id}', [AdminDashboard::class, 'hapus'])
        ->name('produk.hapus');

    Route::get('/pesanan', [PesananController::class, 'index'])
        ->name('pesanan');

    Route::post('/pesanan/{id}', [PesananController::class, 'update'])
        ->name('pesanan.update');

    // ================= PROFIL =================
    Route::get('/profil', [ProfilAdminController::class, 'index'])
        ->name('profil');

    // ================= L A P O R A N =================
    Route::prefix('laporan')->name('laporan.')->group(function () {

        Route::get('/', [LaporanController::class, 'index'])
            ->name('index');

        Route::get('/export/excel', [LaporanController::class, 'exportExcel'])
            ->name('export.excel');

        Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])
            ->name('export.pdf');

        Route::post('/import', [LaporanController::class, 'import'])
            ->name('import');
    });
});