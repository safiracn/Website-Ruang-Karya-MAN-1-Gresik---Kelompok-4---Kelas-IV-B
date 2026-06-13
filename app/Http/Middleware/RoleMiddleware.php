<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Middleware ini menggantikan logika:
     * if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin')
     *
     * Cara kerja:
     * - Jika user belum login → redirect ke login
     * - Jika user login tapi role tidak sesuai → redirect sesuai role-nya
     */
    public function handle(Request $request, Closure $next, string $role)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $users = Auth::user();

    // PENGAMAN: Cek dulu apakah user ini punya relasi role atau tidak
    if (!$users->role) {
        // Jika tidak punya role, paksa logout dan kembalikan ke halaman login
        Auth::logout();
        return redirect()->route('login')->with('error', 'Akun Anda tidak memiliki role yang valid. Silakan hubungi admin.');
    }

    // Jika aman punya role, ambil nama teks rolenya
    $namaRoleUser = $users->role->nama_role; 

    if ($namaRoleUser !== $role) {
        if ($namaRoleUser === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }

    return $next($request);
}
}