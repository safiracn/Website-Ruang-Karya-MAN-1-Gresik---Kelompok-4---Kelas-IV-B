{{-- 
    Layout utama untuk halaman user/publik.
    Menggantikan: header.php dan footer.php
    
    Penggunaan di view lain:
    @extends('layouts.app')
    @section('title', 'Judul Halaman')
    @section('content')
        ... isi halaman ...
    @endsection
--}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Ruang Karya MAN 1 Gresik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- CSS Tailwind (build dari input.css) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        :root {
            --font-serif-heading: "Cambria", serif;
            --font-sans-body: "Inter", sans-serif;
        }
        .font-serif-heading { font-family: var(--font-serif-heading); }
        .font-sans-body { font-family: var(--font-sans-body); }
    </style>
    
    @stack('styles')
</head>

<body class="font-sans-body bg-slate-50 text-slate-800">

    {{-- ============================================================ --}}
    {{-- TOP BAR (kontak) — dari header.php                          --}}
    {{-- ============================================================ --}}
    <div class="w-full rounded-b-[28px] bg-blue-900 text-white text-sm shadow-sm">
        <div class="mx-auto flex flex-col md:flex-row items-center justify-between max-w-7xl px-6 py-3 gap-2">

            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-xs"></i> +031-3949544
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-xs"></i> mangresik@kemenag.go.id
                </span>
            </div>

            <div class="flex gap-2 items-center">
                @guest
                    {{-- Tamu: tampilkan tombol Daftar & Masuk --}}
                    <a href="{{ route('register') }}" class="rounded-lg bg-yellow-500 px-4 py-1.5 font-bold text-blue-900 transition hover:bg-yellow-400">
                        Daftar
                    </a>
                    <a href="{{ route('login') }}" class="rounded-lg bg-yellow-500 px-4 py-1.5 font-bold text-blue-900 transition hover:bg-yellow-400">
                        Masuk
                    </a>
                @endguest

                @auth
                    {{-- User login: tampilkan sapaan --}}
                    <span class="font-semibold italic text-yellow-500">
                        Halo, <b>{{ Auth::user()->nama_lengkap }}</b>
                    </span>
                @endauth
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- NAVBAR UTAMA — dari header.php                              --}}
    {{-- ============================================================ --}}
    <header class="bg-white sticky top-0 z-40 shadow-sm border-b border-slate-100">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            {{-- Logo --}}
            <div class="flex items-center gap-4">
                <img src="{{ asset('image/logo man.png') }}" alt="Logo" class="h-12 w-auto md:h-16" />
                <div>
                    <h1 class="font-serif-heading text-xl md:text-3xl font-bold leading-tight text-blue-900">Ruang Karya</h1>
                    <h2 class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</h2>
                    <p class="hidden md:block mt-1 text-xs italic text-blue-800">
                        Islami, Cerdas, Unggul, Kompetitif, & Peduli Lingkungan
                    </p>
                </div>
            </div>

            {{-- Nav Links --}}
            <nav class="hidden lg:block">
                <ul class="flex items-center gap-8 font-bold">
                    <li>
                        <a href="{{ route('home') }}"
                           class="{{ request()->routeIs('home') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' }}">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('katalog') }}"
                           class="{{ request()->routeIs('katalog') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' }}">
                            Katalog
                        </a>
                    </li>
                    <li>
                        {{-- Riwayat: jika belum login → arahkan ke login --}}
                        <a href="
{{
    Auth::check()
        ? (Auth::user()->role === 'user'
            ? route('riwayat')
            : route('admin.dashboard'))
        : route('login')
}}
"
                           class="{{ request()->routeIs('riwayat') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' }}">
                            Riwayat
                        </a>
                    </li>
                    <li class="relative">
                        {{-- Keranjang: jika belum login → arahkan ke login --}}
                        <a href="
{{
    Auth::check()
        ? (Auth::user()->role === 'user'
            ? route('keranjang')
            : route('admin.dashboard'))
        : route('login')
}}
"
                           class="{{ request()->routeIs('keranjang') ? 'text-yellow-500' : 'text-blue-900 hover:text-yellow-500' }} transition p-2">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                        </a>
                    </li>
                    <li>
                        <a href="
{{ 
    Auth::check()
        ? (Auth::user()->role === 'admin'
            ? route('admin.dashboard')
            : route('profil.user'))
        : route('login')
}}
"
                           class="flex items-center gap-3 group pl-4 border-l border-slate-200">
                            <div class="text-right hidden xl:block">
                                <p class="text-[9px] text-slate-400 font-bold uppercase leading-none">Akun Saya</p>
                                <p class="text-sm text-blue-900 group-hover:text-yellow-500 transition">
                                    {{ Auth::check() ? Auth::user()->nama_lengkap : 'Guest' }}
                                </p>
                            </div>
                            <div class="h-10 w-10 rounded-full border-2 transition shadow-sm flex items-center justify-center font-black
                                {{ request()->routeIs('profil.user') ? 'border-yellow-500 bg-yellow-500 text-blue-900' : 'border-blue-900 bg-blue-900 text-white group-hover:bg-yellow-500 group-hover:border-yellow-500 group-hover:text-blue-900' }}">
                                @auth
                                    {{-- Ambil 2 huruf awal nama --}}
                                    {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}{{ strtoupper(substr(strstr(Auth::user()->nama_lengkap . ' ', ' '), 1, 1)) }}
                                @else
                                    G
                                @endauth
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
    </header>

    {{-- ============================================================ --}}
    {{-- KONTEN HALAMAN                                               --}}
    {{-- ============================================================ --}}
    <main>
        @yield('content')
    </main>

    {{-- ============================================================ --}}
    {{-- FOOTER — dari footer.php                                     --}}
    {{-- ============================================================ --}}
    <footer class="bg-gradient-to-b from-blue-800 to-blue-950 text-white mt-20 rounded-t-[40px]">
        <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-16 px-8 py-16 items-start">

            {{-- Kolom 1: Logo & Sosmed --}}
            <div class="space-y-5">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('image/logo man.png') }}" class="h-16 w-auto" alt="Logo MAN 1 Gresik">
                    <div>
                        <h3 class="font-bold text-xl tracking-wide text-yellow-400">MAN 1 GRESIK</h3>
                        <p class="text-sm text-blue-200">Madrasah Aliyah Negeri 1 Gresik</p>
                    </div>
                </div>
                <p class="text-sm italic text-blue-300 leading-relaxed">
                    "Islami, Cerdas, Unggul, Kompetitif & Peduli Lingkungan"
                </p>
                <div class="flex gap-4 pt-2">
                    <a href="https://web.facebook.com/man1gresik" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-400 hover:text-black transition duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/man1gresik?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-400 hover:text-black transition duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="http://www.youtube.com/@M1GTVMAN1Gresik" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-400 hover:text-black transition duration-300">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            {{-- Kolom 2: Kontak --}}
            <div class="space-y-6">
                <h3 class="font-semibold text-lg border-b border-yellow-400 inline-block pb-1">Kontak Kami</h3>
                <div class="space-y-4 text-sm text-blue-200">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-yellow-400 mt-1"></i>
                        <span>Jl. Raya Bungah No 46 Bungah Gresik, 61152</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-yellow-400"></i>
                        <span>+031 3949544</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-yellow-400"></i>
                        <span>mangresik@kemenag.go.id</span>
                    </div>
                </div>
            </div>

            {{-- Kolom 3: Maps --}}
            <div class="rounded-2xl overflow-hidden shadow-xl ring-1 ring-white/20 hover:scale-[1.02] transition duration-300">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.718361718018!2d112.56943897455088!3d-7.042335192959648!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77f0f62b667e41%3A0x4642055660851ec0!2sMAN%201%20Gresik!5e0!3m2!1sid!2sid!4v1715000000000!5m2!1sid!2sid"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>

        </div>

        <div class="border-t border-white/10 bg-blue-950/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center py-5 px-8 text-sm text-blue-300 gap-3">
                <p>© 2026 <span class="font-semibold text-white">Ruang Karya MAN 1 Gresik</span></p>
                <p class="text-xs text-blue-400">All rights reserved</p>
            </div>
        </div>

        {{-- WhatsApp Float Button --}}
        <a href="https://wa.me/6285859249749"
           class="fixed bottom-8 left-8 bg-green-500 w-16 h-16 flex items-center justify-center rounded-full shadow-xl hover:scale-110 transition-all duration-300 z-50 group">
            <i class="fab fa-whatsapp text-white text-2xl"></i>
            <span class="absolute left-20 flex items-center bg-white text-gray-800 text-sm font-medium px-4 py-2 rounded-xl shadow-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
                Chat Admin
                <span class="absolute -left-1 w-4 h-4 bg-white rotate-45"></span>
            </span>
        </a>
    </footer>

    @stack('scripts')
</body>
</html>