<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class KatalogController extends Controller
{
    public function index()
    {
        $kategoris = DB::table('kategori')->get();

        // Ambil produk + kategori + harga termurah dari varian
        $produk = DB::table('produk as p')
            ->join('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
            ->join('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
            ->select(
    'p.id_produk',
    'p.id_kategori',
    'p.nama_produk',
    'p.deskripsi',
    'p.bahan',
    'p.finishing',
    'p.dimensi',
    'p.garansi',
    'p.foto_produk',
    'p.created_at',
    'p.updated_at',
    'k.nama_kategori',
    DB::raw('MIN(v.harga) as harga_mulai')
)
            ->groupBy(
    'p.id_produk',
    'p.id_kategori',
    'p.nama_produk',
    'p.deskripsi',
    'p.bahan',
    'p.finishing',
    'p.dimensi',
    'p.garansi',
    'p.foto_produk',
    'p.created_at',
    'p.updated_at',
    'k.nama_kategori'
)
            ->get();

        return view('user.katalog', compact('kategoris', 'produk'));
    }
}