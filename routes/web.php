<?php
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ProfilAdminController;

// ============================================================
// ROUTE PUBLIK — siapa saja bisa akses
// ============================================================

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }
    return app(\App\Http\Controllers\User\DashboardController::class)->index();
});

Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/produk/{id}', [DetailProdukController::class, 'show'])->name('produk.detail');

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

    // Halaman Detail Order Sukses (Dimasukkan ke sini agar aman terlindungi login)
    Route::get('/order-sukses/{id}', function ($id) {
        $pesanan = DB::table('pembelian')
            ->where('id_pembelian', $id)
            ->first();

        $detailPesanan = DB::table('pembelian_detail as pd')
            ->join('produk_varian as pv', 'pd.id_varian', '=', 'pv.id_varian')
            ->join('produk as p', 'pv.id_produk', '=', 'p.id_produk')
            ->where('pd.id_pembelian', $id)
            ->select(
                'p.nama_produk',
                'p.foto_produk',
                'pv.nama_varian',
                'pd.jumlah',
                'pd.harga_satuan',
                'pd.subtotal'
            )
            ->get();

        return view('user.ordersukses', compact('pesanan', 'detailPesanan'));
    })->name('order.sukses');

});

// ============================================================
// ADMIN ROUTES — harus login + role admin
// ============================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard',              [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/produk/{id}/hapus',      [AdminDashboard::class, 'hapus'])->name('produk.hapus');

    Route::get('/produk/tambah',          [ProdukController::class, 'create'])->name('produk.tambah');
    Route::post('/produk/tambah',         [ProdukController::class, 'store'])->name('produk.store');

    Route::get('/produk/{id}/edit',       [ProdukController::class, 'edit'])->name('produk.edit');
    Route::post('/produk/{id}/edit',      [ProdukController::class, 'update'])->name('produk.update');

    Route::delete('/produk/{id}/hapus',   [AdminDashboard::class, 'hapus'])->name('produk.hapus.delete');

    Route::get('/profil',                 [ProfilAdminController::class, 'index'])->name('profil');
});