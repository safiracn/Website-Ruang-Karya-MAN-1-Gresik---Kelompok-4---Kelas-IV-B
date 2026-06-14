<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DetailProdukController extends Controller
{
    public function show($id)
    {
        $id = (int) $id;

        $produk = DB::table('produk as p') // Mengambil informasi lengkap produk yang dipilih pengguna
            ->join('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
            ->where('p.id_produk', $id)
            ->select('p.*', 'k.nama_kategori')
            ->first();

        if (!$produk) {
            return redirect()->route('katalog')->with('error', 'Produk tidak ditemukan.');
        }

        // Urutan varian: XS, S, M, L, XL, XXL, Standard, lainnya
        $varians = DB::table('produk_varian') // Mengambil seluruh varian produk seperti ukuran atau jenis produk
            ->where('id_produk', $id)
            ->orderByRaw("CASE nama_varian
                WHEN 'XS' THEN 1 WHEN 'S' THEN 2 WHEN 'M' THEN 3
                WHEN 'L' THEN 4 WHEN 'XL' THEN 5 WHEN 'XXL' THEN 6
                WHEN 'Standard' THEN 7 ELSE 8 END, id_varian ASC")
            ->get();

        if ($varians->isEmpty()) {
            return redirect()->route('katalog')->with('error', 'Varian produk belum tersedia.');
        }

        $varianPertama = $varians->first();
        $isBusana = strtolower(trim($produk->nama_kategori)) === 'busana';

        return view('user.detail-produk', compact('produk', 'varians', 'varianPertama', 'isBusana'));
    }
}