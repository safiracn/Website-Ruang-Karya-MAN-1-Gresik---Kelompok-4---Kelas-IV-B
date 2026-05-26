<?php
// ============================================================
// app/Models/Keranjang.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table      = 'keranjang';
    protected $primaryKey = 'id_keranjang';
    public $timestamps    = false;

    protected $fillable = ['id_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(KeranjangDetail::class, 'id_keranjang', 'id_keranjang');
    }
}