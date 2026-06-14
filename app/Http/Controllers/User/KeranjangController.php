<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    // Ambil atau buat id_keranjang untuk user ini
    private function getOrCreateKeranjang($id_user): int
    {
        $keranjang = DB::table('keranjang')->where('id_user', $id_user)->first();

        if (!$keranjang) {
            $id = DB::table('keranjang')->insertGetId(['id_user' => $id_user]);
            return $id;
        }

        return (int) $keranjang->id_keranjang;
    }

    public function index()
    {
        $id_user     = Auth::id();
        $id_keranjang = $this->getOrCreateKeranjang($id_user);

        $items = DB::table('keranjang_detail as kd')
            ->join('produk_varian as pv', 'kd.id_varian', '=', 'pv.id_varian')
            ->join('produk as p', 'pv.id_produk', '=', 'p.id_produk')
            ->where('kd.id_keranjang', $id_keranjang)
            ->select(
                'kd.id_keranjang_detail', 'kd.jumlah',
                'pv.id_varian', 'pv.nama_varian', 'pv.harga',
                'p.nama_produk', 'p.foto_produk',
                DB::raw('(pv.harga * kd.jumlah) as subtotal')
            )
            ->orderByDesc('kd.id_keranjang_detail')
            ->get();

        $totalItem  = $items->count();
        $grandTotal = $items->sum('subtotal');

        return view('user.keranjang', compact('items', 'totalItem', 'grandTotal'));
    }
    
    // Menambahkan produk ke keranjang belanja
    public function tambah(Request $request)
    {
        $id_user     = Auth::id();
        $id_keranjang = $this->getOrCreateKeranjang($id_user);
        $id_varian   = (int) $request->id_varian;
        $jumlah      = (int) $request->jumlah;

        if ($id_varian > 0 && $jumlah > 0) {
            $varian = DB::table('produk_varian')->where('id_varian', $id_varian)->first();

            if ($varian && $varian->stok > 0) {
                $existing = DB::table('keranjang_detail')
                    ->where('id_keranjang', $id_keranjang)
                    ->where('id_varian', $id_varian)
                    ->first();

                if ($existing) {
                    $jumlahBaru = min($existing->jumlah + $jumlah, $varian->stok);
                    DB::table('keranjang_detail')
                        ->where('id_keranjang_detail', $existing->id_keranjang_detail)
                        ->update(['jumlah' => $jumlahBaru]);
                } else {
                    DB::table('keranjang_detail')->insert([
                        'id_keranjang' => $id_keranjang,
                        'id_varian'    => $id_varian,
                        'jumlah'       => min($jumlah, $varian->stok),
                    ]);
                }
            }
        }

        return redirect()->route('keranjang');
    }

    // Mengubah jumlah produk dalam keranjang
    public function updateQty(Request $request)
    {
        $id_user     = Auth::id();
        $id_keranjang = $this->getOrCreateKeranjang($id_user);
        $id_detail   = (int) $request->id_detail;
        $mode        = $request->mode_qty;

        $row = DB::table('keranjang_detail as kd')
            ->join('produk_varian as pv', 'kd.id_varian', '=', 'pv.id_varian')
            ->where('kd.id_keranjang_detail', $id_detail)
            ->where('kd.id_keranjang', $id_keranjang)
            ->select('kd.jumlah', 'pv.stok', 'kd.id_keranjang_detail')
            ->first();

        if ($row) {
            $jumlahBaru = $mode === 'plus'
                ? min($row->jumlah + 1, $row->stok)
                : max($row->jumlah - 1, 1);

            DB::table('keranjang_detail')
                ->where('id_keranjang_detail', $id_detail)
                ->update(['jumlah' => $jumlahBaru]);
        }

        return redirect()->route('keranjang');
    }

    // Menghapus produk dari keranjang
    public function hapus(Request $request)
    {
        $id_user     = Auth::id();
        $id_keranjang = $this->getOrCreateKeranjang($id_user);
        $id_detail   = (int) $request->id_detail;

        DB::table('keranjang_detail')
            ->where('id_keranjang_detail', $id_detail)
            ->where('id_keranjang', $id_keranjang)
            ->delete();

        return redirect()->route('keranjang');
    }
}