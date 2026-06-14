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

    $tab = $request->input('tab', 'sedang-dipesan');

    $query = DB::table('pembelian as p')
        ->join('pembelian_detail as pd', 'p.id_pembelian', '=', 'pd.id_pembelian')
        ->join('produk_varian as pv', 'pd.id_varian', '=', 'pv.id_varian')
        ->join('produk as pr', 'pv.id_produk', '=', 'pr.id_produk')
        ->where('p.id_user', $id_user)
        ->select(
            'p.*',
            'pr.id_produk',
            'pr.nama_produk',
            'pr.foto_produk',
            'pd.subtotal'
        );

    // TAB SELESAI
    if ($tab === 'selesai') {

    $query->whereIn(
        'p.status_pesanan',
        ['Selesai', 'Dibatalkan']
    );

} else {

    $query->whereNotIn(
        'p.status_pesanan',
        ['Selesai', 'Dibatalkan']
    );

}

    $pesanan = $query
        ->orderByDesc('p.id_pembelian')
        ->get();

    return view(
        'user.riwayat',
        compact('pesanan', 'tab')
    );
}

    public function batal($id)
    {
  
    $pesanan = DB::table('pembelian')
        ->where('id_pembelian', $id)
        ->first();

    DB::table('pembelian')
        ->where('id_pembelian', $id)
        ->where('status_kirim', 'Belum dikirim')
        ->update([
            'status_pesanan' => 'Menunggu Konfirmasi Pembatalan'
        ]);

    $kodePesanan = 'RK' . str_pad(
        $pesanan->id_pembelian,
        5,
        '0',
        STR_PAD_LEFT
    );

    $pesan =
        "Halo Admin Ruang Karya,%0A%0A" .
        "Saya ingin mengajukan pembatalan pesanan.%0A%0A" .
        "Kode Pesanan : {$kodePesanan}%0A" .
        "Nama : {$pesanan->nama_penerima}%0A%0A" .
        "Mohon diproses.%0A" .
        "Terima kasih.";

    $nomorAdmin = "6285150688313";

    return redirect(
        "https://wa.me/{$nomorAdmin}?text={$pesan}"
    );

    }
}