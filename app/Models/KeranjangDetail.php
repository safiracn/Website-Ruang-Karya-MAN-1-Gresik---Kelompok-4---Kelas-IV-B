<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeranjangDetail extends Model
{
    protected $table      = 'keranjang_detail';
    protected $primaryKey = 'id_keranjang_detail';
    public $timestamps    = false;

    protected $fillable = ['id_keranjang', 'id_varian', 'jumlah'];

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang');
    }

    public function varian()
    {
        return $this->belongsTo(ProdukVarian::class, 'id_varian', 'id_varian');
    }
}