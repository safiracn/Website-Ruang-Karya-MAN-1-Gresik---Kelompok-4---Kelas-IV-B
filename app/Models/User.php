<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $timestamps = true;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_telp',
        'alamat',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}