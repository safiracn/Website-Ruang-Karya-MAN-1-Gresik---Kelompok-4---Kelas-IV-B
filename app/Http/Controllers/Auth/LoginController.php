<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\ActivityHelper;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            // simpan cookie email kalau remember dicentang
            if ($remember) {
                Cookie::queue('login_email', $request->email, 60 * 24 * 30); // 30 hari
            } else {
                Cookie::queue(Cookie::forget('login_email'));
            }

            $user = Auth::user();

            ActivityHelper::log(
                $user->role === 'admin' ? 'Login Admin' : 'Login User',
                $user->nama_lengkap . ' berhasil login'
            );


            return $user->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah'])
            ->withInput();
    }
}