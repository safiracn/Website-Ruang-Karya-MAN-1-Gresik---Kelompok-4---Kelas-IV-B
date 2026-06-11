<?php
session_start();
require '../php/koneksi.php'; // Pastikan file koneksi database sudah benar

// --- LOGIKA COOKIE UNTUK REMEMBER LOGIN ---
if (!isset($_SESSION['id_user']) && isset($_COOKIE['user_login'])) {
    // Jika user belum login di session, tapi punya cookie
    $_SESSION['id_user'] = $_COOKIE['user_login'];
    $_SESSION['nama_lengkap'] = $_COOKIE['user_nama'];
}

// Setelah dicek cookie-nya, baru jalankan proteksi login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); location='login.php';</script>";
    exit;
}

$isLoggedIn = isset($_SESSION['id_user']);

$user_id = $_SESSION['id_user'];
$namaUser = $_SESSION['nama_lengkap'] ?? 'User';

// Logika Filter Status via URL (Tab)
$tab = $_GET['tab'] ?? 'sedang-dipesan';

// Query dasar
$query = "SELECT 
            p.*, 
            pr.nama_produk, 
            pr.foto_produk, 
            pr.id_produk,
            pd.subtotal
          FROM pembelian p
          JOIN pembelian_detail pd ON p.id_pembelian = pd.id_pembelian
          JOIN produk_varian pv ON pd.id_varian = pv.id_varian
          JOIN produk pr ON pv.id_produk = pr.id_produk
          WHERE p.id_user = '$user_id'";

if ($tab === 'selesai') {
    $query .= " AND p.status_pesanan = 'Selesai'";
} else {
    $query .= " AND p.status_pesanan != 'Selesai' AND p.status_pesanan!= 'Dibatalkan'";
}

// Logika Pencarian
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND pr.nama_produk LIKE '%$search%'";
}

$query .= " ORDER BY p.tgl_pembelian DESC";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Ruang Karya</title>
    <link href="../RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="font-sans-body bg-slate-50 text-slate-900">

<?php include 'header.php'; ?> 

    <main class="max-w-5xl mx-auto px-6 py-12 min-h-screen">
        <h2 class="text-4xl font-serif-heading font-bold text-blue-900 mb-2">Riwayat Pesanan</h2>
        
        <?php if(!$isLoggedIn): ?>
            <div class="mt-10 p-10 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-center">
                <div class="w-20 h-20 bg-blue-50 text-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-lock text-3xl"></i>
                </div>
                <h3 class="text-2xl font-serif-heading font-bold text-blue-900 mb-2">Ingin melihat pesanan Anda?</h3>
                <p class="text-slate-500 mb-6">Silakan masuk atau buat akun terlebih dahulu untuk melihat riwayat pembelian karya siswa kami.</p>
                <div class="flex justify-center gap-4">
                    <a href="login.php" class="px-8 py-3 bg-blue-900 hover:bg-yellow-500 text-white rounded-xl font-bold shadow-lg shadow-blue-200">Masuk Sekarang</a>
                    <a href="daftar.php" class="px-8 py-3 border-2 border-blue-900 hover:border-yellow-500 hover:text-yellow-500 text-blue-900 rounded-xl font-bold">Daftar Akun</a>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                <div class="flex bg-slate-200 p-1 rounded-xl">
                    <a href="?tab=sedang-dipesan" class="px-6 py-2 rounded-lg font-bold transition <?= $tab != 'selesai' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-500' ?>">Sedang Dipesan</a>
                    <a href="?tab=selesai" class="px-6 py-2 rounded-lg font-bold transition <?= $tab == 'selesai' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-500' ?>">Selesai</a>
                </div>
                <div class="relative w-full md:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nama produk..." class="w-full pl-10 pr-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-yellow-400 transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400"></i>
                </div>
            </div>

            <div class="space-y-4" id="orderContainer">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="order-item bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center gap-6">
                            <img src="../images/<?= $row['foto_produk'] ?? 'default.jpg' ?>" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1 text-center md:text-left">
                                <h4 class="product-name font-bold text-blue-900"><?= $row['nama_produk'] ?? 'Produk tidak ditemukan' ?></h4>
                                <p class="text-blue-900 font-black text-lg">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></p>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100"><?= $row['status_pesanan'] ?></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="detail_pesanan.php?id=<?= $row['id_pembelian'] ?>" class="text-xs px-4 py-2 border border-blue-900 text-blue-900 rounded-lg font-bold">Detail</a>
                                <?php if($row['status_pesanan'] == 'Selesai'): ?>
                                    <a href="detail_produk.php?id=<?= $row['id_produk'] ?>" class="text-xs px-4 py-2 bg-yellow-500 text-blue-900 rounded-lg font-bold">Beli Lagi</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-slate-400 py-10 italic">Belum ada pesanan di kategori ini.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
<?php include 'footer.php'; ?>
</body>
</html>