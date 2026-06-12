<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilUserController extends Controller
{
    public function index()
    {
        $user = DB::table('users')
            ->where('id_user', Auth::user()->id_user)
            ->where('role', 'user')
            ->select('id_user', 'nama_lengkap', 'email', 'no_telp', 'alamat')
            ->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        $nama = $user->nama_lengkap ?? 'User';
        $parts = preg_split('/\s+/', trim($nama));
        $inisial = '';

        foreach ($parts as $i => $part) {
            if ($i >= 2) break;
            $inisial .= strtoupper(substr($part, 0, 1));
        }

        $inisial = $inisial ?: 'US';

        $avatarUrl = "https://ui-avatars.com/api/?name="
            . urlencode($inisial)
            . "&background=e2e8f0&color=1e3a8a&size=128";

        return view('user.profil', compact('user', 'avatarUrl'));
    }
}