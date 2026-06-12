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
                'users.id_user'
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

        // Cek apakah produk sudah pernah dipakai di transaksi
        $totalDipakai = DB::table('pembelian_detail as pd')
            ->join('produk_varian as pv', 'pd.id_varian', '=', 'pv.id_varian')
            ->where('pv.id_produk', $id)
            ->count();

        $namaProduk = DB::table('produk')->where('id_produk', $id)->value('nama_produk') ?? 'Produk';

        if ($totalDipakai > 0) {
            return redirect()->route('admin.dashboard', ['search' => $keyword])
                ->with('error_hapus', "Produk \"{$namaProduk}\" tidak bisa dihapus karena sudah pernah masuk transaksi.");
        }

        DB::table('produk')->where('id_produk', $id)->delete();

        ActivityHelper::log(
            'Hapus Produk',
            'Menghapus produk: ' . $namaProduk
        );

        return redirect()->route('admin.dashboard', ['search' => $keyword])
            ->with('success_hapus', $namaProduk);
    }
}