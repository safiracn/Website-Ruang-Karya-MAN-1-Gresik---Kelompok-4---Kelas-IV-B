<?php
session_start();
require './php/koneksi.php';

// 1. Logika Identitas (Sudah Login atau Tamu)
$isLoggedIn = isset($_SESSION['role']);

$namaUser = $isLoggedIn ? $_SESSION['nama_lengkap'] : "Tamu"; // Jika belum login, tampilkan "Tamu"

// 2. Ambil Data Riwayat
$items = [];
if ($isLoggedIn) {
    $user_id = $_SESSION['id_user'];
    $tab = $_GET['tab'] ?? 'sedang-dipesan';
    
    // Query hanya untuk user yang sedang login
    $query = "SELECT p.*, pr.nama_produk, pr.gambar, pr.id as produk_id 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              WHERE p.user_id = '$user_id'";

    if ($tab === 'selesai') {
        $query .= " AND p.status = 'Selesai'";
    } else {
        $query .= " AND p.status IN ('Menunggu Pembayaran', 'Diproses', 'Dikirim')";
    }
    
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Ruang Karya</title>
    <link href="./RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="font-sans-body bg-slate-50 text-slate-900">

    <div class="w-full rounded-b-[28px] bg-blue-900 text-white text-sm shadow-sm">
        <div class="mx-auto flex flex-col md:flex-row items-center justify-between max-w-7xl px-6 py-3 gap-2">
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-xs"></i>
                     +031-3949544
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-xs"></i>
                     mangresik@kemenag.go.id
                    </span>
            </div>
            <div class="flex items-center gap-4">
                <?php if(!$isLoggedIn): ?>
                    <a href="daftar.php" class="bg-yellow-500 text-blue-900 px-4 py-1.5 rounded-lg font-semibold transition hover:bg-yellow-500">Daftar</a>
                    <a href="login.php" class="bg-yellow-500 text-blue-900 px-4 py-1.5 rounded-lg font-semibold transition hover:bg-yellow-500">Masuk</a>
                <?php else: ?>
                    <span class="font-semibold italic text-yellow-500">Halo, <?= $namaUser ?>!</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header class="bg-white sticky top-0 z-40 shadow-sm border-b border-slate-100">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
                <img src="./images/logo man.png" alt="Logo" class="h-14 w-auto md:h-20" />
                <div>
                    <h1 class="text-xl md:text-3xl font-serif-heading font-bold text-blue-900">Ruang Karya</h1>
                    <h2 class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</h2>
                    <p class="hidden md:block mt-1 text-xs italic text-blue-800">
                        Islami, Cerdas, Unggul, Kompetitif, & Peduli Lingkungan
                    </p>
                </div>
            </div>

            <nav class="hidden lg:block">
                <ul class="flex items-center gap-8 font-semibold">
                    <li><a href="dashboardPembeli.php" class="text-blue-900 hover:text-yellow-500 transition">Beranda</a></li>
                    <li><a href="katalog.php" class="text-blue-900 hover:text-yellow-500 transition">Katalog</a></li>
                    <li><a href="riwayat.php" class="text-yellow-500 border-b-2 border-blue-900 pb-1">Riwayat</a></li>
                    <li class="relative"> 
                        <a href="keranjang.php" class="text-blue-900 hover:text-yellow-500 transition flex items-center p-2 rounded-full hover:bg-slate-">
                            <div class="relative">
                                <i class="fa-solid fa-cart-shopping text-xl"></i>
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1 py-0.1 rounded-full shadow-sm">0</span>
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="akun_saya.php" class="flex items-center gap-3 group border-l pl-4 border-slate-200">
                            <div class="text-right hidden xl:block">
                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider leading-none">Akun Saya</p>
                                <p class="text-sm text-blue-900 font-bold group-hover:text-yellow-500 transition">
                                    <?= isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Tamu' ?>
                                </p>
                            </div>
                            <div class="h-11 w-11 rounded-full border-2 border-blue-900 overflow-hidden group-hover:border-yellow-500 transition shadow-sm bg-blue-50 flex items-center justify-center">
                                <img src="https://ui-avatars.com/api/?name=<?= $namaUser ?>&background=1e3a8a&color=fff" alt="Profil" class="h-10 w-10 object-cover" />
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="lg:hidden flex items-center gap-4">
                <a href="#" class="relative text-blue-900 p-2">
                    <i class="fa-solid fa-cart-shopping text-2xl"></i>
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
                </a>
                <button id="mobile-menu-button" class="text-blue-900 p-2 hover:bg-slate-100 rounded-lg">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

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
                            <img src="images/<?= $row['gambar'] ?>" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1 text-center md:text-left">
                                <h4 class="product-name font-bold text-blue-900"><?= $row['nama_produk'] ?></h4>
                                <p class="text-blue-900 font-black text-lg">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></p>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100"><?= $row['status'] ?></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="detail_pesanan.php?id=<?= $row['id'] ?>" class="text-xs px-4 py-2 border border-blue-900 text-blue-900 rounded-lg font-bold">Detail</a>
                                <?php if($row['status'] == 'Selesai'): ?>
                                    <a href="detail_produk.php?id=<?= $row['produk_id'] ?>" class="text-xs px-4 py-2 bg-yellow-500 text-blue-900 rounded-lg font-bold">Beli Lagi</a>
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

    <footer class="bg-gradient-to-b from-blue-800 to-blue-950 text-white mt-20 rounded-t-[40px]">
    <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-16 px-8 py-16 items-start">

    <!-- Logo & Deskripsi -->
    <div class="space-y-5">

      <div class="flex items-center gap-4">
        <img src="images/logo man.png" class="h-16 w-auto" alt="Logo MAN 1 Gresik">

        <div>
          <h3 class="font-bold text-xl tracking-wide text-yellow-500">
            MAN 1 GRESIK
          </h3>

          <p class="text-sm text-blue-200">
            Madrasah Aliyah Negeri 1 Gresik
          </p>
        </div>
      </div>

      <p class="text-sm italic text-blue-300 leading-relaxed">
        "Islami, Cerdas, Unggul, Kompetitif & Peduli Lingkungan"
      </p>

      <!-- Social Media -->
      <div class="flex gap-4 pt-2">

        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-500 hover:text-black transition duration-300">
          <i class="fab fa-facebook-f"></i>
        </a>

        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-500 hover:text-black transition duration-300">
          <i class="fab fa-instagram"></i>
        </a>

        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-500 hover:text-black transition duration-300">
          <i class="fab fa-youtube"></i>
        </a>

      </div>

    </div>


    <!-- Kontak -->
    <div class="space-y-6">

      <h3 class="font-semibold text-lg border-b border-yellow-500 inline-block pb-1">
        Kontak Kami
      </h3>

      <div class="space-y-4 text-sm text-blue-200">

        <div class="flex items-start gap-3">
          <i class="fa-solid fa-location-dot text-yellow-500 mt-1"></i>
          <span>Jl. Raya Bungah No 46 Bungah Gresik, 61152</span>
        </div>

        <div class="flex items-center gap-3">
          <i class="fa-solid fa-phone text-yellow-500"></i>
          <span>+031 3949544</span>
        </div>

        <div class="flex items-center gap-3">
          <i class="fa-solid fa-envelope text-yellow-500"></i>
          <span>mangresik@kemenag.go.id</span>
        </div>

      </div>

    </div>


    <!-- Map -->
    <div class="rounded-2xl overflow-hidden shadow-xl ring-1 ring-white/20 hover:scale-[1.02] transition duration-300">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.6303657255885!2d112.56814117403772!3d-7.052646669109158!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77e32ae8a147bf%3A0xafba609a0b2aaa12!2sMAN%201%20gresik!5e0!3m2!1sen!2sid!4v1773229345183!5m2!1sen!2sid" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>


  <!-- Bottom Footer -->
  <div class="border-t border-white/10 bg-blue-950/80 backdrop-blur-md">

    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center py-5 px-8 text-sm text-blue-300 gap-3">

      <p>
        © 2026 
        <span class="font-semibold text-white">
          Ruang Karya MAN 1 Gresik
        </span>
      </p>

      <p class="text-xs text-blue-400">
        All rights reserved
      </p>
    </div>
  </div>

<!-- WhatsApp Button -->
<a href="#" 
   class="fixed bottom-8 left-8 bg-green-500 w-16 h-16 flex items-center justify-center rounded-full shadow-xl hover:scale-110 transition-all duration-300 z-50 group">

    <i class="fab fa-whatsapp text-white text-2xl"></i>

    <!-- Tooltip -->
    <span class="absolute left-20 flex items-center bg-white text-gray-800 text-sm font-medium px-4 py-2 rounded-xl shadow-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
        Chat Admin

        <!-- Panah -->
        <span class="absolute -left-1 w-4 h-4 bg-white rotate-45"></span>
    </span>

        </a>
    </footer>

    <script>
        // Pencarian Sederhana via JS (Tanpa Refresh)
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.order-item');
            
            items.forEach(item => {
                let name = item.querySelector('.product-name').innerText.toLowerCase();
                item.style.display = name.includes(filter) ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>