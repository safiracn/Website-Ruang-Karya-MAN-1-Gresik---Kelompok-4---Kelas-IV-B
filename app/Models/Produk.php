<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps    = false;

    protected $fillable = [
        'id_kategori', 'nama_produk', 'deskripsi',
        'bahan', 'finishing', 'dimensi', 'garansi', 'foto_produk',
    ];

    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    // Relasi ke varian-varian produk
    public function varian()
    {
        return $this->hasMany(ProdukVarian::class, 'id_produk', 'id_produk');
    }
}