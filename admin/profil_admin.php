<?php
session_start();
require_once '../php/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../php/login.php");
    exit;
}

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

/* avatar admin */
$adminNama = $admin['nama_lengkap'] ?? 'Admin';
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($adminNama) . "&background=e2e8f0&color=1e3a8a&size=128";
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
                        class="h-full w-full rounded-full object-cover"
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- CARD INFORMASI -->
    <div class="rounded-[24px] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <div class="space-y-7">

            <div>
                <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($admin['nama_lengkap'] ?? ''); ?>"
                    readonly
                    class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none"
                >
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                        Email
                    </label>
                    <input
                        type="email"
                        value="<?= htmlspecialchars($admin['email'] ?? ''); ?>"
                        readonly
                        class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none"
                    >
                </div>

                <div>
                    <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                        No. Telepon
                    </label>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($admin['no_telp'] ?? ''); ?>"
                        readonly
                        class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none"
                    >
                </div>
            </div>

            <div>
                <label class="mb-3 block text-[15px] font-semibold text-blue-900">
                    Alamat
                </label>
                <textarea
                    rows="5"
                    readonly
                    class="w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 py-4 text-[16px] text-slate-700 outline-none resize-none"
                ><?= htmlspecialchars($admin['alamat'] ?? ''); ?></textarea>
            </div>

        </div>
    </div>

</section>

    </main>
</div>

</body>
</html>