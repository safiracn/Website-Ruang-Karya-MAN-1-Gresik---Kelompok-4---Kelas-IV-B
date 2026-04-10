<?php
session_start();
require_once 'koneksi.php';

$nama_lengkap = $_SESSION['nama_lengkap'] ?? "Safira Nisa'";
$email        = $_SESSION['email'] ?? "firafizua@gmail.com";
$no_telp      = $_SESSION['no_telp'] ?? "085859249749";
$alamat       = $_SESSION['alamat'] ?? "Pagerngumbuk Rt.03 Rw.01, Kec. wonoayu Kab. Sidoarjo Jawa Timur 61261";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>

    <link rel="stylesheet" href="../RuangKaryaCSS/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --font-serif-heading: "Cambria", serif;
            --font-sans-body: "Inter", sans-serif;
        }

        .font-serif-heading { font-family: var(--font-serif-heading); }
        .font-sans-body { font-family: var(--font-sans-body); }
    </style>
</head>
<body class="font-sans-body bg-slate-100 text-slate-800">

    <!-- Space header -->
    <div class="h-[135px]"></div>

    <main class="px-6 pb-16">
        <div class="mx-auto max-w-[1280px]">

            <!-- Heading + logout -->
            <section class="mb-8 flex items-start justify-between gap-6">
                <div>
                    <h1 class="text-[46px] font-extrabold leading-none text-blue-900">
                        Profil Pengguna
                    </h1>
                    <p class="mt-3 text-[17px] text-slate-500">
                        Sesuaikan informasi publik dan pengaturan akun Anda di Ruang Karya MAN 1 Gresik.
                    </p>
                </div>

                <a
                    href="logout.php"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-[15px] font-semibold text-red-500 shadow-sm transition hover:bg-red-50"
                >
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </a>
            </section>

            <!-- Content -->
            <section class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">

                <!-- Card Foto -->
                <div class="rounded-[24px] bg-white p-7 shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col items-center">
                        <div class="flex h-[128px] w-[128px] items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-4 ring-slate-200">
                            <img
                                src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"
                                alt="Foto Profil"
                                class="h-[118px] w-[118px] object-cover"
                            >
                        </div>

                        <button
                            type="button"
                            class="mt-7 inline-flex h-[52px] w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-[15px] font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Ganti Foto
                        </button>
                    </div>
                </div>

                <!-- Card Form -->
                <div class="rounded-[24px] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <form action="#" method="POST" class="space-y-7">

                        <div>
                            <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="nama_lengkap"
                                value="<?= htmlspecialchars($nama_lengkap); ?>"
                                class="h-[58px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
                            >
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="<?= htmlspecialchars($email); ?>"
                                    class="h-[58px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                                    No. Telepon
                                </label>
                                <input
                                    type="text"
                                    name="no_telp"
                                    value="<?= htmlspecialchars($no_telp); ?>"
                                    class="h-[58px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                                Alamat
                            </label>
                            <textarea
                                name="alamat"
                                rows="5"
                                class="w-full rounded-xl border border-slate-300 bg-white px-5 py-4 text-[16px] outline-none transition focus:border-blue-900"
                            ><?= htmlspecialchars($alamat); ?></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-8 pt-2">
                            <button
                                type="button"
                                class="text-[16px] font-semibold text-blue-900 transition hover:text-blue-700"
                            >
                                Batalkan
                            </button>

                            <button
                                type="submit"
                                class="inline-flex h-[56px] items-center justify-center rounded-xl bg-yellow-500 px-8 text-[16px] font-semibold text-blue-900 shadow-sm transition hover:bg-yellow-400 hover:shadow-md"
                            >
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>

            </section>
        </div>
    </main>

</body>
</html>