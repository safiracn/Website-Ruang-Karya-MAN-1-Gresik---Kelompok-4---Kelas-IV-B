<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilAdminController extends Controller
{
    public function index()
    {
        $admin = DB::table('users')
<<<<<<< HEAD
            ->where('id', Auth::id())
            ->where('role', 'admin')
            ->select('id', 'nama_lengkap', 'email', 'no_telp', 'alamat')
            ->first();
=======
        ->select('id_user','nama_lengkap','email','no_telp','alamat')
        ->where('id_user', Auth::id())
        ->where('role','admin')
        ->first();
>>>>>>> shava

        return view('admin.profil', compact('admin'));
    }
}