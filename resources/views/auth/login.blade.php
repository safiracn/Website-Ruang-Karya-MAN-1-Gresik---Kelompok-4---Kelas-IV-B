<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ruang Karya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { jakarta: ['"Plus Jakarta Sans"', 'sans-serif'] } } }
        }
    </script>
    <style>
        :root {
            --font-serif-heading: "Cambria", serif;
        }
        .font-serif-heading { font-family: var(--font-serif-heading); }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-[#f3f4f6] font-jakarta text-slate-900">

<div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
    <div class="pointer-events-none absolute inset-0 opacity-[0.08]">
        <div class="absolute bottom-0 left-0 h-[360px] w-[360px] rounded-full border-[40px] border-slate-400"></div>
        <div class="absolute bottom-[-80px] left-[180px] h-[300px] w-[300px] border-l-[6px] border-t-[6px] border-slate-400"></div>
    </div>

    <div class="relative z-10 w-full max-w-4xl">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center gap-3">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-14 w-14 object-contain">
                <div class="text-left">
                    <h1 class="font-serif-heading text-xl md:text-3xl font-bold text-blue-900">Ruang Karya</h1>
                    <h2 class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</h2>
                    <p class="hidden md:block mt-1 text-xs italic text-blue-800">
                        Islami, Cerdas, Unggul, Kompetitif, & Peduli Lingkungan
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-sm border border-slate-300 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.10)]">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.15fr]">

                {{-- FORM LOGIN --}}
                <div class="bg-white px-8 py-8 sm:px-10">
                    <h2 class="text-4xl font-extrabold tracking-tight text-blue-900">Login</h2>

                    {{-- Pesan sukses dari register --}}
                    @if(session('success'))
                        <div class="mt-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Pesan error login --}}
                    @if($errors->any())
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email"
                                name="email"
                                value="{{ old('email', request()->cookie('login_email')) }}"
                                placeholder="nama@email.com"
                                required
                                class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Kata Sandi</label>
                            <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                <input type="password" name="password" id="password"
                                       placeholder="Masukkan kata sandi Anda" required
                                       class="w-full px-4 py-3 text-sm outline-none">
                                <button type="button" onclick="togglePassword()"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50">
                                    👁
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-900">
                                Remember Me
                            </label>
                            <a href="#" class="text-sm font-semibold text-blue-900 hover:underline">Lupa Kata Sandi?</a>
                        </div>

                        <button type="submit"
                                class="w-full rounded-md bg-yellow-400 px-4 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300">
                            Login
                        </button>

                        <div class="pt-1 text-center text-sm text-slate-500">
                            Pengguna Baru?
                            <a href="{{ route('register') }}" class="font-bold text-red-600 hover:underline">Daftar di sini</a>
                        </div>
                    </form>
                </div>

                {{-- BRANDING KANAN --}}
                <div class="relative min-h-[520px] overflow-hidden bg-[#22306d]">
                    <img src="{{ asset('image/login.png') }}" alt="Brand Image"
                         class="absolute inset-0 h-full w-full object-cover opacity-25">
                    <svg class="absolute inset-0 h-full w-full opacity-60" viewBox="0 0 800 600" fill="none">
                        <path d="M0 120C160 120 180 420 360 420C500 420 560 180 800 180" stroke="#f4c430" stroke-width="2"/>
                        <path d="M0 140C170 140 190 440 370 440C510 440 570 200 800 200" stroke="#f4c430" stroke-width="2"/>
                        <path d="M0 160C180 160 200 460 380 460C520 460 580 220 800 220" stroke="#f4c430" stroke-width="2"/>
                        <path d="M0 180C190 180 210 480 390 480C530 480 590 240 800 240" stroke="#f4c430" stroke-width="2"/>
                        <path d="M0 200C200 200 220 500 400 500C540 500 600 260 800 260" stroke="#f4c430" stroke-width="2"/>
                    </svg>
                    <div class="relative flex h-full flex-col justify-end p-8 text-white sm:p-10">
                        <h3 class="text-4xl font-extrabold leading-tight sm:text-5xl">Ruang Karya</h3>
                        <p class="mt-4 max-w-md text-lg leading-8 text-slate-200">
                            Jelajahi karya siswa, produk kreatif, dan hasil kerajinan terbaik dari MAN 1 Gresik.
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('katalog') }}"
                               class="inline-flex rounded-xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300">
                                Lihat katalog karya
                            </a>
                        </div>
                        <p class="mt-10 text-xs text-slate-300">Copyright © 2026 Ruang Karya MAN 1 Gresik</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-500">
            <span class="font-semibold text-blue-900">MAN 1 Gresik</span>
            <span>•</span>
            <span>Galeri Karya Siswa</span>
            <span>•</span>
            <span>Marketplace Kreatif</span>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>