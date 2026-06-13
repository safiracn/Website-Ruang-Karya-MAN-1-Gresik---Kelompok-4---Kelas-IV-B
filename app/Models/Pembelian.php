<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    protected $table      = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    public    $timestamps = true; // pakai created_at & updated_at Laravel standar

    protected $fillable = [
        'id_user',
        'nama_penerima',
        'no_telp_penerima',
        'provinsi',
        'kota_kabupaten',
        'kode_pos',
        'detail_alamat',
        'metode_pengiriman',
        'status_pembayaran',
        'status_kirim',
        'status_pesanan',
        'total_harga',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'total_harga' => 'float',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────

    /** Pemilik akun yang memesan */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /** Detail item dalam pesanan ini */
    public function details(): HasMany
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian', 'id_pembelian');
    }

    // ─── Accessor ─────────────────────────────────────────────────────────

    /** Kode pesanan otomatis, contoh: RK260605-0003 */
    public function getKodePesananAttribute(): string
    {
        return 'RK260605-' . sprintf('%04d', $this->id_pembelian);
    }
}