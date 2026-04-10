<?php
session_start();
require_once '../php/koneksi.php';

/* ambil admin id 1 */
$queryAdmin = mysqli_query($koneksi, "
    SELECT id_user, nama_lengkap, email, no_telp, alamat
    FROM user
    WHERE id_user = 1 AND role = 'admin'
    LIMIT 1
");

$admin = mysqli_fetch_assoc($queryAdmin);

$activeMenu = 'profil';
$pageTitle = 'Profil';
$pageDesc = 'Kelola informasi akun admin Ruang Karya MAN 1 Gresik.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Ruang Karya</title>

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

<?php include 'header_admin.php'; ?>

<section class="grid grid-cols-1 gap-8 xl:grid-cols-[240px_1fr]">

    <!-- CARD FOTO -->
    <div class="h-fit rounded-[24px] bg-white p-7 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col items-center">
            <div class="relative">
                <div class="flex h-[112px] w-[112px] items-center justify-center overflow-hidden rounded-full ring-4 ring-slate-200">
                    <img
                        src="<?= $avatarUrl; ?>"
                        alt="Foto Profil"
                        class="h-full w-full object-cover rounded-full"
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- CARD FORM -->
    <div class="rounded-[24px] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <form action="#" method="POST" class="space-y-7">

            <div>
                <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    name="nama_lengkap"
                    value="<?= htmlspecialchars($admin['nama_lengkap'] ?? ''); ?>"
                    class="h-[56px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
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
                        value="<?= htmlspecialchars($admin['email'] ?? ''); ?>"
                        class="h-[56px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
                    >
                </div>

                <div>
                    <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                        No. Telepon
                    </label>
                    <input
                        type="text"
                        name="no_telp"
                        value="<?= htmlspecialchars($admin['no_telp'] ?? ''); ?>"
                        class="h-[56px] w-full rounded-xl border border-slate-300 bg-white px-5 text-[16px] outline-none transition focus:border-blue-900"
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
                ><?= htmlspecialchars($admin['alamat'] ?? ''); ?></textarea>
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

    </main>
</div>

</body>
</html>