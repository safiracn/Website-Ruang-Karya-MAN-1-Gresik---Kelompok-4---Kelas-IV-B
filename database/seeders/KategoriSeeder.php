<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori')->insert([
            ['nama_kategori' => 'Mebel',            'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Busana',           'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Kerajinan Tangan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}