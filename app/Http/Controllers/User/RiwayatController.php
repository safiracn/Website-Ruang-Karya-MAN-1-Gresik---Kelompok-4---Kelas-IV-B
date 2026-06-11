<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::id();
        $tab     = $request->input('tab', 'sedang-dipesan');

        $query = DB::table('pembelian as p')
            ->join('pembelian_detail as pd', 'p.id_pembelian', '=', 'pd.id_pembelian')
            ->join('produk_varian as pv', 'pd.id_varian', '=', 'pv.id_varian')
            ->join('produk as pr', 'pv.id_produk', '=', 'pr.id_produk')
            ->where('p.id_user', $id_user)
            ->select('p.*', 'pr.nama_produk', 'pr.foto_produk', 'pr.id_produk', 'pd.subtotal');

        if ($tab === 'selesai') {
            $query->where('p.status_pesanan', 'Selesai');
        } else {
            $query->whereNotIn('p.status_pesanan', ['Selesai', 'Dibatalkan']);
        }

<<<<<<< HEAD
        $pesanan = $query->orderByDesc('p.created_at')->get();
=======
        $pesanan = $query->orderByDesc('p.tgl_pembelian')->get();
>>>>>>> shava

        return view('user.riwayat', compact('pesanan', 'tab'));
    }
}