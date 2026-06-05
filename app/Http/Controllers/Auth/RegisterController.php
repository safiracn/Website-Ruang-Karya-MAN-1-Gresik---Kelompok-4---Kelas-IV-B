<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap'        => ["required", "regex:/^[a-zA-Z'`.\s]+$/"],
            'email'               => 'required|email|unique:users,email',
            'no_telp'             => ['required', 'regex:/^[0-9]+$/'],
            'alamat'              => 'required',
            'password'            => 'required|min:6|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.regex'    => 'Nama hanya boleh huruf, spasi, tanda petik, titik.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar. Gunakan email lain.',
            'no_telp.required'      => 'No. telepon wajib diisi.',
            'no_telp.regex'         => 'No. telepon hanya boleh angka.',
            'alamat.required'       => 'Alamat wajib diisi.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak sama.',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'no_telp'      => $request->no_telp,
            'alamat'       => $request->alamat,
            'password'     => Hash::make($request->password),
            'role'         => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
    }
}