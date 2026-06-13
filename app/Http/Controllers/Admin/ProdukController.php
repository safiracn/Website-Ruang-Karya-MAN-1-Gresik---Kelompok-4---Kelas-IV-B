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

        // Menggunakan Database Transaction agar jika salah satu proses gagal, data tidak rusak
        DB::beginTransaction();

        try {
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

            // Insert varian jika ada input
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

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success_tambah', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
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
            'foto_utama'  => 'nullable|image|max:2048', // Diubah jadi nullable karena saat edit foto tidak wajib diisi kembali
        ]);

        DB::beginTransaction();

        try {
            // Ambil foto lama dari database
            $fotoProduk = DB::table('produk')->where('id_produk', $id)->value('foto_produk');

            // Jika mengupload foto utama baru
            if ($request->hasFile('foto_utama')) {
                // Hapus foto lama di folder public/image jika file-nya ada
                if ($fotoProduk && file_exists(public_path('image/' . $fotoProduk))) {
                    @unlink(public_path('image/' . $fotoProduk));
                }

                $foto = $request->file('foto_utama');
                $namaFile = uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('image'), $namaFile);
                $fotoProduk = $namaFile;
            }

            // Update data produk dasar
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

            // --- LOGIKA UPDATE / INSERT / DELETE VARIAN YANG SUDAH DIPERBAIKI ---
            
            // 1. Ambil semua ID varian lama yang tercatat di database untuk produk ini
$idsLama = DB::table('produk_varian')
    ->where('id_produk', $id)
    ->pluck('id_varian')
    ->toArray();

// 2. Ambil ID varian pertama/paling tua (Varian Standard) agar TIDAK MASUK daftar hapus
$idVarianPertama = DB::table('produk_varian')
    ->where('id_produk', $id)
    ->orderBy('id_varian', 'asc') // Urutkan berdasarkan ID terkecil
    ->value('id_varian');

// 3. Ambil ID varian yang dikirim dari form
$idsForm = array_filter($request->id_varian ?? []);

// 4. Cari selisih mana yang harus dihapus
$hapusIds = array_diff($idsLama, $idsForm);

// KUNCI PENGAMAN: Hapus ID varian pertama dari daftar hapus (jika tidak sengaja masuk)
if (($key = array_search($idVarianPertama, $hapusIds)) !== false) {
    unset($hapusIds[$key]);
}

// 5. Eksekusi hapus varian sisa yang memang boleh dihapus
if (!empty($hapusIds)) {
    DB::table('produk_varian')
        ->whereIn('id_varian', $hapusIds)
        ->delete();
}

// 6. Proses Update atau Tambah Baru seperti biasa
if ($request->filled('nama_varian')) {
    foreach ($request->nama_varian as $i => $nama) {
        $harga = preg_replace('/[^0-9]/', '', $request->harga_varian[$i] ?? '0');
        $stok  = preg_replace('/[^0-9]/', '', $request->stok_varian[$i] ?? '0');

        if (!empty($request->id_varian[$i])) {
            // Ini akan tetap meng-update nama, harga, atau stok dari varian standard/pertama
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

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success_update', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }
}