<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: dashboardPembeli.php");
    }
    exit;
}

$saved_email = "";
if (isset($_COOKIE['remember_email'])) {
    $saved_email = $_COOKIE['remember_email'];
}

$error = "";
$success = $_GET['success'] ?? '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email === "" || $password === "") {
        $error = "Email dan password wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        $email = mysqli_real_escape_string($koneksi, $email);
        $query = "SELECT * FROM user WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Pakai password_verify kalau password disimpan dengan password_hash
            $login_valid = false;

            if (password_verify($password, $row['password'])) {
                $login_valid = true;
            } elseif ($password === $row['password']) {
                $login_valid = true;
            }

            if ($login_valid) {
                $_SESSION['login'] = true;
                $_SESSION['id_user'] = $row['id_user'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];

                if (isset($_POST['remember'])) {
                    setcookie("remember_email", $row['email'], time() + 86400, "/");
                } else {
                    setcookie("remember_email", "", time() - 3600, "/");
                }

                if ($row['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: dashboardPembeli.php");
                }
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ruang Karya</title>
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

        <div class="relative z-10 w-full max-w-4xl">
            <div class="mb-6 text-center">
                <div class="inline-flex items-center gap-3">
                    <img src="../images/logo.png" alt="Logo" class="h-14 w-14 object-contain">
                    <div class="text-left">
                        <h1 class="text-xl md:text-3xl font-serif-heading font-bold text-blue-900">Ruang Karya</h1>
                        <p class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-sm border border-slate-300 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.10)]">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.15fr]">

                    <div class="bg-white px-8 py-8 sm:px-10">
                        <h2 class="text-4xl font-extrabold tracking-tight text-blue-900">Login</h2>

                        <?php if ($success === 'register'): ?>
                            <div class="mt-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                                Pendaftaran berhasil. Silakan login.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" class="mt-6 space-y-5" id="formLogin">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="<?= htmlspecialchars($saved_email) ?>"
                                    placeholder="nama@email.com"
                                    required
                                    class="w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Kata Sandi</label>
                                <div class="flex overflow-hidden rounded-md border border-slate-300 focus-within:border-blue-900">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        placeholder="Masukkan kata sandi Anda"
                                        required
                                        class="w-full px-4 py-3 text-sm outline-none"
                                    >
                                    <button
                                        type="button"
                                        onclick="togglePassword()"
                                        class="flex w-12 items-center justify-center border-l border-slate-300 text-slate-500 hover:bg-slate-50"
                                    >
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-900 focus:ring-blue-900">
                                    Remember Me
                                </label>

                                <a href="#" class="text-sm font-semibold text-blue-900 hover:underline">
                                    Lupa Kata Sandi?
                                </a>
                            </div>

                            <button
                                type="submit"
                                name="submit"
                                class="w-full rounded-md bg-yellow-400 px-4 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-yellow-300"
                            >
                                Login
                            </button>

                            <div class="space-y-2 pt-1 text-center text-sm text-slate-500">
                                <p>
                                    Pengguna Baru?
                                    <a href="Daftar.php" class="font-bold text-red-600 hover:underline">Daftar di sini</a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <div class="relative min-h-[520px] overflow-hidden bg-[#22306d]">
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
                            <h3 class="text-4xl font-extrabold leading-tight sm:text-5xl">
                                Ruang Karya
                            </h3>

                            <p class="mt-4 max-w-md text-lg leading-8 text-slate-200">
                                Jelajahi karya siswa, produk kreatif, dan hasil kerajinan terbaik dari MAN 1 Gresik.
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

        const formLogin = document.getElementById('formLogin');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        formLogin.addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (email === '' || password === '') {
                e.preventDefault();
                alert('Email dan password wajib diisi.');
                return;
            }

            const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
            if (!emailPattern.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid.');
                emailInput.focus();
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter.');
                passwordInput.focus();
                return;
            }
        });
    </script>
</body>
</html>