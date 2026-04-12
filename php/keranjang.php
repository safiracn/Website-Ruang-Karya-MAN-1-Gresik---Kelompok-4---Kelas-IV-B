<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
require_once 'koneksi.php';

/* Mengambil ID user dari session login */
$id_user = $_SESSION['id_user'] ?? 1;

/* Memeriksa apakah user sudah memiliki keranjang berdasarkan id_user */
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

/* Mengecek varian produk, stok, dan menambahkan item ke keranjang user */
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

/* Mengupdate jumlah produk di keranjang (tambah atau kurang) sesuai aksi user dari button + dan -*/
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

/* Menghapus produk di keranjang */
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

/* Mengambil semua data produk dalam keranjang beserta detailnya untuk ditampilkan */
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

/* Menghitung jumlah item dalam keranjang dan total harga keseluruhan */
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

function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>

    <link rel="stylesheet" href="../RuangKaryaCSS/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans-body text-slate-800">

    <?php include 'header.php'; ?>

    <main class="px-4 py-8 md:px-6">
        <div class="mx-auto max-w-[1450px]">

            <section class="mb-6">
                <h1 class="font-serif-heading text-[38px] font-bold leading-tight text-blue-900 md:text-[52px]">
                    Keranjang Belanja
                </h1>
                <p class="mt-2 max-w-[760px] text-[15px] leading-relaxed text-slate-500 md:text-[16px]">
                    Koleksi karya terpilih dari siswa MAN 1 Gresik yang siap menghiasi ruang belajar dan kerja Anda.
                </p>
            </section>

            <section class="overflow-hidden rounded-[24px] bg-white shadow-sm ring-1 ring-slate-200">
                <div class="grid grid-cols-[48px_2.4fr_1fr_150px_1fr_40px] items-center border-b border-slate-200 px-4 py-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500 md:px-6">
                    <div></div>
                    <div>Produk</div>
                    <div>Harga Satuan</div>
                    <div>Jumlah</div>
                    <div>Total</div>
                    <div></div>
                </div>

                <div class="h-[560px] overflow-y-auto">
                    <?php if ($qCart && mysqli_num_rows($qCart) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($qCart)): ?>
                            <div
                                class="cart-item grid grid-cols-[48px_2.4fr_1fr_150px_1fr_40px] items-center border-b border-slate-200 px-4 py-3 transition hover:bg-slate-50 md:px-6"
                                data-id="<?= (int)$row['id_keranjang_detail']; ?>"
                                data-subtotal="<?= (float)$row['subtotal']; ?>"
                            >
                                <div class="flex justify-center">
                                    <input
                                        type="checkbox"
                                        class="item-checkbox h-6 w-6 cursor-pointer rounded-md border-2 border-slate-300 bg-white text-blue-900 accent-blue-900 transition"
                                        value="<?= (int)$row['id_keranjang_detail']; ?>"
                                        checked
                                    >
                                </div>

                                <div class="flex items-center gap-4">
                                    <img
                                        src="../images/<?= htmlspecialchars($row['foto_produk']); ?>"
                                        alt="<?= htmlspecialchars($row['nama_produk']); ?>"
                                        class="h-[68px] w-[68px] rounded-xl object-cover ring-1 ring-slate-200 md:h-[78px] md:w-[78px]"
                                    >

                                    <div class="min-w-0">
                                        <p class="text-[18px] font-bold leading-tight text-blue-900 md:text-[21px]">
                                            <?= htmlspecialchars($row['nama_produk']); ?>
                                        </p>
                                        <p class="mt-1 text-[12px] font-medium text-slate-500 md:text-[13px]">
                                            Varian: <?= htmlspecialchars($row['nama_varian']); ?>
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-[19px] font-bold text-blue-900 md:text-[21px]">
                                        <?= rupiah($row['harga']); ?>
                                    </p>
                                </div>

                                <div>
                                    <div class="inline-flex h-[42px] items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                                        <form method="POST" class="contents">
                                            <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                            <input type="hidden" name="mode_qty" value="minus">
                                            <button type="submit" name="update_qty" class="flex h-full w-11 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                                <i class="fa-solid fa-minus text-sm"></i>
                                            </button>
                                        </form>

                                        <span class="flex h-full min-w-[48px] items-center justify-center text-[17px] font-semibold text-slate-700">
                                            <?= (int)$row['jumlah']; ?>
                                        </span>

                                        <form method="POST" class="contents">
                                            <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                            <input type="hidden" name="mode_qty" value="plus">
                                            <button type="submit" name="update_qty" class="flex h-full w-11 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                                <i class="fa-solid fa-plus text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div>
                                    <p class="item-total-text text-[19px] font-bold text-blue-900 md:text-[21px]">
                                        <?= rupiah($row['subtotal']); ?>
                                    </p>
                                </div>

                                <div class="flex justify-center">
                                    <form method="POST">
                                        <input type="hidden" name="id_detail" value="<?= (int)$row['id_keranjang_detail']; ?>">
                                        <button type="submit" name="hapus_item" class="text-red-500 transition hover:scale-110 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-[20px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="min-h-[280px] border-b border-slate-200">
                            <div class="flex h-full items-center justify-center px-6 py-20 text-[18px] text-slate-400">
                                Keranjang masih kosong.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-200 bg-white px-4 py-4 shadow-[0_-6px_18px_rgba(15,23,42,0.04)] md:px-6">
                    <form action="FormCheckout.php" method="GET" id="checkoutForm">
                        <input type="hidden" name="selected_items" id="selectedItemsInput">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto_240px] md:items-center md:gap-5">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="selectAll" class="h-6 w-6 cursor-pointer rounded-md border-2 border-slate-300 bg-white text-blue-900 accent-blue-900 transition" <?= $adaProduk ? 'checked' : ''; ?>>
                                <p class="text-[18px] font-semibold text-slate-800 md:text-[20px]">
                                    Pilih Semua (<span id="selectedCountText"><?= $total_item; ?></span> Produk)
                                </p>
                            </div>

                            <div class="text-left md:text-right">
                                <p class="text-[13px] font-medium text-slate-500">
                                    Total Pesanan (<span id="selectedCountInfo"><?= $total_item; ?></span> Produk)
                                </p>
                                <p id="grandTotalText" class="mt-1 text-[30px] font-bold leading-none text-blue-900 md:text-[36px]">
                                    <?= rupiah($grand_total); ?>
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    id="checkoutButton"
                                    class="inline-flex h-[56px] w-full items-center justify-center rounded-2xl bg-yellow-500 px-6 text-[20px] font-semibold text-blue-900 shadow-sm transition hover:bg-yellow-400 disabled:cursor-not-allowed disabled:opacity-60"
                                    <?= $adaProduk ? '' : 'disabled'; ?>
                                >
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    /* Menghitung total harga keseluruhan (grand total) dari item yang dipilih dari checkbox yg ditekan dan berdasarkan subtotal masing-masing produk. */
    <script>
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const selectAll = document.getElementById('selectAll');
        const selectedCountText = document.getElementById('selectedCountText');
        const selectedCountInfo = document.getElementById('selectedCountInfo');
        const grandTotalText = document.getElementById('grandTotalText');
        const selectedItemsInput = document.getElementById('selectedItemsInput');
        const checkoutButton = document.getElementById('checkoutButton');

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updateCartSummary() {
            let total = 0;
            let count = 0;
            let selectedIds = [];

            itemCheckboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    count++;
                    selectedIds.push(checkbox.value);

                    const itemRow = checkbox.closest('.cart-item');
                    total += Number(itemRow.dataset.subtotal || 0);
                }
            });

            selectedCountText.textContent = count;
            selectedCountInfo.textContent = count;
            grandTotalText.textContent = formatRupiah(total);
            selectedItemsInput.value = selectedIds.join(',');

            checkoutButton.disabled = count === 0;

            if (itemCheckboxes.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                return;
            }

            if (count === itemCheckboxes.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (count === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                itemCheckboxes.forEach((checkbox) => {
                    checkbox.checked = this.checked;
                });
                updateCartSummary();
            });
        }

        itemCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateCartSummary);
        });

        updateCartSummary();
    </script>
</body>
</html>