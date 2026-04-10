<?php
session_start();
require_once 'koneksi.php';

/*
|--------------------------------------------------------------------------
| AMBIL DATA USER LOGIN
|--------------------------------------------------------------------------
| Nanti idealnya pakai session login:
| $_SESSION['id_user']
*/

$id_user = $_SESSION['id_user'] ?? 0;

if ($id_user > 0) {
    $queryUser = mysqli_query($koneksi, "
        SELECT id_user, nama_lengkap, email, no_telp, alamat
        FROM user
        WHERE id_user = '$id_user' AND role = 'user'
        LIMIT 1
    ");
} else {
    // fallback sementara kalau session belum ada
    $queryUser = mysqli_query($koneksi, "
        SELECT id_user, nama_lengkap, email, no_telp, alamat
        FROM user
        WHERE role = 'user'
        ORDER BY id_user ASC
        LIMIT 1
    ");
}

$user = mysqli_fetch_assoc($queryUser);

/*
|--------------------------------------------------------------------------
| FUNGSI BUAT INISIAL 2 KATA PERTAMA
|--------------------------------------------------------------------------
| Contoh:
| Safira Choirun Nisa -> SC
*/
function getInitials($nama)
{
    $nama = trim($nama);
    if ($nama === '') return 'US';

    $parts = preg_split('/\s+/', $nama);
    $initials = '';

    for ($i = 0; $i < count($parts) && $i < 2; $i++) {
        $initials .= strtoupper(substr($parts[$i], 0, 1));
    }

    return $initials ?: 'US';
}

$userNama = $user['nama_lengkap'] ?? 'User';
$inisialUser = getInitials($userNama);

$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($inisialUser) . "&background=e2e8f0&color=1e3a8a&size=128";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Ruang Karya</title>

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
            <section class="grid grid-cols-1 gap-8 xl:grid-cols-[260px_1fr]">

                <!-- FOTO KIRI -->
                <div class="h-fit rounded-[24px] bg-white p-7 shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col items-center">
                        <div class="h-[150px] w-[150px] overflow-hidden rounded-full">
                            <img
                                src="<?= $avatarUrl; ?>"
                                alt="Foto Profil"
                                class="h-full w-full rounded-full object-cover"
                            >
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <div class="rounded-[24px] bg-white p-10 shadow-sm ring-1 ring-slate-200">
                    <form action="#" method="POST" class="space-y-8">

                        <div>
                            <label class="mb-4 block text-[15px] font-semibold text-blue-900">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="nama_lengkap"
                                value="<?= htmlspecialchars($user['nama_lengkap'] ?? ''); ?>"
                                class="h-[64px] w-full rounded-2xl border border-slate-300 bg-white px-6 text-[18px] outline-none transition focus:border-blue-900"
                            >
                        </div>

                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div>
                                <label class="mb-4 block text-[15px] font-semibold text-blue-900">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="<?= htmlspecialchars($user['email'] ?? ''); ?>"
                                    class="h-[64px] w-full rounded-2xl border border-slate-300 bg-white px-6 text-[18px] outline-none transition focus:border-blue-900"
                                >
                            </div>

                            <div>
                                <label class="mb-4 block text-[15px] font-semibold text-blue-900">
                                    No. Telepon
                                </label>
                                <input
                                    type="text"
                                    name="no_telp"
                                    value="<?= htmlspecialchars($user['no_telp'] ?? ''); ?>"
                                    class="h-[64px] w-full rounded-2xl border border-slate-300 bg-white px-6 text-[18px] outline-none transition focus:border-blue-900"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-4 block text-[15px] font-semibold text-blue-900">
                                Alamat
                            </label>
                            <textarea
                                name="alamat"
                                rows="7"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-6 py-5 text-[18px] outline-none transition focus:border-blue-900"
                            ><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
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
                            class="inline-flex h-[62px] min-w-[220px] items-center justify-center rounded-xl bg-yellow-500 px-10 text-[17px] font-semibold text-blue-900 shadow-sm transition hover:bg-yellow-400 hover:shadow-md"
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