<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // ─── PRODUK ───────────────────────────────────────────────────────────
        $produk = [
            // id_kategori 1 = Mebel
            [
                'id_kategori' => 1,
                'nama_produk' => 'Meja Aksara',
                'deskripsi'   => 'Meja minimalis ukuran 110 × 60 cm yang kokoh dan cocok untuk belajar, bekerja, maupun kebutuhan sehari-hari.',
                'bahan'       => 'Kayu Jati Belanda',
                'finishing'   => 'Natural Glossy',
                'dimensi'     => '110 x 60 cm',
                'garansi'     => '6 Bulan',
                'foto_produk' => 'meja belajar kotak.jpeg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_kategori' => 1,
                'nama_produk' => 'Meja Ksatria',
                'deskripsi'   => 'Meja kayu elegan berukuran 110 × 70 cm dengan desain modern yang memberikan kenyamanan saat digunakan.',
                'bahan'       => 'Kayu Mahoni',
                'finishing'   => 'Dark Brown Doft',
                'dimensi'     => '110 x 70 cm',
                'garansi'     => '1 Tahun',
                'foto_produk' => 'meja 2 lapis.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_kategori' => 1,
                'nama_produk' => 'Meja Bulong',
                'deskripsi'   => 'Meja serbaguna ukuran 120 × 60 cm dengan tampilan simpel yang cocok melengkapi berbagai ruangan.',
                'bahan'       => 'Kayu Pinus',
                'finishing'   => 'Clear Gloss',
                'dimensi'     => '120 x 60 cm',
                'garansi'     => '3 Bulan',
                'foto_produk' => 'meja lucu.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_kategori' => 1,
                'nama_produk' => 'Kursi Kartini',
                'deskripsi'   => 'Kursi kayu minimalis ukuran ±45 × 45 × 85 cm yang nyaman digunakan untuk berbagai kebutuhan.',
                'bahan'       => 'Kayu Jati',
                'finishing'   => 'Politur Tradisional',
                'dimensi'     => '45 x 45 x 85 cm',
                'garansi'     => '1 Tahun',
                'foto_produk' => 'kursi.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_kategori' => 1,
                'nama_produk' => 'Kursi Rahayu',
                'deskripsi'   => 'Kursi kayu elegan ukuran ±45 × 45 × 90 cm dengan desain sederhana dan kokoh.',
                'bahan'       => 'Kayu Sonokeling',
                'finishing'   => 'Natural Wax',
                'dimensi'     => '45 x 45 x 90 cm',
                'garansi'     => '1 Tahun',
                'foto_produk' => 'kursi 2.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // id_kategori 2 = Busana
            [
                'id_kategori' => 2,
                'nama_produk' => 'Batik Nusantara',
                'deskripsi'   => 'Batik berkualitas dengan bahan nyaman dipakai tersedia ukuran S, M, dan L.',
                'bahan'       => 'Katun Primisima',
                'finishing'   => 'Hand Printing',
                'dimensi'     => null,
                'garansi'     => 'Tidak Ada',
                'foto_produk' => 'batik 1.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_kategori' => 2,
                'nama_produk' => 'Batik Nuansa Bening',
                'deskripsi'   => 'Kemeja batik motif elegan dengan bahan adem tersedia ukuran S, M, dan L.',
                'bahan'       => 'Katun Silk',
                'finishing'   => 'Cap Tradisional',
                'dimensi'     => null,
                'garansi'     => 'Tidak Ada',
                'foto_produk' => 'batik 2.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // id_kategori 3 = Kerajinan Tangan
            [
                'id_kategori' => 3,
                'nama_produk' => 'Vas Bunga Anyam Premium',
                'deskripsi'   => 'Vas bunga anyaman handmade dengan ukuran 20 × 20 × 30 cm yang cocok untuk dekorasi ruangan.',
                'bahan'       => 'Rotan Alam',
                'finishing'   => 'Melamine Coating',
                'dimensi'     => '20 x 20 x 30 cm',
                'garansi'     => '1 Bulan',
                'foto_produk' => 'vas bunga.jpg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('produk')->insert($produk);

        // ─── PRODUK VARIAN ────────────────────────────────────────────────────
        // Ambil id_produk yang baru saja diinsert berdasarkan urutan
        $idMejaAksara       = DB::table('produk')->where('nama_produk', 'Meja Aksara')->value('id_produk');
        $idMejaKsatria      = DB::table('produk')->where('nama_produk', 'Meja Ksatria')->value('id_produk');
        $idMejaBulong       = DB::table('produk')->where('nama_produk', 'Meja Bulong')->value('id_produk');
        $idKursiKartini     = DB::table('produk')->where('nama_produk', 'Kursi Kartini')->value('id_produk');
        $idKursiRahayu      = DB::table('produk')->where('nama_produk', 'Kursi Rahayu')->value('id_produk');
        $idBatikNusantara   = DB::table('produk')->where('nama_produk', 'Batik Nusantara')->value('id_produk');
        $idBatikBening      = DB::table('produk')->where('nama_produk', 'Batik Nuansa Bening')->value('id_produk');
        $idVasBunga         = DB::table('produk')->where('nama_produk', 'Vas Bunga Anyam Premium')->value('id_produk');

        $varian = [
            // ── Mebel: 1 varian Standard ─────────────────────────────────────
            ['id_produk' => $idMejaAksara,   'nama_varian' => 'Standard', 'harga' => 100000, 'stok' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idMejaKsatria,  'nama_varian' => 'Standard', 'harga' => 180000, 'stok' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idMejaBulong,   'nama_varian' => 'Standard', 'harga' => 150000, 'stok' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idKursiKartini, 'nama_varian' => 'Standard', 'harga' => 99999,  'stok' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idKursiRahayu,  'nama_varian' => 'Standard', 'harga' => 129000, 'stok' => 12, 'created_at' => now(), 'updated_at' => now()],

            // ── Busana: varian ukuran S, M, L, XL, XXL ───────────────────────
            // Batik Nusantara
            ['id_produk' => $idBatikNusantara, 'nama_varian' => 'S',   'harga' => 100000, 'stok' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikNusantara, 'nama_varian' => 'M',   'harga' => 100000, 'stok' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikNusantara, 'nama_varian' => 'L',   'harga' => 100000, 'stok' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikNusantara, 'nama_varian' => 'XL',  'harga' => 105000, 'stok' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikNusantara, 'nama_varian' => 'XXL', 'harga' => 110000, 'stok' => 5,  'created_at' => now(), 'updated_at' => now()],

            // Batik Nuansa Bening
            ['id_produk' => $idBatikBening, 'nama_varian' => 'S',   'harga' => 100000, 'stok' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikBening, 'nama_varian' => 'M',   'harga' => 100000, 'stok' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikBening, 'nama_varian' => 'L',   'harga' => 100000, 'stok' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikBening, 'nama_varian' => 'XL',  'harga' => 105000, 'stok' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_produk' => $idBatikBening, 'nama_varian' => 'XXL', 'harga' => 110000, 'stok' => 5,  'created_at' => now(), 'updated_at' => now()],

            // ── Kerajinan: 1 varian Standard ─────────────────────────────────
            ['id_produk' => $idVasBunga, 'nama_varian' => 'Standard', 'harga' => 50000, 'stok' => 30, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('produk_varian')->insert($varian);
    }
}