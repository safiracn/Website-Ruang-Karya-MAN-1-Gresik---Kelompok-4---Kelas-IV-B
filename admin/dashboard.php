<?php
session_start();
/* kalau user belum login || dan kalau user login tetapi role bukan admin, maka dia tidak bisa masuk halaman ini */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../php/login.php"); // kalau user tidak lolos pengecekan di atas, dia langsung diarahkan ke halaman login
    exit;
}

require_once '../php/koneksi.php';

/* id_user diambil dari session login, lalu dipakai untuk mengambil data admin */
$id_admin = (int)($_SESSION['id_user'] ?? 0);

/* $id_admin diambil dari id session (id admin yg mana? alias admin yg sudah login dan role jg harus admin */
$queryAdmin = mysqli_query($koneksi, "
    SELECT id_user, nama_lengkap, email, no_telp, alamat
    FROM user
    WHERE id_user = '$id_admin' AND role = 'admin' 
    LIMIT 1
");
$admin = mysqli_fetch_assoc($queryAdmin);

/* untuk mengirim data ke header_admin.php */
$activeMenu = 'dashboard';
$pageTitle = 'Daftar Produk';
$pageDesc = 'Kelola dan pantau semua inventaris produk karya siswa Anda.';

/* periksa apakah ada permintaan hapus, jika tidak ada hapus, blok ini tidak dijalankan */
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    $search_redirect = trim($_GET['search'] ?? '');

    if ($id_hapus > 0) {
        // ambil nama produk dulu
        $qNamaProduk = mysqli_query($koneksi, "
            SELECT nama_produk
            FROM produk
            WHERE id_produk = '$id_hapus'
            LIMIT 1
        ");

        $dataNamaProduk = mysqli_fetch_assoc($qNamaProduk);
        $namaProdukHapus = $dataNamaProduk['nama_produk'] ?? 'Produk';

        // cek apakah produk sudah pernah dipakai di transaksi
        $qCekDipakai = mysqli_query($koneksi, "
            SELECT COUNT(*) AS total
            FROM pembelian_detail pd
            INNER JOIN produk_varian pv ON pd.id_varian = pv.id_varian
            WHERE pv.id_produk = '$id_hapus'
        ");

        $dataCekDipakai = mysqli_fetch_assoc($qCekDipakai);
        $totalDipakai = (int)($dataCekDipakai['total'] ?? 0);

        /* Menentukan apakah produk boleh dihapus atau tidak. 
        lalu menyimpan hasilnya ke session (untuk notifikasi)/nama produk dan jenis pesan (sukses/error disimpan untuk notif) */
        if ($totalDipakai > 0) {
            $_SESSION['error_hapus'] = 'Produk "' . $namaProdukHapus . '" tidak bisa dihapus karena sudah pernah masuk transaksi.';
        } else {
            $hapusProduk = mysqli_query($koneksi, "
                DELETE FROM produk
                WHERE id_produk = '$id_hapus'
                LIMIT 1
            ");

            if ($hapusProduk) {
                $_SESSION['success_hapus'] = $namaProdukHapus;
            } else {
                $_SESSION['error_hapus'] = 'Produk "' . $namaProdukHapus . '" gagal dihapus.';
            }
        }
    } else {
        $_SESSION['error_hapus'] = 'ID produk tidak valid.';
    }

    if ($search_redirect !== '') {
        header("Location: dashboard.php?search=" . urlencode($search_redirect));
    } else {
        header("Location: dashboard.php");
    }
    exit;
}


/* Menghitung total produk, stok tersedia, dan stok habis.
Menghitung total semua produk */
$qTotalProduk = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_produk
    FROM produk
");
$totalProduk = mysqli_fetch_assoc($qTotalProduk)['total_produk'] ?? 0;

/* Menghitung jumlah produk yang total stok variannya lebih dari 0 */
$qStokTersedia = mysqli_query($koneksi, "
    SELECT COUNT(*) AS stok_tersedia
    FROM (
        SELECT p.id_produk, COALESCE(SUM(v.stok), 0) AS total_stok
        FROM produk p
        LEFT JOIN produk_varian v ON p.id_produk = v.id_produk
        GROUP BY p.id_produk
        HAVING COALESCE(SUM(v.stok), 0) > 0
    ) AS stok_ready
");
$stokTersedia = mysqli_fetch_assoc($qStokTersedia)['stok_tersedia'] ?? 0;

/* Menghitung jumlah produk yang total stok variannya sama dengan 0 */
$qStokHabis = mysqli_query($koneksi, "
    SELECT COUNT(*) AS stok_habis
    FROM (
        SELECT p.id_produk, COALESCE(SUM(v.stok), 0) AS total_stok
        FROM produk p
        LEFT JOIN produk_varian v ON p.id_produk = v.id_produk
        GROUP BY p.id_produk
        HAVING COALESCE(SUM(v.stok), 0) = 0
    ) AS stok_kosong
");
$stokHabis = mysqli_fetch_assoc($qStokHabis)['stok_habis'] ?? 0;

/* search produk */
$keyword = trim($_GET['search'] ?? '');
$keyword_escape = mysqli_real_escape_string($koneksi, $keyword);

/* Mengambil semua data produk untuk ditampilkan di tabel dashboard */
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
    WHERE p.nama_produk LIKE '%$keyword_escape%'
    GROUP BY p.id_produk, p.nama_produk, p.foto_produk, k.nama_kategori
    ORDER BY p.id_produk ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Pengaturan dasar halaman -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruang Karya</title>

    <!-- File CSS hasil build Tailwind -->
    <link rel="stylesheet" href="../RuangKaryaCSS/output.css">

    <!-- Icon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /*
        |--------------------------------------------------------------------------
        | FONT CUSTOM
        |--------------------------------------------------------------------------
        | Variabel ini dipakai untuk menyamakan font heading dan isi.
        */
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

<?php
// Menampilkan layout sidebar dan topbar admin
include 'header_admin.php';
?>

 <!-- Menampilkan ringkasan total produk, stok tersedia, dan stok habis. -->
<section class="mb-7 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Card total produk -->
    <div class="flex items-center justify-between rounded-2xl border-l-4 border-blue-900 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Total Produk</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900"><?= $totalProduk; ?></h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-900">
            <i class="fa-solid fa-box-archive text-xl"></i>
        </div>
    </div>

    <!-- Card stok tersedia -->
    <div class="flex items-center justify-between rounded-2xl border-l-4 border-green-500 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Stok Tersedia</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900"><?= $stokTersedia; ?></h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
            <i class="fa-solid fa-circle-check text-xl"></i>
        </div>
    </div>

    <!-- Card stok habis -->
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

<!-- menampilkan search box, tombol tambah produk, notifikasi, dan tabel -->
<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

    <!-- Notifikasi gagal hapus -->
    <?php if (isset($_SESSION['error_hapus'])): ?>
        <div id="notif-error-hapus" class="mb-6 rounded-xl border border-yellow-300 bg-yellow-100 px-4 py-3 text-[15px] font-semibold text-yellow-800 shadow-sm">
            <?= htmlspecialchars($_SESSION['error_hapus']); ?>
        </div>
        <?php unset($_SESSION['error_hapus']); ?>
    <?php endif; ?>

    <!-- Notifikasi sukses hapus -->
    <?php if (isset($_SESSION['success_hapus'])): ?>
        <div id="notif-hapus" class="mb-6 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-[15px] font-semibold text-red-700 shadow-sm">
            Produk "<span class="font-bold"><?= htmlspecialchars($_SESSION['success_hapus']); ?></span>" berhasil dihapus.
        </div>
        <?php unset($_SESSION['success_hapus']); ?>
    <?php endif; ?>

    <!-- Bar atas: search dan tombol tambah produk -->
    <div class="mb-6 flex items-center justify-between gap-4">
        <!--
        Search dibungkus form GET.
        Jadi saat admin mengetik lalu menekan Enter, browser otomatis submit.
        -->
        <form action="" method="GET" class="w-[360px]">
            <div class="group flex h-12 items-center gap-3 rounded-xl bg-slate-100 px-4 transition hover:bg-slate-200">
                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($keyword); ?>"
                    placeholder="Cari nama produk..."
                    class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
                >
            </div>
        </form>

        <!-- Tombol menuju halaman tambah produk -->
        <a href="tambahProduk.php"
           class="inline-flex h-14 items-center gap-2 rounded-2xl bg-yellow-500 px-6 text-[15px] font-semibold text-blue-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-yellow-400 hover:shadow-md">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Tambah Produk</span>
        </a>
    </div>

    <!-- Bungkus tabel agar tetap rapi jika layar sempit -->
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
                <?php if (mysqli_num_rows($qProduk) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($qProduk)) : ?>
                        <tr class="border-b border-slate-200 align-middle transition hover:bg-slate-50">

                            <!-- Kolom informasi produk -->
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

                            <!-- Kolom kategori -->
                            <td class="px-4 py-4 align-middle">
                                <div class="flex min-h-[64px] items-center">
                                    <span class="text-[15px] text-slate-700">
                                        <?= htmlspecialchars($row['nama_kategori']); ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Kolom harga -->
                            <td class="px-4 py-4 align-middle">
                                <div class="flex min-h-[64px] items-center">
                                    <span class="text-[15px] font-semibold text-blue-900">
                                        Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Kolom stok + detail varian -->
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

                            <!-- Kolom aksi: edit dan hapus -->
                            <td class="px-4 py-4 align-middle">
                                <div class="flex min-h-[64px] items-center gap-2">

                                    <!-- Tombol edit menuju edit.php dengan id produk -->
                                    <a
                                        href="edit.php?id=<?= (int)$row['id_produk']; ?>"
                                        title="Edit"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100 hover:shadow-sm"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <!-- Tombol hapus, search tetap dipertahankan -->
                                    <a
                                        href="dashboard.php?hapus=<?= (int)$row['id_produk']; ?>&search=<?= urlencode($keyword); ?>"
                                        title="Hapus"
                                        onclick="return confirm('Yakin untuk menghapus produk &quot;<?= htmlspecialchars(addslashes($row['nama_produk']), ENT_QUOTES); ?>&quot;?')"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 transition hover:-translate-y-0.5 hover:bg-red-100 hover:shadow-sm"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                            Produk tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Auto-hide notif -->
<script>
    const notifList = [
        document.getElementById('notif-hapus'),
        document.getElementById('notif-error-hapus')
    ];

    notifList.forEach((notif) => {
        if (notif) {
            setTimeout(() => {
                notif.style.transition = 'all 0.5s ease';
                notif.style.opacity = '0';
                notif.style.transform = 'translateY(-8px)';

                setTimeout(() => {
                    notif.remove();
                }, 500);
            }, 3000);
        }
    });
</script>
</main>
</div>
</body>
</html>