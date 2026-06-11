<?php
if (!isset($activeMenu)) {
    $activeMenu = '';
}

$adminNama = $admin['nama_lengkap'] ?? 'Admin User';
$adminRole = 'System Root';
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($adminNama) . "&background=e2e8f0&color=1e3a8a&size=128";
?>

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-[255px] shrink-0 bg-blue-900 px-6 py-5 text-white flex flex-col justify-between">
        <div>
            <div class="mb-10 flex items-center gap-3">
                <img src="../images/logo man.png" alt="Logo MAN 1 Gresik" class="h-14 w-14 object-contain">
                <div>
                    <h1 class="font-serif-heading text-[22px] font-bold leading-tight">Ruang Karya</h1>
                    <p class="mt-0.5 text-[11px] tracking-wide text-blue-100">MAN 1 GRESIK</p>
                </div>
            </div>

            <nav>
                <ul class="space-y-3">
                    <li>
                        <a href="dashboard.php"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 transition <?= $activeMenu === 'dashboard' ? 'bg-yellow-500 font-semibold text-blue-900 shadow-sm hover:bg-yellow-400' : 'hover:bg-blue-800' ?>">
                            <span class="flex h-5 w-5 items-center justify-center">
                                <i class="fa-solid fa-house text-[15px]"></i>
                            </span>
                            <span>Beranda</span>
                        </a>
                    </li>

                    <li>
                        <a href="pesanan.php"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 transition <?= $activeMenu === 'pesanan' ? 'bg-yellow-500 font-semibold text-blue-900 shadow-sm hover:bg-yellow-400' : 'hover:bg-blue-800' ?>">
                            <span class="flex h-5 w-5 items-center justify-center">
                                <i class="fa-solid fa-cart-shopping text-[15px]"></i>
                            </span>
                            <span>Pesanan</span>
                        </a>
                    </li>

                    <li>
                        <a href="laporan.php"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 transition <?= $activeMenu === 'laporan' ? 'bg-yellow-500 font-semibold text-blue-900 shadow-sm hover:bg-yellow-400' : 'hover:bg-blue-800' ?>">
                            <span class="flex h-5 w-5 items-center justify-center">
                                <i class="fa-solid fa-chart-column text-[15px]"></i>
                            </span>
                            <span>Laporan</span>
                        </a>
                    </li>

                    <li>
                        <a href="profil_admin.php"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 transition <?= $activeMenu === 'profil' ? 'bg-yellow-500 font-semibold text-blue-900 shadow-sm hover:bg-yellow-400' : 'hover:bg-blue-800' ?>">
                            <span class="flex h-5 w-5 items-center justify-center">
                                <i class="fa-solid fa-user text-[15px]"></i>
                            </span>
                            <span>Akun Saya</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- LOGOUT -->
        <div class="pt-6">
            <a href="logout_admin.php"
               class="flex items-center gap-3 rounded-xl border border-red-800 bg-red-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:bg-red-800">
                <span class="flex h-5 w-5 items-center justify-center">
                    <i class="fa-solid fa-right-from-bracket text-[15px]"></i>
                </span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 px-8 py-6">

        <!-- TOPBAR -->
        <div class="mb-7 flex items-start justify-between gap-6">
            <div>
                <h2 class="font-serif-heading text-[48px] font-bold leading-none text-black">
                    <?= $pageTitle ?? 'Halaman Admin'; ?>
                </h2>
                <p class="mt-3 text-[15px] text-slate-500">
                    <?= $pageDesc ?? ''; ?>
                </p>
            </div>

            <div class="ml-auto flex items-center gap-5">
                <button class="flex h-11 w-11 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-200 hover:text-blue-900">
                    <i class="fa-regular fa-bell text-lg"></i>
                </button>

                <div class="flex items-center gap-3 border-l border-slate-300 pl-5">
                    <div class="text-right">
                        <p class="text-[15px] font-semibold leading-none text-slate-800">
                            <?= htmlspecialchars($adminNama); ?>
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            <?= $adminRole; ?>
                        </p>
                    </div>
                    <div class="h-12 w-12 overflow-hidden rounded-full bg-slate-200 shadow-sm ring-2 ring-white">
                        <img
                            src="<?= $avatarUrl; ?>"
                            alt="Admin User"
                            class="h-full w-full object-cover"
                        >
                    </div>
                </div>
            </div>
        </div>