<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityHelper;

class ProdukController extends Controller
{
    // FORM TAMBAH PRODUK
    public function create()
    {
        return view('admin.tambah-produk');
    }

    // PROSES TAMBAH PRODUK
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'deskripsi'   => 'required',
            'bahan'       => 'required',
            'garansi'     => 'required',
            'foto_utama'  => 'required|image|max:2048',
        ]);

        // Upload foto
        $foto = $request->file('foto_utama');
        $namaFile = uniqid() . '.' . $foto->getClientOriginalExtension();
        $foto->move(public_path('image'), $namaFile);

        // Insert produk
        $id_produk_baru = DB::table('produk')->insertGetId([
            'nama_produk' => $request->nama_produk,
            'id_kategori' => $request->id_kategori,
            'deskripsi'   => $request->deskripsi,
            'bahan'       => $request->bahan,
            'finishing'   => $request->finishing,
            'dimensi'     => $request->dimensi,
            'garansi'     => $request->garansi,
            'foto_produk' => $namaFile,
        ]);

        ActivityHelper::log(
            'Tambah Produk',
            'Menambahkan produk: ' . $request->nama_produk
        );

        // Insert varian
        if ($request->filled('nama_varian')) {
            foreach ($request->nama_varian as $i => $nama) {
                $harga = preg_replace('/[^0-9]/', '', $request->harga_varian[$i] ?? '0');
                $stok  = preg_replace('/[^0-9]/', '', $request->stok_varian[$i] ?? '0');

                DB::table('produk_varian')->insert([
                    'id_produk'   => $id_produk_baru,
                    'nama_varian' => $nama,
                    'harga'       => $harga ?: 0,
                    'stok'        => $stok ?: 0,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')
    ->with('success_tambah', true);
    }

    // FORM EDIT PRODUK
    public function edit($id)
    {
        $id = (int) $id;

        $produk = DB::table('produk as p')
            ->join('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
            ->where('p.id_produk', $id)
            ->select('p.*', 'k.nama_kategori')
            ->first();

        if (!$produk) {
            abort(404, 'Produk tidak ditemukan');
        }

        $varians = DB::table('produk_varian')
            ->where('id_produk', $id)
            ->get();

        return view('admin.edit', compact('produk', 'varians'));
    }

    // PROSES UPDATE PRODUK
    public function update(Request $request, $id)
    {
        $id = (int) $id;

        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'deskripsi'   => 'required',
            'bahan'       => 'required',
            'garansi'     => 'required',
        ]);

        $fotoProduk = DB::table('produk')
    ->where('id_produk', $id)
    ->value('foto_produk');

if ($request->hasFile('foto_utama')) {

    $foto = $request->file('foto_utama');

    $namaFile = uniqid() . '.' . $foto->getClientOriginalExtension();

    $foto->move(public_path('image'), $namaFile);

    $fotoProduk = $namaFile;
}

        DB::table('produk')->where('id_produk', $id)->update([
    'nama_produk' => $request->nama_produk,
    'id_kategori' => $request->id_kategori,
    'deskripsi'   => $request->deskripsi,
    'bahan'       => $request->bahan,
    'finishing'   => $request->finishing,
    'dimensi'     => $request->dimensi,
    'garansi'     => $request->garansi,
    'foto_produk' => $fotoProduk,
]);

        // Update / Insert / Delete Varian
if ($request->filled('nama_varian')) {

    $idsLama = DB::table('produk_varian')
        ->where('id_produk', $id)
        ->pluck('id_varian')
        ->toArray();

    $idsForm = array_filter($request->id_varian ?? []);

    // Hapus varian yang tidak ada di form lagi
    $hapusIds = array_diff($idsLama, $idsForm);

    if (!empty($hapusIds)) {
        DB::table('produk_varian')
            ->whereIn('id_varian', $hapusIds)
            ->delete();
    }

    // Update / insert varian
    foreach ($request->nama_varian as $i => $nama) {

        $harga = preg_replace('/[^0-9]/', '', $request->harga_varian[$i] ?? '0');
        $stok  = preg_replace('/[^0-9]/', '', $request->stok_varian[$i] ?? '0');

        if (!empty($request->id_varian[$i])) {

            // UPDATE
            DB::table('produk_varian')
                ->where('id_varian', $request->id_varian[$i])
                ->update([
                    'nama_varian' => $nama,
                    'harga'       => $harga ?: 0,
                    'stok'        => $stok ?: 0,
                ]);

        } else {

            // INSERT BARU
            DB::table('produk_varian')->insert([
                'id_produk'   => $id,
                'nama_varian' => $nama,
                'harga'       => $harga ?: 0,
                'stok'        => $stok ?: 0,
            ]);
        }
    }
}

    ActivityHelper::log(
        'Edit Produk',
        'Mengubah produk: ' . $request->nama_produk
    );

        return redirect()->route('admin.dashboard')
            ->with('success_update', true);
    }
}