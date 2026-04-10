<?php
session_start();

/**
 * LOGIKA AKSES ROLE
 * 1. Jika belum login (session role kosong), boleh lewat (sebagai tamu/calon pembeli).
 * 2. Jika sudah login dan role-nya 'pembeli', boleh lewat.
 * 3. Jika sudah login dan role-nya 'admin', tendang ke dashboard admin.
 */
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php"); // Ganti dengan file admin kamu
    exit();
}

/**
 * LOGIKA COOKIE
 * Menyimpan nama user di browser selama 1 jam jika dia sudah login, 
 * agar JS bisa menyapa namanya.
 */
$namaUser = "Pengunjung";
if (isset($_SESSION['nama_lengkap'])) {
    $namaUser = $_SESSION['nama_lengkap'];
    setcookie("last_user", $namaUser, time() + 3600, "/");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Karya - MAN 1 Gresik</title>
    <link href="./RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans-body text-slate-900">

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
            <?php if(!isset($_SESSION['role'])): ?>
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
            <img src="./images/logo man.png" alt="Logo MAN 1 Gresik" class="h-14 w-auto md:h-20" />
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
            <li><a href="dashboardPembeli.php" class="text-yellow-500 border-b-2 border-blue-900 pb-1">Beranda</a></li>
            <li><a href="gabungan.php" class="text-blue-900 hover:text-yellow-500 transition">Katalog</a></li>
            <li><a href="riwayat.php" class="text-blue-900 hover:text-yellow-500 transition">Riwayat</a></li>
            <li class="relative"> 
                <a href="keranjang.php" class="text-blue-900 hover:text-yellow-500 transition flex items-center p-2 rounded-full hover:bg-slate-">
            <div class="relative">
              <i class="fa-solid fa-cart-shopping text-xl"></i>
              <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1 py-0.1 rounded-full shadow-sm">0</span>
            </div>
                </a>
            </li>

            <li>
                <a href="akun_saya.php" class="flex items-center gap-3 group pl-4 border-l border-slate-200">
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

    <header class="container mx-auto px-6 md:px-20 py-16 flex flex-col md:flex-row items-center gap-10">
        <div class="md:w-1/2">
            <h2 class="text-5xl font-serif-heading font-bold text-[#0A265F] mb-6">Ruang Karya: <br> Dari Siswa untuk Dunia</h2>
            <p class="text-slate-500 mb-8 leading-relaxed">Etalase kreativitas dan inovasi berkelanjutan dari siswa-siswi MAN 1 Gresik. Menghadirkan karya autentik yang memadukan keterampilan tradisional dengan visi modern.</p>
            <div class="flex gap-4">
                <a href="gabungan.php" class="bg-[#0A265F] text-white px-6 py-3 rounded-full font-bold hover:bg-yellow-500 transition inline-block">
                    Jelajahi Karya Kami →
                </a>
            </div>
        </div>
        <div class="md:w-1/2">
            <div class="relative">
                <img src="./images/siswa.png" class="rounded-2xl shadow-2xl transition duration-500 hover:rotate-1">
            </div>
        </div>
    </header>

    <section class="bg-white py-20 px-6 md:px-20">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16">
            <div class="space-y-8">
                <div>
                    <h3 class="text-[#0A265F] font-serif-heading font-bold text-3xl mb-4">Visi Kami</h3>
                    <p class="text-2xl italic font-serif-heading font-semibold text-slate-700 leading-snug">"Menjadi platform pusat kreativitas siswa yang unggul dan berdaya saing global."</p>
                </div>
                <div>
                    <h3 class="text-[#0A265F] font-bold text-3xl font-serif-heading mb-4">Misi Kami</h3>
                    <p class="text-slate-500">Membangun ekosistem pendidikan yang tidak hanya mengajar, tetapi memberdayakan siswa untuk menjadi kreator yang mandiri dan kompetitif.</p>
                </div>
            </div>
            <div class="grid gap-6">
                <div class="p-6 bg-blue-50 border-l-8 border-blue-900 rounded-2xl flex gap-5">
                    <div class="bg-blue-900 text-white w-12 h-12 flex items-center justify-center rounded-xl shrink-0"><i class="fa-solid fa-lightbulb text-xl"></i></div>
                    <div>
                        <h4 class="font-semibold text-xl font-serif-heading text-[#0A265F]">Inovasi Berkelanjutan</h4>
                        <p class="text-xs text-slate-500 mt-2">Mendorong siswa untuk terus mengeksplorasi teknik dan material baru dalam menciptakan produk yang relevan dengan zaman.</p>
                    </div>
                </div>
                <div class="p-6 bg-yellow-50 border-l-8 border-yellow-500 rounded-2xl flex gap-5">
                    <div class="bg-yellow-500 text-blue-900 w-12 h-12 flex items-center justify-center rounded-xl shrink-0"><i class="fa-solid fa-chart-line text-xl"></i></div>
                    <div>
                        <h4 class="font-semibold text-xl font-serif-heading text-[#0A265F]">Kemandirian Ekonomi</h4>
                        <p class="text-xs text-slate-500 mt-2">Membekali siswa dengan jiwa kewirausahaan sehingga mampu mengelola karya mereka menjadi nilai ekonomi mandiri.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-6 md:px-20 container mx-auto">
        <div class="flex justify-between items-end mb-10">
            <h2 class="text-3xl font-serif-heading font-black text-[#0A265F]">Kategori Unggulan</h2>
            <a href="gabungan.php" class="text-blue-800 font-bold text-sm">Lihat Semua &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="group relative h-[400px] overflow-hidden rounded-[2rem] shadow-xl">
                <img src="./images/meja lucu.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A265F] to-transparent opacity-80"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-yellow-500 text-blue-900 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-block">Siswa Kelas XII</span>
                    <h5 class="text-2xl font-serif-heading font-bold">Mebel</h5>
                    <p class="text-xs opacity-70 mt-1">Fungsi dan estetika dalam balutan kayu pilihan.</p>
                </div>
            </div>
            <div class="group relative h-[400px] overflow-hidden rounded-[2rem] shadow-xl">
                <img src="./images/vas bunga.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A265F] to-transparent opacity-80"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-yellow-500 text-blue-900 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-block">Unit Produksi</span>
                    <h5 class="text-2xl font-serif-heading font-bold">Kerajinan</h5>
                    <p class="text-xs opacity-70 mt-1">Sentuhan tangan kreatif dalam kearifan lokal.</p>
                </div>
            </div>
            <div class="group relative h-[400px] overflow-hidden rounded-[2rem] shadow-xl">
                <img src="./images/batik 2.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A265F] to-transparent opacity-80"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-yellow-500 text-blue-900 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-block">Tata Busana</span>
                    <h5 class="text-2xl font-serif-heading font-bold">Busana</h5>
                    <p class="text-xs opacity-70 mt-1">Karya fashion orisinal dari bangku sekolah.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 md:px-20 mb-20">
        <div class="bg-[#0A265F] rounded-[3rem] p-16 text-center text-white space-y-6">
            <h2 class="text-4xl font-serif-heading font-bold">Miliki Karya Eksklusif Siswa Kami</h2>
            <p class="max-w-xl mx-auto opacity-70 text-sm">Setiap pembelian adalah bentuk dukungan nyata bagi pengembangan kreativitas dan jiwa wirausaha generasi muda.</p>
            <a href="gabungan.php" class="bg-yellow-500 text-blue-900 font-black px-10 py-4 rounded-full hover:scale-105 transition shadow-lg inline-block">
                Mulai Belanja
            </a>
        </div>
    </section>

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
        // Ambil data cookie lewat JS untuk sapaan console
        const getCookie = (name) => {
            let value = "; " + document.cookie;
            let parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        }

        const user = getCookie('last_user');
        if (user) {
            console.log("Selamat datang kembali, " + decodeURIComponent(user) + "!");
        }

        // Interaksi Tombol
        document.getElementById('shopBtn').onclick = function() {
            alert("Mengarahkan ke halaman belanja...");
            // Session Storage (bertahan selama tab dibuka)
            sessionStorage.setItem('clicked_shop', 'true');
        }

        // Toggle Mobile Menu (Sederhana)
        const mobileBtn = document.getElementById('mobile-menu-button');
        mobileBtn.onclick = function() {
            alert("Fitur menu mobile akan membuka sidebar/dropdown menu.");
            // Di sini nanti kamu bisa tambahkan logika membuka menu mobile
        }
    </script>
</body>
</html>