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
            ->where('id', Auth::id())
            ->where('role', 'admin')
            ->select('id', 'nama_lengkap', 'email', 'no_telp', 'alamat')
            ->first();

        return view('admin.profil', compact('admin'));
    }
}