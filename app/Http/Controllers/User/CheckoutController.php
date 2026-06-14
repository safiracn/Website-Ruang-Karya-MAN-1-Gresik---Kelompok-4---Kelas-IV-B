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
        $id_user = Auth::user()->id;
        $items = [];

        // 1. Ambil list ID item terpilih, prioritaskan dari data session lama (jika gagal validasi) baru dari request URL
        $selectedItemsString = old('selected_items', $request->selected_items);

        if (!empty($selectedItemsString)) {
            $ids = array_filter(array_map('intval', explode(',', $selectedItemsString)));

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

        // 2. Antisipasi jika metodenya adalah "Beli Langsung" tanpa lewat keranjang belanjaan
        elseif (($request->isMethod('post') && $request->input('aksi') === 'beli') || old('id_varian_langsung')) {
            
            // Ambil data produk (baik dari request post awal atau dari session flash data lama)
            $id_produk = old('id_produk_langsung', $request->id_produk);
            $id_varian = old('id_varian_langsung', $request->id_varian);
            $jumlah    = old('jumlah_langsung', $request->jumlah);

            $row = DB::table('produk as p')
                ->join('produk_varian as v', 'p.id_produk', '=', 'v.id_produk')
                ->where('p.id_produk', $id_produk)
                ->where('v.id_varian', $id_varian)
                ->select('p.nama_produk', 'p.foto_produk', 'v.harga', 'v.nama_varian')
                ->first();

            if ($row) {
                $items[] = [
                    'id_varian' => $id_varian,
                    'nama'      => $row->nama_produk . ' (' . $row->nama_varian . ')',
                    'harga'     => $row->harga,
                    'jumlah'    => $jumlah,
                    'gambar'    => $row->foto_produk,
                ];
            }
        }

        $totalAkhir = collect($items)->sum(fn($i) => $i['harga'] * $i['jumlah']);

        return view('user.checkout', compact('items', 'totalAkhir'));
    }

    public function proses(Request $request)
    {
        $rules = [
            'nama'      => ['required', 'regex:/^[a-zA-Z\s\']+$/'],
            'noTelp'    => ['required', 'regex:/^[0-9]+$/'],
            'provinsi'   => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'kota'       => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'kodePos'   => ['required', 'regex:/^[0-9]+$/'],
            'detail'    => 'required',
            'pengiriman'=> 'required|in:Ambil,Antar',
        ];

        // 2. Buat Pesan Error Kustom Bahasa Indonesia
    $messages = [
        'nama.required'     => 'Nama Penerima wajib diisi.',
        'nama.regex'        => 'Format Nama Lengkap salah! Hanya boleh huruf, spasi, dan tanda petik (\').',
        'noTelp.required'   => 'Nomor Telepon wajib diisi.',
        'noTelp.regex'      => 'Format Nomor Telepon salah! Hanya boleh berisi angka (0-9).',
        'provinsi.required' => 'Provinsi wajib diisi.',
        'provinsi.regex'    => 'Format Provinsi salah! Hanya boleh berisi huruf dan spasi.',
        'kota.required'     => 'Kota/Kabupaten wajib diisi.',
        'kota.regex'        => 'Format Kota/Kabupaten salah! Hanya boleh berisi huruf dan spasi.',
        'kodePos.required'  => 'Kode Pos wajib diisi.',
        'kodePos.regex'     => 'Format Kode Pos salah! Hanya boleh berisi angka (0-9).',
        'detail.required'   => 'Detail Alamat wajib diisi.',
    ];

    // ACID 
    $request->validate($rules, $messages);

        $id_pembelian = DB::transaction(function () use ($request) {

            $id_user = Auth::id();

            // Membuat data pesanan baru
            $id_pembelian = DB::table('pembelian')->insertGetId([
                'id_user'           => $id_user,
                'nama_penerima'     => $request->nama,
                'no_telp_penerima'  => $request->noTelp,
                'provinsi'          => $request->provinsi,
                'kota_kabupaten'    => $request->kota,
                'kode_pos'          => $request->kodePos,
                'detail_alamat'     => $request->detail,
                'metode_pengiriman' => $request->pengiriman,

                // STATUS AWAL PESANAN
                'status_pembayaran' => 'Belum Dibayar',
                'status_pesanan'    => 'Pending',
                'status_kirim'      => 'Belum dikirim',

                'total_harga'       => $request->total_final,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            if ($request->has('items')) {

                foreach ($request->items as $item) {

                    $subtotal = $item['harga'] * $item['jumlah'];

                    // Menyimpan detail produk yang dibeli
                    DB::table('pembelian_detail')->insert([
                        'id_pembelian' => $id_pembelian,
                        'id_varian'    => $item['id_varian'],
                        'jumlah'       => $item['jumlah'],
                        'harga_satuan' => $item['harga'],
                        'subtotal'     => $subtotal,
                    ]);

                    DB::table('produk_varian')
                        ->where('id_varian', $item['id_varian'])
                        ->decrement('stok', $item['jumlah']); // Mengurangi stok produk sesuai jumlah yang dibeli
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
        }); // ACID SAMPE SINI 

        return redirect()->route('order.sukses', $id_pembelian);
    }

    public function orderSukses($id)
    {
        $pesanan = DB::table('pembelian')
        ->select('pembelian.*', 'created_at AS tgl_pembelian') // ⬅️ Tambahkan select alias ini
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