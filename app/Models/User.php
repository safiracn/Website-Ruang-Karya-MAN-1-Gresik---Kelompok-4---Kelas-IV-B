<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id'; 

    public $timestamps = true;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'role_id',
        'no_telp',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * ============================================================
     * HUBUNGAN RELASI KE TABEL ROLES (INI YANG WAJIB ADA)
     * ============================================================
     */
    public function role()
    {
        // Hubungan Many-to-One (User memiliki satu Role via role_id)
        return $this->belongsTo(Role::class, 'role_id');
    }
}