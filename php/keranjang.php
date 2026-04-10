<?php
session_start();
require_once 'koneksi.php';

/*
|--------------------------------------------------------------------------
| USER LOGIN
|--------------------------------------------------------------------------
*/
$id_user = $_SESSION['id_user'] ?? 1;

/*
|--------------------------------------------------------------------------
| CARI / BUAT KERANJANG USER
|--------------------------------------------------------------------------
*/
$qCariKeranjang = mysqli_query($koneksi, "
    SELECT id_keranjang
    FROM keranjang
    WHERE id_user = '$id_user'
    LIMIT 1
");

if ($qCariKeranjang && mysqli_num_rows($qCariKeranjang) > 0) {
    $dataKeranjang = mysqli_fetch_assoc($qCariKeranjang);
    $id_keranjang = (int)$dataKeranjang['id_keranjang'];
} else {
    mysqli_query($koneksi, "
        INSERT INTO keranjang (id_user)
        VALUES ('$id_user')
    ");
    $id_keranjang = mysqli_insert_id($koneksi);
}

/*
|--------------------------------------------------------------------------
| TAMBAH KE KERANJANG DARI DETAIL PRODUK
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'keranjang') {
    $id_varian = isset($_POST['id_varian']) ? (int)$_POST['id_varian'] : 0;
    $jumlah    = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

    if ($id_varian > 0 && $jumlah > 0) {
        $qVarian = mysqli_query($koneksi, "
            SELECT id_varian, stok
            FROM produk_varian
            WHERE id_varian = '$id_varian'
            LIMIT 1
        ");

        if ($qVarian && mysqli_num_rows($qVarian) > 0) {
            $varian = mysqli_fetch_assoc($qVarian);
            $stokTersedia = (int)$varian['stok'];

            if ($stokTersedia > 0) {
                $qCekDetail = mysqli_query($koneksi, "
                    SELECT id_keranjang_detail, jumlah
                    FROM keranjang_detail
                    WHERE id_keranjang = '$id_keranjang'
                      AND id_varian = '$id_varian'
                    LIMIT 1
                ");

                if ($qCekDetail && mysqli_num_rows($qCekDetail) > 0) {
                    $detailLama = mysqli_fetch_assoc($qCekDetail);
                    $jumlahBaru = (int)$detailLama['jumlah'] + $jumlah;

                    if ($jumlahBaru > $stokTersedia) {
                        $jumlahBaru = $stokTersedia;
                    }

                    mysqli_query($koneksi, "
                        UPDATE keranjang_detail
                        SET jumlah = '$jumlahBaru'
                        WHERE id_keranjang_detail = '{$detailLama['id_keranjang_detail']}'
                    ");
                } else {
                    if ($jumlah > $stokTersedia) {
                        $jumlah = $stokTersedia;
                    }

                    mysqli_query($koneksi, "
                        INSERT INTO keranjang_detail (id_keranjang, id_varian, jumlah)
                        VALUES ('$id_keranjang', '$id_varian', '$jumlah')
                    ");
                }
            }
        }
    }

    header("Location: keranjang.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE JUMLAH
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $id_detail = (int)($_POST['id_detail'] ?? 0);
    $mode_qty  = $_POST['mode_qty'] ?? '';

    $qQty = mysqli_query($koneksi, "
        SELECT kd.id_keranjang_detail, kd.jumlah, pv.stok
        FROM keranjang_detail kd
        JOIN produk_varian pv ON kd.id_varian = pv.id_varian
        WHERE kd.id_keranjang_detail = '$id_detail'
          AND kd.id_keranjang = '$id_keranjang'
        LIMIT 1
    ");

    if ($qQty && mysqli_num_rows($qQty) > 0) {
        $dataQty = mysqli_fetch_assoc($qQty);
        $jumlahSekarang = (int)$dataQty['jumlah'];
        $stokMaks = (int)$dataQty['stok'];

        if ($mode_qty === 'plus') {
            $jumlahBaru = $jumlahSekarang + 1;
            if ($jumlahBaru > $stokMaks) {
                $jumlahBaru = $stokMaks;
            }
        } else {
            $jumlahBaru = $jumlahSekarang - 1;
            if ($jumlahBaru < 1) {
                $jumlahBaru = 1;
            }
        }

        mysqli_query($koneksi, "
            UPDATE keranjang_detail
            SET jumlah = '$jumlahBaru'
            WHERE id_keranjang_detail = '$id_detail'
        ");
    }

    header("Location: keranjang.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| HAPUS ITEM
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_item'])) {
    $id_detail = (int)($_POST['id_detail'] ?? 0);

    mysqli_query($koneksi, "
        DELETE FROM keranjang_detail
        WHERE id_keranjang_detail = '$id_detail'
          AND id_keranjang = '$id_keranjang'
    ");

    header("Location: keranjang.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATA KERANJANG
|--------------------------------------------------------------------------
*/
$qCart = mysqli_query($koneksi, "
    SELECT
        kd.id_keranjang_detail,
        kd.jumlah,
        pv.id_varian,
        pv.nama_varian,
        pv.harga,
        p.nama_produk,
        p.foto_produk,
        (pv.harga * kd.jumlah) AS subtotal
    FROM keranjang_detail kd
    INNER JOIN produk_varian pv ON kd.id_varian = pv.id_varian
    INNER JOIN produk p ON pv.id_produk = p.id_produk
    WHERE kd.id_keranjang = '$id_keranjang'
    ORDER BY kd.id_keranjang_detail DESC
");

/*
|--------------------------------------------------------------------------
| TOTAL
|--------------------------------------------------------------------------
*/
$qTotal = mysqli_query($koneksi, "
    SELECT
        COUNT(kd.id_keranjang_detail) AS total_item,
        COALESCE(SUM(pv.harga * kd.jumlah), 0) AS grand_total
    FROM keranjang_detail kd
    INNER JOIN produk_varian pv ON kd.id_varian = pv.id_varian
    WHERE kd.id_keranjang = '$id_keranjang'
");

$dataTotal   = mysqli_fetch_assoc($qTotal);
$total_item  = (int)($dataTotal['total_item'] ?? 0);
$grand_total = (float)($dataTotal['grand_total'] ?? 0);

$adaProduk = $total_item > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>

    <link rel="stylesheet" href="../RuangKaryaCSS/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --font-serif-heading: "Cambria", serif;
            --font-sans-body: "Inter", sans-serif;
        }

        .font-serif-heading { font-family: var(--font-serif-heading); }
        .font-sans-body { font-family: var(--font-sans-body); }

        .custom-scrollbar::-webkit-scrollbar {
            width: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d7dde7;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #bcc6d4;
        }
    </style>
</head>
<body class="font-sans-body bg-slate-100 text-slate-800">

    <!-- Space header -->
    <div class="h-[150px]"></div>

    <main class="px-6 pb-16">
        <div class="mx-auto max-w-[1500px]">

            <!-- Judul -->
            <section class="mb-7">
                <h1 class="font-serif-heading text-[54px] font-bold leading-none text-blue-900">
                    Keranjang Belanja
                </h1>
                <p class="mt-3 max-w-[760px] text-[16px] leading-relaxed text-slate-500">
                    Koleksi karya terpilih dari siswa MAN 1 Gresik yang siap menghiasi ruang belajar dan kerja Anda.
                </p>
            </section>

            <!-- Box utama -->
            <section class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200">

                <!-- Header tabel -->
                <div class="grid grid-cols-[70px_2.2fr_1.1fr_180px_1.1fr_50px] items-center border-b border-slate-200 px-6 py-5 text-[14px] font-semibold uppercase tracking-wide text-slate-500">
                    <div></div>
                    <div>Produk</div>
                    <div>Harga Satuan</div>
                    <div>Jumlah</div>
                    <div>Total</div>
                    <div></div>
                </div>

                <!-- Isi list -->
                <div class="custom-scrollbar max-h-[720px] overflow-y-auto">
                    <?php if ($qCart && mysqli_num_rows($qCart) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($qCart)): ?>
                            <div class="grid grid-cols-[70px_2.2fr_1.1fr_180px_1.1fr_50px] items-center border-b border-slate-200 px-6 py-6 transition hover:bg-slate-50">

                                <!-- Checkbox item -->
                                <div class="flex justify-center">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-900 text-white shadow-sm">
                                        <i class="fa-solid fa-check text-sm"></i>
                                    </div>
                                </div>

                                <!-- Produk -->
                                <div class="flex items-center gap-4">
                                    <img
                                        src="../images/<?= htmlspecialchars($row['foto_produk']); ?>"
                                        alt="<?= htmlspecialchars($row['nama_produk']); ?>"
                                        class="h-[92px] w-[92px] rounded-xl object-cover ring-1 ring-slate-200"
                                    >

                                    <div>
                                        <p class="text-[30px] font-bold leading-tight text-blue-900">
                                            <?= htmlspecialchars($row['nama_produk']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Harga -->
                                <div>
                                    <p class="text-[28px] font-bold text-blue-900">
                                        Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                    </p>
                                </div>

                                <!-- Jumlah -->
                                <div>
                                    <div class="inline-flex h-[42px] items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                                        <form method="POST" class="contents">
                                            <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                            <input type="hidden" name="mode_qty" value="minus">
                                            <button type="submit" name="update_qty" class="flex h-full w-12 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                                <i class="fa-solid fa-minus text-sm"></i>
                                            </button>
                                        </form>

                                        <span class="flex h-full min-w-[54px] items-center justify-center text-[18px] font-semibold text-slate-700">
                                            <?= (int)$row['jumlah']; ?>
                                        </span>

                                        <form method="POST" class="contents">
                                            <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                            <input type="hidden" name="mode_qty" value="plus">
                                            <button type="submit" name="update_qty" class="flex h-full w-12 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                                <i class="fa-solid fa-plus text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div>
                                    <p class="text-[30px] font-bold text-blue-900">
                                        Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?>
                                    </p>
                                </div>

                                <!-- Hapus -->
                                <div class="flex justify-center">
                                    <form method="POST">
                                        <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                        <button type="submit" name="hapus_item" class="text-red-500 transition hover:scale-110 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-[22px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Tetap tampil area kosong rapi -->
                        <div class="min-h-[420px] border-b border-slate-200">
                            <div class="flex h-full items-center justify-center px-6 py-20 text-[20px] text-slate-400">
                                Keranjang masih kosong.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Panel bawah -->
                <div class="sticky bottom-0 z-20 border-t border-slate-200 bg-white px-6 py-5 shadow-[0_-6px_18px_rgba(15,23,42,0.04)]">
                    <div class="grid grid-cols-[1fr_auto_320px] items-center gap-6">

                        <!-- Kiri -->
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-md border border-slate-300 bg-white shadow-sm"></div>
                            <p class="text-[28px] font-semibold text-slate-800">
                                Pilih Semua (<?= $total_item; ?> Produk)
                            </p>
                        </div>

                        <!-- Tengah kanan -->
                        <div class="text-right">
                            <p class="text-[14px] font-medium text-slate-500">
                                Total Pesanan (<?= $total_item; ?> Produk)
                            </p>
                            <p class="mt-1 text-[44px] font-bold leading-none text-blue-900">
                                Rp <?= number_format($grand_total, 0, ',', '.'); ?>
                            </p>
                        </div>

                        <!-- Kanan -->
                        <div class="flex justify-end">
                            <?php if ($adaProduk): ?>
                                <button class="inline-flex h-[76px] w-full max-w-[300px] items-center justify-center rounded-2xl bg-yellow-500 px-8 text-[28px] font-semibold text-blue-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-yellow-400 hover:shadow-md">
                                    Pesan Sekarang
                                </button>
                            <?php else: ?>
                                <button class="inline-flex h-[76px] w-full max-w-[300px] items-center justify-center rounded-2xl bg-yellow-500 px-8 text-[28px] font-semibold text-blue-900 opacity-80 cursor-not-allowed shadow-sm">
                                    Pesan Sekarang
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </main>

</body>
</html>