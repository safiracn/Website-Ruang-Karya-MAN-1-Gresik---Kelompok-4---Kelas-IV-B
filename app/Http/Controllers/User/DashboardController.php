<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Kalau sudah login → redirect ke dashboard sesuai role
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // role user → lanjut ke dashboard user
        }

        // Tamu atau user biasa → tampilkan dashboard pembeli
        return view('user.dashboard');
    }
}