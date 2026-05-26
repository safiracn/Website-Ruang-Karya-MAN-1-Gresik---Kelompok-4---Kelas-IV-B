<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    protected $table      = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';
    public $timestamps    = false;

    protected $fillable = [
        'id_pembelian', 'id_varian',
        'jumlah', 'harga_satuan', 'subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function varian()
    {
        return $this->belongsTo(ProdukVarian::class, 'id_varian', 'id_varian');
    }
}