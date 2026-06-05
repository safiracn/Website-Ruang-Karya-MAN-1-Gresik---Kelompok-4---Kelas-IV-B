<?php
// ============================================================
// app/Models/ProdukVarian.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukVarian extends Model
{
    protected $table      = 'produk_varian';
    protected $primaryKey = 'id_varian';
    public $timestamps    = false;

    protected $fillable = ['id_produk', 'nama_varian', 'harga', 'stok'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function keranjangDetail()
    {
        return $this->hasMany(KeranjangDetail::class, 'id_varian', 'id_varian');
    }

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class, 'id_varian', 'id_varian');
    }
}