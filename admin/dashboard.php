<?php
session_start();
require_once '../php/koneksi.php';

$queryAdmin = mysqli_query($koneksi, "
    SELECT id_user, nama_lengkap, email, no_telp, alamat
    FROM user
    WHERE id_user = 1 AND role = 'admin'
    LIMIT 1
");
$admin = mysqli_fetch_assoc($queryAdmin);

$activeMenu = 'dashboard';
$pageTitle = 'Daftar Produk';
$pageDesc = 'Kelola dan pantau semua inventaris produk karya siswa Anda.';

/*
|--------------------------------------------------------------------------
| DASHBOARD STATS
|--------------------------------------------------------------------------
*/

// Total produk
$qTotalProduk = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_produk
    FROM produk
    WHERE id_produk BETWEEN 1 AND 8
");
$totalProduk = mysqli_fetch_assoc($qTotalProduk)['total_produk'] ?? 0;

// Produk yang stok totalnya > 0
$qStokTersedia = mysqli_query($koneksi, "
    SELECT COUNT(*) AS stok_tersedia
    FROM (
        SELECT p.id_produk, COALESCE(SUM(v.stok), 0) AS total_stok
        FROM produk p
        LEFT JOIN produk_varian v ON p.id_produk = v.id_produk
        WHERE p.id_produk BETWEEN 1 AND 8
        GROUP BY p.id_produk
        HAVING COALESCE(SUM(v.stok), 0) > 0
    ) AS stok_ready
");
$stokTersedia = mysqli_fetch_assoc($qStokTersedia)['stok_tersedia'] ?? 0;

// Produk yang stok totalnya = 0
$qStokHabis = mysqli_query($koneksi, "
    SELECT COUNT(*) AS stok_habis
    FROM (
        SELECT p.id_produk, COALESCE(SUM(v.stok), 0) AS total_stok
        FROM produk p
        LEFT JOIN produk_varian v ON p.id_produk = v.id_produk
        WHERE p.id_produk BETWEEN 1 AND 8
        GROUP BY p.id_produk
        HAVING COALESCE(SUM(v.stok), 0) = 0
    ) AS stok_kosong
");
$stokHabis = mysqli_fetch_assoc($qStokHabis)['stok_habis'] ?? 0;

/*
|--------------------------------------------------------------------------
| DATA PRODUK
|--------------------------------------------------------------------------
*/
$qProduk = mysqli_query($koneksi, "
    SELECT 
        p.id_produk,
        p.nama_produk,
        p.foto_produk,
        k.nama_kategori,
        MIN(v.harga) AS harga,
        COALESCE(SUM(v.stok), 0) AS stok,
        GROUP_CONCAT(
            CONCAT(v.nama_varian, ':', v.stok)
            ORDER BY v.id_varian ASC
            SEPARATOR ' • '
        ) AS detail_varian
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    LEFT JOIN produk_varian v ON p.id_produk = v.id_produk
    WHERE p.id_produk BETWEEN 1 AND 8
    GROUP BY p.id_produk, p.nama_produk, p.foto_produk, k.nama_kategori
    ORDER BY p.id_produk ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruang Karya</title>

    <link rel="stylesheet" href="../RuangKaryaCSS/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --font-serif-heading: "Cambria", serif;
            --font-sans-body: "Inter", sans-serif;
        }

        .font-serif-heading {
            font-family: var(--font-serif-heading);
        }

        .font-sans-body {
            font-family: var(--font-sans-body);
        }
    </style>
</head>
<body class="font-sans-body bg-slate-100 text-slate-800">

<?php include 'header_admin.php'; ?>

<!-- CARD STATISTIK -->
<section class="mb-7 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="flex items-center justify-between rounded-2xl border-l-4 border-blue-900 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Total Produk</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900"><?= $totalProduk; ?></h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-900">
            <i class="fa-solid fa-box-archive text-xl"></i>
        </div>
    </div>

    <div class="flex items-center justify-between rounded-2xl border-l-4 border-green-500 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Stok Tersedia</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900"><?= $stokTersedia; ?></h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
            <i class="fa-solid fa-circle-check text-xl"></i>
        </div>
    </div>

    <div class="flex items-center justify-between rounded-2xl border-l-4 border-red-500 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Stok Habis</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900"><?= $stokHabis; ?></h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-500">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
        </div>
    </div>
</section>

<!-- TABEL -->
<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div class="group flex h-12 w-[360px] items-center gap-3 rounded-xl bg-slate-100 px-4 transition hover:bg-slate-200">
            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
            <input
                type="text"
                placeholder="Cari nama produk..."
                class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
            >
        </div>

        <button class="inline-flex h-14 items-center gap-2 rounded-2xl bg-yellow-500 px-6 text-[15px] font-semibold text-blue-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-yellow-400 hover:shadow-md">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Tambah Produk</span>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-fixed border-collapse">
            <thead>
                <tr class="border-b border-slate-200 text-left text-[14px] font-semibold uppercase tracking-wide text-slate-500">
                    <th class="w-[39%] px-4 py-4">Informasi Produk</th>
                    <th class="w-[16%] px-4 py-4">Kategori</th>
                    <th class="w-[16%] px-4 py-4">Harga</th>
                    <th class="w-[17%] px-4 py-4">Stok</th>
                    <th class="w-[12%] px-4 py-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($qProduk)) : ?>
                    <tr class="border-b border-slate-200 align-middle transition hover:bg-slate-50">
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center gap-4">
                                <img
                                    src="../images/<?= htmlspecialchars($row['foto_produk']); ?>"
                                    alt="<?= htmlspecialchars($row['nama_produk']); ?>"
                                    class="h-12 w-12 shrink-0 rounded-xl object-cover ring-1 ring-slate-200"
                                >

                                <div class="min-w-0">
                                    <p class="truncate text-[16px] font-bold leading-tight text-blue-900">
                                        <?= htmlspecialchars($row['nama_produk']); ?>
                                    </p>
                                    <p class="mt-1 text-sm leading-none text-slate-400">
                                        ID: PRD-<?= $row['id_produk']; ?>
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center">
                                <span class="text-[15px] text-slate-700">
                                    <?= htmlspecialchars($row['nama_kategori']); ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center">
                                <span class="text-[15px] font-semibold text-blue-900">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] flex-col justify-center gap-2">
                                <?php if ((int)$row['stok'] > 10) : ?>
                                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        <?= $row['stok']; ?> Pcs
                                    </span>
                                <?php elseif ((int)$row['stok'] == 0) : ?>
                                    <span class="inline-flex w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        0 Pcs
                                    </span>
                                <?php else : ?>
                                    <span class="inline-flex w-fit rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">
                                        <?= $row['stok']; ?> Pcs
                                    </span>
                                <?php endif; ?>

                                <p class="pr-2 text-[11px] leading-relaxed text-slate-400">
                                    <?= htmlspecialchars($row['detail_varian'] ?? '-'); ?>
                                </p>
                            </div>
                        </td>

                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center gap-2">
                                <button
                                    type="button"
                                    title="Edit"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100 hover:shadow-sm"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button
                                    type="button"
                                    title="Hapus"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 transition hover:-translate-y-0.5 hover:bg-red-100 hover:shadow-sm"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

    </main>
</div>
</body>
</html>