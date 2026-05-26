<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::id();
        $items   = [];

        // Dari keranjang (via GET selected_items)
        if ($request->filled('selected_items')) {
            $ids = array_filter(array_map('intval', explode(',', $request->selected_items)));

            if (!empty($ids)) {
                $placeholders = implode(',', $ids);
                $rows = DB::select("
                    SELECT kd.jumlah, pv.id_varian, pv.harga, pv.nama_varian, p.nama_produk, p.foto_produk
                    FROM keranjang_detail kd
                    JOIN produk_varian pv ON kd.id_varian = pv.id_varian
                    JOIN produk p ON pv.id_produk = p.id_produk
                    WHERE kd.id_keranjang_detail IN ($placeholders)
                ");

                foreach ($rows as $row) {
                    $items[] = [
                        'id_varian' => $row->id_varian,
                        'nama'      => $row->nama_produk . ' (' . $row->nama_varian . ')',
                        'harga'     => $row->harga,
                        'jumlah'    => $row->jumlah,
                        'gambar'    => $row->foto_produk,
                    ];
                }
            }
        }
        // Beli langsung dari detail produk
        elseif ($request->isMethod('post') && $request->input('aksi') === 'beli') {
            $row = DB::table('produk as p')
                ->join('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
                ->where('p.id_produk', $request->id_produk)
                ->where('v.id_varian', $request->id_varian)
                ->select('p.nama_produk', 'p.foto_produk', 'v.harga', 'v.nama_varian')
                ->first();

            if ($row) {
                $items[] = [
                    'id_varian' => $request->id_varian,
                    'nama'      => $row->nama_produk . ' (' . $row->nama_varian . ')',
                    'harga'     => $row->harga,
                    'jumlah'    => $request->jumlah,
                    'gambar'    => $row->foto_produk,
                ];
            }
        }

        $totalAkhir = collect($items)->sum(fn($i) => $i['harga'] * $i['jumlah']);

        return view('user.checkout', compact('items', 'totalAkhir'));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'nama'      => ['required', 'regex:/^[a-zA-Z\s\']+$/'],
            'noTelp'    => ['required', 'regex:/^[0-9]+$/'],
            'provinsi'  => ['required', 'regex:/^[a-zA-Z\s\']+$/'],
            'kota'      => ['required', 'regex:/^[a-zA-Z\s\']+$/'],
            'kodePos'   => ['required', 'regex:/^[0-9]+$/'],
            'detail'    => 'required',
            'pengiriman'=> 'required|in:Ambil,Antar',
        ]);

        $id_user = Auth::id();

        $id_pembelian = DB::table('pembelian')->insertGetId([
            'id_user'            => $id_user,
            'nama_penerima'      => $request->nama,
            'no_telp_penerima'   => $request->noTelp,
            'provinsi'           => $request->provinsi,
            'kota_kabupaten'     => $request->kota,
            'kode_pos'           => $request->kodePos,
            'detail_alamat'      => $request->detail,
            'metode_pengiriman'  => $request->pengiriman,
            'total_harga'        => $request->total_final,
        ]);

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $subtotal = $item['harga'] * $item['jumlah'];

                DB::table('pembelian_detail')->insert([
                    'id_pembelian'  => $id_pembelian,
                    'id_varian'     => $item['id_varian'],
                    'jumlah'        => $item['jumlah'],
                    'harga_satuan'  => $item['harga'],
                    'subtotal'      => $subtotal,
                ]);

                // Kurangi stok
                DB::table('produk_varian')
                    ->where('id_varian', $item['id_varian'])
                    ->decrement('stok', $item['jumlah']);
            }

            // Kosongkan keranjang setelah checkout
            $keranjang = DB::table('keranjang')->where('id_user', $id_user)->first();
            if ($keranjang) {
                DB::table('keranjang_detail')
                    ->where('id_keranjang', $keranjang->id_keranjang)
                    ->delete();
            }
        }

        return redirect()->route('riwayat')->with('success', 'Pesanan berhasil disimpan! Terima kasih telah berbelanja.');
    }
}