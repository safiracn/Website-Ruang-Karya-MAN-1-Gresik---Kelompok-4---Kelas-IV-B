<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: gabungan.php");
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$old_nama = $_GET['nama'] ?? '';
$old_email = $_GET['email'] ?? '';
$old_telp = $_GET['telp'] ?? '';
$old_alamat = $_GET['alamat'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Ruang Karya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[#f3f4f6] font-jakarta text-slate-900">

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute inset-0 opacity-[0.08]">
            <div class="absolute bottom-0 left-0 h-[360px] w-[360px] rounded-full border-[40px] border-slate-400"></div>
            <div class="absolute bottom-[-80px] left-[180px] h-[300px] w-[300px] border-l-[6px] border-t-[6px] border-slate-400"></div>
        </div>

        <div class="relative z-10 w-full max-w-5xl">
            <!-- logo atas -->
            <div class="mb-6 text-center">
                <div class="inline-flex items-center gap-3">
                    <img src="../images/logo.png" alt="Logo" class="h-14 w-14 object-contain">
                    <div class="text-left">
                        <h1 class="text-xl md:text-3xl font-serif-heading font-bold text-blue-900">Ruang Karya</h1>
                        <p class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</p>
                    </div>
                </div>
            </div>

            <!-- card utama -->
            <div class="overflow-hidden rounded-sm border border-slate-300 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.10)]">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.15fr]">

                    <!-- kiri form -->
                    <div class="order-2 bg-white px-6 py-8 sm:px-8 lg:order-1 lg:px-10">
                        <h2 class="text-4xl font-extrabold tracking-tight text-blue-900">Daftar</h2>
                        <p class="mt-2 text-sm text-slate-500">
                            Buat akun baru untuk mulai menjelajahi karya dan produk kreatif.
                        </p>

                        <?php if ($error === 'empty'): ?>
                            <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                                Semua field wajib diisi.
                            </div>
                        <?php elseif ($error === 'email_exists'): ?>
                            <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                                Email sudah terdaftar. Gunakan email lain.
                            </div>
                        <?php elseif ($error === 'password_mismatch'): ?>
                            <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                                Konfirmasi password tidak sama.
                            </div>
                        <?php elseif ($error === 'db'): ?>
                            <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                                Gagal menyimpan data. Coba lagi.
                            </div>
                        <?php elseif ($success === '1'): ?>
                            <div class="mt-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                                Pendaftaran berhasil. Silakan login.
                            </div>
                        <?php endif; ?>

                        <form action="proses_daftar.php" method="POST" class="mt-6 space-y-4" id="formRegister">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                                <input
                                    type="text"
                                    name="nama_lengkap"
                                    id="nama_lengkap"
                                    value="<?= htmlspecialchars($old_nama) ?>"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                    class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="<?= htmlspecialchars($old_email) ?>"
                                    placeholder="nama@email.com"
                                    required
                                    class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">No. Telepon</label>
                                <input
                                    type="text"
                                    name="no_telp"
                                    id="no_telp"
                                    value="<?= htmlspecialchars($old_telp) ?>"
                                    placeholder="08xxxxxxxxxx"
                                    required
                                    class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Alamat</label>
                                <textarea
                                    name="alamat"
                                    id="alamat"
                                    rows="3"
                                    placeholder="Masukkan alamat lengkap"
                                    required
                                    class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900"
                                ><?= htmlspecialchars($old_alamat) ?></textarea>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Kata Sandi</label>
                                <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        placeholder="Masukkan kata sandi"
                                        required
                                        class="w-full px-4 py-3 text-sm outline-none"
                                    >
                                    <button
                                        type="button"
                                        onclick="togglePassword('password')"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50"
                                    >
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi</label>
                                <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                    <input
                                        type="password"
                                        name="konfirmasi_password"
                                        id="konfirmasi_password"
                                        placeholder="Ulangi kata sandi"
                                        required
                                        class="w-full px-4 py-3 text-sm outline-none"
                                    >
                                    <button
                                        type="button"
                                        onclick="togglePassword('konfirmasi_password')"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50"
                                    >
                                        👁
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                name="submit"
                                class="w-full rounded-md bg-yellow-400 px-4 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300"
                            >
                                Daftar Sekarang
                            </button>

                            <div class="pt-1 text-center text-sm text-slate-500">
                                Sudah punya akun?
                                <a href="login.php" class="font-bold text-blue-900 hover:underline">Masuk di sini</a>
                            </div>
                        </form>
                    </div>

                    <!-- kanan branding -->
                    <div class="order-1 relative min-h-[320px] overflow-hidden bg-[#22306d] lg:order-2 lg:min-h-[680px]">
                        <img
                            src="../images/login.png"
                            alt="Brand Image"
                            class="absolute inset-0 h-full w-full object-cover opacity-25"
                        >

                        <svg class="absolute inset-0 h-full w-full opacity-60" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 120C160 120 180 420 360 420C500 420 560 180 800 180" stroke="#f4c430" stroke-width="2"/>
                            <path d="M0 140C170 140 190 440 370 440C510 440 570 200 800 200" stroke="#f4c430" stroke-width="2"/>
                            <path d="M0 160C180 160 200 460 380 460C520 460 580 220 800 220" stroke="#f4c430" stroke-width="2"/>
                            <path d="M0 180C190 180 210 480 390 480C530 480 590 240 800 240" stroke="#f4c430" stroke-width="2"/>
                            <path d="M0 200C200 200 220 500 400 500C540 500 600 260 800 260" stroke="#f4c430" stroke-width="2"/>
                        </svg>

                        <div class="relative flex h-full flex-col justify-end p-8 text-white sm:p-10">
                            <h3 class="text-3xl font-extrabold leading-tight sm:text-5xl">
                                Bergabung Bersama Ruang Karya
                            </h3>

                            <p class="mt-4 max-w-md text-base leading-8 text-slate-200 sm:text-lg">
                                Buat akun untuk menikmati pengalaman belanja karya kreatif, kerajinan tangan, dan produk siswa terbaik.
                            </p>

                            <div class="mt-8">
                                <a
                                    href="gabungan.php"
                                    class="inline-flex rounded-xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300"
                                >
                                    Lihat katalog karya
                                </a>
                            </div>

                            <p class="mt-10 text-xs text-slate-300">
                                Copyright © 2026 Ruang Karya MAN 1 Gresik
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- footer bawah -->
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
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        const formRegister = document.getElementById('formRegister');
        const namaInput = document.getElementById('nama_lengkap');
        const emailInput = document.getElementById('email');
        const telpInput = document.getElementById('no_telp');
        const passwordInput = document.getElementById('password');
        const konfirmasiPasswordInput = document.getElementById('konfirmasi_password');

        namaInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Z'`.\s]/g, '');
        });

        telpInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        formRegister.addEventListener('submit', function (e) {
            const nama = namaInput.value.trim();
            const email = emailInput.value.trim();
            const telp = telpInput.value.trim();
            const password = passwordInput.value.trim();
            const konfirmasiPassword = konfirmasiPasswordInput.value.trim();

            const regexNama = /^[a-zA-Z'`.\s]+$/;
            const regexTelp = /^[0-9]+$/;
            const regexEmail = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;

            if (nama === '' || email === '' || telp === '' || password === '' || konfirmasiPassword === '') {
                e.preventDefault();
                alert('Semua field wajib diisi.');
                return;
            }

            if (!regexNama.test(nama)) {
                e.preventDefault();
                alert("Nama hanya boleh huruf, spasi, tanda petik satu ('), titik, dan backtick (`).");
                namaInput.focus();
                return;
            }

            if (!regexEmail.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid.');
                emailInput.focus();
                return;
            }

            if (!regexTelp.test(telp)) {
                e.preventDefault();
                alert('Nomor telepon hanya boleh angka.');
                telpInput.focus();
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter.');
                passwordInput.focus();
                return;
            }

            if (password !== konfirmasiPassword) {
                e.preventDefault();
                alert('Konfirmasi password tidak sama.');
                konfirmasiPasswordInput.focus();
            }
        });
    </script>
</body>
</html>