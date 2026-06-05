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

        if ($users->role !== $role) {
            // Jika admin coba akses route user → ke dashboard admin
            if ($users->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // Jika user coba akses route admin → ke home
            return redirect()->route('home');
        }

        return $next($request);
    }
}