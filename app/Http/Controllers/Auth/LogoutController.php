<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityHelper;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityHelper::log(
                'Logout',
                Auth::user()->nama_lengkap . ' logout dari sistem'
            );
            
            // Menghapus status login pengguna
            Auth::logout();
        }

        // Menghapus session yang masih tersisa setelah logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman login dengan selamat tanpa error 500
        return redirect()->route('login');
    }
}