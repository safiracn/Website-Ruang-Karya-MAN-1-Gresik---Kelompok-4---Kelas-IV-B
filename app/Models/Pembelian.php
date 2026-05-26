<?php
// ============================================================
// app/Models/Pembelian.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table      = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    public $timestamps    = false;

    protected $fillable = [
        'id_user', 'tgl_pembelian',
        'nama_penerima', 'no_telp_penerima',
        'provinsi', 'kota_kabupaten', 'kode_pos', 'detail_alamat',
        'metode_pengiriman',
        'status_pembayaran', 'status_kirim', 'status_pesanan',
        'total_harga',
    ];

    // Cast tanggal
    protected $casts = [
        'tgl_pembelian' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian', 'id_pembelian');
    }
}