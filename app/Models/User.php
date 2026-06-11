<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
<<<<<<< HEAD
    protected $table      = 'users';       // nama tabel kamu

    protected $fillable = [
        'nama_lengkap', 'email', 'password', 'no_telp', 'alamat', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];
=======

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
>>>>>>> shava
}