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
$namaUser = "Pengunjung"; // Default kalau belum login

if (isset($_SESSION['nama_lengkap'])) {
    // Kalau sudah login, ambil dari session saja
    $namaUser = $_SESSION['nama_lengkap'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Karya - MAN 1 Gresik</title>
    <link href="../RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans-body text-slate-900">

<?php include 'header.php'; ?>

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
                <img src="../images/siswa.png" class="rounded-2xl shadow-2xl transition duration-500 hover:rotate-1">
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
                <img src="../images/meja lucu.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A265F] to-transparent opacity-80"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-yellow-500 text-blue-900 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-block">Siswa Kelas XII</span>
                    <h5 class="text-2xl font-serif-heading font-bold">Mebel</h5>
                    <p class="text-xs opacity-70 mt-1">Fungsi dan estetika dalam balutan kayu pilihan.</p>
                </div>
            </div>
            <div class="group relative h-[400px] overflow-hidden rounded-[2rem] shadow-xl">
                <img src="../images/vas bunga.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A265F] to-transparent opacity-80"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-yellow-500 text-blue-900 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-block">Unit Produksi</span>
                    <h5 class="text-2xl font-serif-heading font-bold">Kerajinan</h5>
                    <p class="text-xs opacity-70 mt-1">Sentuhan tangan kreatif dalam kearifan lokal.</p>
                </div>
            </div>
            <div class="group relative h-[400px] overflow-hidden rounded-[2rem] shadow-xl">
                <img src="../images/batik 2.jpg" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
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

    <?php include 'footer.php'; ?>
</body>
</html>