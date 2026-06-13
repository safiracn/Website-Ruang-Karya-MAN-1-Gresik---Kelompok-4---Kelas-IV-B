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
        ActivityHelper::log(
            'Logout',
            Auth::user()->nama_lengkap . ' logout dari sistem'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}