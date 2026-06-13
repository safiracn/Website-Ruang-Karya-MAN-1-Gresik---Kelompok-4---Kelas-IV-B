<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityHelper;

class PesananController extends Controller
{
    public function index(Request $request)
{
    $search = $request->search;

    $pesanan = DB::table('pembelian as p')
        ->join('users as u', 'p.id_user', '=', 'u.id')
        ->select(
            'p.*',
            'u.nama_lengkap',
            'u.email',
            'u.no_telp'
        )

        ->when($request->filled('search'), function ($q) use ($request) {

            $search = trim($request->search);

            $q->where(function ($sub) use ($search) {

                $sub->where('u.nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('p.nama_penerima', 'LIKE', "%{$search}%")
                    ->orWhere('u.no_telp', 'LIKE', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT('RK', LPAD(p.id_pembelian,5,'0')) LIKE ?",
                        ["%{$search}%"]
                    );

            });

        })

        ->when($request->status_pembayaran, function ($q) use ($request) {
            $q->where('p.status_pembayaran', $request->status_pembayaran);
        })

        ->when($request->status_pesanan, function ($q) use ($request) {
            $q->where('p.status_pesanan', $request->status_pesanan);
        })

        ->when($request->status_kirim, function ($q) use ($request) {
            $q->where('p.status_kirim', $request->status_kirim);
        })

        ->orderByDesc('p.id_pembelian')
        ->get();

    $totalPesanan = DB::table('pembelian')->count();

    $belumDibayar = DB::table('pembelian')
    ->whereRaw('LOWER(status_pembayaran) = ?', ['belum dibayar'])
    ->sum('total_harga');
    
    $perluDiproses = DB::table('pembelian')
    ->where('status_pesanan', 'Diproses')
    ->count();

    $menungguPembatalan = DB::table('pembelian')
        ->where('status_pesanan', 'Menunggu Konfirmasi Pembatalan')
        ->count();

    $pesananSelesai = DB::table('pembelian')
        ->where('status_pesanan', 'Selesai')
        ->count();
    

    return view('admin.pesanan', compact(
    'pesanan',
    'totalPesanan',
    'belumDibayar',      // tambah ini
    'perluDiproses',
    'menungguPembatalan',
    'pesananSelesai',
    'search'
));
}

    public function update(Request $request, $id)
    {
        DB::table('pembelian')
            ->where('id_pembelian', $id)
            ->update([
                'status_pembayaran' => $request->status_pembayaran,
                'status_pesanan' => $request->status_pesanan,
                'status_kirim' => $request->status_kirim,
            ]);

    ActivityHelper::logPesanan(
        'Update Status Pesanan',
        $id,
        '-> Pembayaran: ' . $request->status_pembayaran .
        ', Pesanan: ' . $request->status_pesanan .
        ', Kirim: ' . $request->status_kirim
    );

        return back()->with('success', 'Status berhasil diperbarui');
    }


}