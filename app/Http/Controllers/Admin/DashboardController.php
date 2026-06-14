<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityHelper;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Total produk
        $totalProduk = DB::table('produk')->count();

        // Stok tersedia (total stok varian > 0)
        $stokTersedia = DB::table('produk as p')
            ->leftJoin('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
            ->select('p.id_produk', DB::raw('COALESCE(SUM(v.stok), 0) as total_stok'))
            ->groupBy('p.id_produk')
            ->havingRaw('COALESCE(SUM(v.stok), 0) > 0')
            ->get()->count();

        // Stok habis (total stok varian = 0)
        $stokHabis = DB::table('produk as p')
            ->leftJoin('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
            ->select('p.id_produk', DB::raw('COALESCE(SUM(v.stok), 0) as total_stok'))
            ->groupBy('p.id_produk')
            ->havingRaw('COALESCE(SUM(v.stok), 0) = 0')
            ->get()->count();

        // Search produk
        $keyword = $request->input('search', '');

        $produk = DB::table('produk as p')
            ->leftJoin('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
            ->leftJoin('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
            ->select(
                'p.id_produk', 'p.nama_produk', 'p.foto_produk',
                'k.nama_kategori',
                DB::raw('MIN(v.harga) as harga'),
                DB::raw('COALESCE(SUM(v.stok), 0) as stok'),
                DB::raw("GROUP_CONCAT(CONCAT(v.nama_varian, ':', v.stok) ORDER BY v.id_varian ASC SEPARATOR ' • ') as detail_varian")
            )
            ->where('p.nama_produk', 'like', "%{$keyword}%")
            ->groupBy('p.id_produk', 'p.nama_produk', 'p.foto_produk', 'k.nama_kategori')
            ->orderBy('p.id_produk')
            ->get();

        $aktivitasTerbaru = ActivityLog::leftJoin(
                'users',
                'activity_logs.user_id',
                '=',
                'users.id'
            )
            ->select(
                'activity_logs.*',
                'users.nama_lengkap'
            )
            ->latest()
            ->limit(10)
            ->get();
        return view('admin.dashboard', compact(
            'totalProduk',
            'stokTersedia',
            'stokHabis',
            'produk',
            'keyword',
            'aktivitasTerbaru'
        ));
    }

    public function hapus(Request $request, $id)
{
    $keyword = $request->input('search', '');
    $id = (int) $id;

    // 1. Cek apakah produk ini ada di dalam transaksi yang MASIH AKTIF (Belum Selesai & Belum Batal)
    // Jika statusnya 'Pending', 'Diproses', dll., maka hitungannya akan > 0 (Tolak Hapus)
    $totalTransaksiAktif = DB::table('pembelian_detail as pd')
        ->join('produk_varian as pv', 'pd.id_varian', '=', 'pv.id_varian')
        ->join('pembelian as p', 'pd.id_pembelian', '=', 'p.id_pembelian')
        ->where('pv.id_produk', $id)
        ->whereNotIn('p.status_pesanan', ['Selesai', 'Dibatalkan']) // 💡 Transaksi Selesai/Batal DIABAIKAN (Bisa Dihapus)
        ->count();

    $namaProduk = DB::table('produk')->where('id_produk', $id)->value('nama_produk') ?? 'Produk';

    // 2. JIKA ada transaksi yang masih berjalan/aktif, BARU kita tolak penghapusannya
    if ($totalTransaksiAktif > 0) {
        return redirect()->route('admin.dashboard', ['search' => $keyword])
            ->with('error_hapus', "Produk \"{$namaProduk}\" tidak bisa dihapus karena masih ada dalam transaksi aktif user yang belum selesai.");
    }

    // 3. JIKA HANYA ADA transaksi yang Selesai/Batal, proses hapus di bawah ini akan dijalankan
    DB::transaction(function () use ($id) {
        // Ambil semua id_varian yang dimiliki oleh produk ini
        $idVarians = DB::table('produk_varian')->where('id_produk', $id)->pluck('id_varian')->toArray();

        if (!empty($idVarians)) {
            // A. Bersihkan produk dari keranjang belanja user jika ada yang menyimpannya
            DB::table('keranjang_detail')->whereIn('id_varian', $idVarians)->delete();

            // B. 🛠️ PUTUS HUBUNGAN FOREIGN KEY: Ubah id_varian di riwayat pembelian (Selesai/Batal) menjadi NULL
            // Cara ini membuat data produk bisa dihapus tanpa merusak/menghapus nota riwayat milik user
            DB::table('pembelian_detail')
                ->whereIn('id_varian', $idVarians)
                ->update(['id_varian' => null]);
        }

        // C. Hapus data varian produk
        DB::table('produk_varian')->where('id_produk', $id)->delete();

        // D. Hapus produk utama
        DB::table('produk')->where('id_produk', $id)->delete();
    });

    ActivityHelper::log(
        'Hapus Produk',
        'Menghapus produk: ' . $namaProduk
    );

    return redirect()->route('admin.dashboard', ['search' => $keyword])
        ->with('success_hapus', $namaProduk);
}
}