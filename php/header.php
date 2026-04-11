<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Deteksi Halaman Aktif
$current_page = basename($_SERVER['PHP_SELF']);

// 2. Cek Login menggunakan variabel dari login.php kamu
$is_logged_in = isset($_SESSION['login']) && $_SESSION['login'] === true;
$nama_user = $is_logged_in ? $_SESSION['nama_lengkap'] : "Guest";

// 3. Fungsi Inisial
function getInitial($name) {
    if (!$name || $name == "Guest") return "U";
    $words = explode(" ", trim($name));
    $initial = "";
    foreach ($words as $w) {
        if (!empty($w)) $initial .= strtoupper($w[0]);
    }
    return substr($initial, 0, 2); 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ruang Karya MAN 1 Gresik</title>
  <link rel="stylesheet" href="../RuangKaryaCSS/output.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="font-sans-body bg-slate-50 text-slate-800">

  <div class="w-full rounded-b-[28px] bg-blue-900 text-white text-sm shadow-sm">
    <div class="mx-auto flex flex-col md:flex-row items-center justify-between max-w-7xl px-6 py-3 gap-2">

      <div class="flex items-center gap-6">
        <span class="flex items-center gap-2">
          <i class="fa-solid fa-phone text-xs"></i> +031-3949544
        </span>
        <span class="flex items-center gap-2">
          <i class="fa-solid fa-envelope text-xs"></i> mangresik@kemenag.go.id
        </span>
      </div>

      <div class="flex gap-2 items-center">
        <?php if (!$is_logged_in): ?>
          <a href="Daftar.php" class="rounded-lg bg-yellow-500 px-4 py-1.5 font-bold text-blue-900 transition hover:bg-yellow-400">
            Daftar
          </a>
          <a href="login.php" class="rounded-lg bg-yellow-500 px-4 py-1.5 font-bold text-blue-900 transition hover:bg-yellow-400">
            Masuk
          </a>
        <?php else: ?>
          <span class="font-semibold italic text-yellow-500">Halo, <b class=font-semibold italic text-yellow-500"><?= htmlspecialchars($nama_user) ?></b></span>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <header class="bg-white sticky top-0 z-40 shadow-sm border-b border-slate-100">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

      <div class="flex items-center gap-4">
        <img src="../images/logo man.png" alt="Logo" class="h-12 w-auto md:h-16" />
        <div>
          <h1 class="text-xl md:text-3xl font-bold leading-tight text-blue-900">Ruang Karya</h1>
        <h2 class="text-sm md:text-xl italic font-semibold leading-tight text-blue-900">MAN 1 Gresik</h2>
        <p class="hidden md:block mt-1 text-xs italic text-blue-800">
          Islami, Cerdas, Unggul, Kompetitif, & Peduli Lingkungan
        </p>
        </div>
      </div>

      <nav class="hidden lg:block">
        <ul class="flex items-center gap-8 font-bold">
          <li>
            <a href="dashboardPembeli.php" class="<?= ($current_page == 'dashboardPembeli.php') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' ?>">Beranda</a>
          </li>
          <li>
            <a href="gabungan.php" class="<?= ($current_page == 'gabungan.php') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' ?>">Katalog</a>
          </li>
          <li>
            <a href="riwayat.php" class="<?= ($current_page == 'riwayat.php') ? 'text-yellow-500 border-b-2 border-blue-900 pb-1' : 'text-blue-900 hover:text-yellow-500 transition' ?>">Riwayat</a>
          </li>
          <li class="relative">
            <a href="keranjang.php" class="<?= ($current_page == 'keranjang.php') ? 'text-yellow-500' : 'text-blue-900 hover:text-yellow-500' ?> transition p-2">
              <i class="fa-solid fa-cart-shopping text-xl"></i>
            </a>
          </li>
          <li>
            <a href="profil_user.php" class="flex items-center gap-3 group pl-4 border-l border-slate-200">
              <div class="text-right hidden xl:block">
                <p class="text-[9px] text-slate-400 font-bold uppercase leading-none">Akun Saya</p>
                <p class="text-sm text-blue-900 group-hover:text-yellow-500 transition"><?= htmlspecialchars($nama_user) ?></p>
              </div>
              <div class="h-10 w-10 rounded-full border-2 transition shadow-sm flex items-center justify-center font-black
                <?= ($current_page == 'profil_user.php') ? 'border-yellow-500 bg-yellow-500 text-blue-900' : 'border-blue-900 bg-blue-900 text-white group-hover:bg-yellow-500 group-hover:border-yellow-500 group-hover:text-blue-900' ?>">
                <?= getInitial($nama_user) ?>
              </div>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>
  <main class="min-h-screen">