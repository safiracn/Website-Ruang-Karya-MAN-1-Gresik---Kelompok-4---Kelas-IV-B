<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityHelper;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id_user;
        $items = [];

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
            'provinsi'  => ['required'],
            'kota'      => ['required'],
            'kodePos'   => ['required', 'regex:/^[0-9]+$/'],
            'detail'    => 'required',
            'pengiriman'=> 'required|in:Ambil,Antar',
        ]);

        $id_pembelian = DB::transaction(function () use ($request) {

            $id_user = Auth::id();

            $id_pembelian = DB::table('pembelian')->insertGetId([
                'id_user'           => $id_user,
                'nama_penerima'     => $request->nama,
                'no_telp_penerima'  => $request->noTelp,
                'provinsi'          => $request->provinsi,
                'kota_kabupaten'    => $request->kota,
                'kode_pos'          => $request->kodePos,
                'detail_alamat'     => $request->detail,
                'metode_pengiriman' => $request->pengiriman,
                'total_harga'       => $request->total_final,
            ]);

            if ($request->has('items')) {

                foreach ($request->items as $item) {

                    $subtotal = $item['harga'] * $item['jumlah'];

                    DB::table('pembelian_detail')->insert([
                        'id_pembelian' => $id_pembelian,
                        'id_varian'    => $item['id_varian'],
                        'jumlah'       => $item['jumlah'],
                        'harga_satuan' => $item['harga'],
                        'subtotal'     => $subtotal,
                    ]);

                    DB::table('produk_varian')
                        ->where('id_varian', $item['id_varian'])
                        ->decrement('stok', $item['jumlah']);
                }
            }

            if ($request->filled('selected_items')) {
                $selectedIds = array_filter(
                    array_map('intval', explode(',', $request->selected_items))
                );

                if (!empty($selectedIds)) {

                    DB::table('keranjang_detail')
                        ->whereIn('id_keranjang_detail', $selectedIds)
                        ->delete();
                }
            }

            ActivityHelper::log(
                'Checkout User',
                'Membuat pesanan #' . $id_pembelian .
                ' total Rp ' . number_format($request->total_final, 0, ',', '.')
            );

            // Hapus item yang sudah dicheckout dari keranjang
            if ($request->filled('selected_items')) {

                $ids = array_filter(
                    array_map('intval', explode(',', $request->selected_items))
                );

                DB::table('keranjang_detail')
                    ->whereIn('id_keranjang_detail', $ids)
                    ->delete();
            }

            return $id_pembelian;
        });

        return redirect()->route('order.sukses', $id_pembelian);
    }

    public function orderSukses($id)
    {
        $pesanan = DB::table('pembelian')
            ->where('id_pembelian', $id)
            ->first();

        $detailPesanan = DB::table('pembelian_detail')
            ->join('produk_varian', 'pembelian_detail.id_varian', '=', 'produk_varian.id_varian')
            ->join('produk', 'produk_varian.id_produk', '=', 'produk.id_produk')
            ->where('pembelian_detail.id_pembelian', $id)
            ->select(
                'produk.nama_produk',
                'produk.foto_produk',
                'produk_varian.nama_varian',
                'pembelian_detail.jumlah',
                'pembelian_detail.harga_satuan',
                'pembelian_detail.subtotal'
            )
            ->get();

        return view('user.ordersukses', compact('pesanan', 'detailPesanan'));
    }
}