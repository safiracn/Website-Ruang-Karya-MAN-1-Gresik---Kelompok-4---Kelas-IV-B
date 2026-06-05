<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan penting! Kategori harus ada sebelum Produk,
     * Admin (users) harus ada sebelum data transaksi.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
            AdminSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}