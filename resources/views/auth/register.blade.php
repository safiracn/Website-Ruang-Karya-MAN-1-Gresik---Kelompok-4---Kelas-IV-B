<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Ruang Karya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

    <div class="relative z-10 w-full max-w-5xl">
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

                {{-- FORM REGISTER --}}
                <div class="order-2 bg-white px-6 py-8 sm:px-8 lg:order-1 lg:px-10">
                    <h2 class="text-4xl font-extrabold tracking-tight text-blue-900">Daftar</h2>
                    <p class="mt-2 text-sm text-slate-500">Buat akun baru untuk mulai menjelajahi karya dan produk kreatif.</p>

                    {{-- Error validasi dari Laravel --}}
                    @if($errors->any())
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Kontainer Pesan Error Real-time JavaScript --}}
                    <div id="js-error-msg" class="hidden mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    </div>

                    <form action="{{ route('register') }}" method="POST" id="registerForm" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                   placeholder="Masukkan nama lengkap" required oninput="validasiNama(this)"
                                   class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   placeholder="nama@email.com" required
                                   class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">No. Telepon</label>
                            <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}"
                                   placeholder="08xxxxxxxxxx" required oninput="validasiTelepon(this)"
                                   class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                            <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap" required
                                      class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900">{{ old('alamat') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Kata Sandi (Minimal 6 Karakter)</label>
                            <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                <input type="password" name="password" id="password" minlength="6"
                                       placeholder="Masukkan kata sandi" required
                                       class="w-full px-4 py-3 text-sm outline-none">
                                <button type="button" onclick="togglePassword('password', 'icon-pass')"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50">
                                    <i id="icon-pass" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Kata Sandi</label>
                            <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       placeholder="Ulangi kata sandi" required
                                       class="w-full px-4 py-3 text-sm outline-none">
                                <button type="button" onclick="togglePassword('password_confirmation', 'icon-confirm')"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50">
                                    <i id="icon-confirm" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full rounded-md bg-yellow-400 px-4 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300">
                            Daftar Sekarang
                        </button>

                        <div class="pt-1 text-center text-sm text-slate-500">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-bold text-red-600 hover:underline">Masuk di sini</a>
                        </div>
                    </form>
                </div>

                {{-- BRANDING KANAN --}}
                <div class="order-1 relative min-h-[320px] overflow-hidden bg-[#22306d] lg:order-2 lg:min-h-[680px]">
                    <img src="{{ asset('image/login.png') }}" alt="Brand Image"
                         class="absolute inset-0 h-full w-full object-cover opacity-25">
                    <div class="relative flex h-full flex-col justify-end p-8 text-white sm:p-10">
                        <h3 class="text-3xl font-extrabold leading-tight sm:text-5xl">Bergabung Bersama Ruang Karya</h3>
                        <p class="mt-4 max-w-md text-base leading-8 text-slate-200 sm:text-lg">
                            Buat akun untuk menikmati pengalaman belanja karya kreatif, kerajinan tangan, dan Ghost produk siswa terbaik.
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
    </div>
</div>

{{-- LOGIKA VALIDASI JAVASCRIPT --}}
<script>
    // 1. Validasi Input Nama (Hanya Huruf, Spasi, dan Tanda Petik Satu)
    function validasiNama(input) {
        // Regex ini akan menghapus angka dan karakter selain huruf, spasi, dan petik (')
        input.value = input.value.replace(/[^a-zA-Z\s']/g, '');
    }

    // 3. Validasi Input No Telepon (Hanya Angka)
    function validasiTelepon(input) {
        // Regex ini akan langsung menghapus karakter non-angka saat diketik
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    // Fitur Show/Hide Password
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    }

    // Validasi Final Saat Form akan Disubmit/Dikirim
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const errorBox = document.getElementById('js-error-msg');
        
        let errors = [];

        // 2. Validasi Struktur Email (Harus mengandung tanda '@' dan domain yang valid)
        if (!email.includes('@') || email.indexOf('@') === 0 || email.lastIndexOf('@') === email.length - 1) {
            errors.push("Format Email tidak valid! Pastikan menggunakan tanda '@' dengan benar.");
        }

        // 4a. Validasi Panjang Minimal Password
        if (password.length < 6) {
            errors.push("Kata Sandi minimal harus terdiri dari 6 karakter!");
        }

        // 4b. Validasi Kesamaan Password dan Konfirmasi Password
        if (password !== confirmPassword) {
            errors.push("Konfirmasi Kata Sandi tidak cocok! Pastikan isinya sama.");
        }

        // Jika ditemukan error, batalkan pengiriman form dan tampilkan pesan ke user
        if (errors.length > 0) {
            e.preventDefault(); // Mencegah form dikirim ke controller laravel
            errorBox.innerHTML = errors.join('<br>');
            errorBox.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll otomatis ke atas agar user membaca errornya
        } else {
            errorBox.classList.add('hidden');
        }
    });
</script>
</body>
</html>