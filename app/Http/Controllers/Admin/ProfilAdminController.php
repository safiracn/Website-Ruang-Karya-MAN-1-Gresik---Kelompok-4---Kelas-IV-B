<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilAdminController extends Controller
{
    public function index()
    {
        $admin = Auth::user();

        return view('admin.profil', compact('admin'));
    }
}