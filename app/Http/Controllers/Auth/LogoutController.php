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
            
            // Keluarkan user secara resmi
            Auth::logout();
        }

        // Proses pembersihan sisa session (tetap dijalankan agar aman)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman login dengan selamat tanpa error 500
        return redirect()->route('login');
    }
}