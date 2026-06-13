<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nama_lengkap' => 'Admin',
                'email'        => 'admin@mangresik.go.id',
                'password'     => Hash::make('123456'),
                'role_id'      => 1,
                'no_telp'      => '081332783500',
                'alamat'       => 'Jalan Raya Bungah No. 46, Kecamatan Bungah, Kabupaten Gresik, Jawa Timur',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}