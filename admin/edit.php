<?php
session_start();
require_once '../php/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../php/login.php");
    exit();
}

setcookie("last_admin_page", "edit_produk", time() + 3600, "/");

$namaAdmin = $_SESSION['nama_lengkap'] ?? 'Admin';

$id_produk = $_GET['id'] ?? null;

if ($id_produk) {
    $id_produk = (int)$id_produk;

    $query = "SELECT produk.*, kategori.nama_kategori 
              FROM produk 
              JOIN kategori ON produk.id_kategori = kategori.id_kategori 
              WHERE produk.id_produk = '$id_produk'";

    $result = mysqli_query($koneksi, $query);
    $produk = mysqli_fetch_assoc($result);

    if (!$produk) {
        die("Produk tidak ditemukan!");
    }
} else {
    die("ID produk tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Informasi Produk | Ruang Karya Admin</title>
    <link href="../RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="font-sans-body bg-slate-100 font-sans text-slate-900">

    <main class="max-w-[90rem] mx-auto p-6 md:p-10">
        <h2 class="text-3xl font-bold text-blue-900 mb-10">Ubah Informasi Produk</h2>

        <form action="proses_update_produk.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <input type="hidden" name="id" value="<?= $produk['id_produk'] ?>">

            <div class="xl:col-span-2 space-y-8">
                
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Informasi Dasar
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Nama Produk</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <input type="text" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required 
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Kategori</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <select name="id_kategori" class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                                <option value="1" <?= ($produk['id_kategori'] == 1) ? 'selected' : '' ?>>Mebel</option>
                                <option value="2" <?= ($produk['id_kategori'] == 2) ? 'selected' : '' ?>>Busana</option>
                                <option value="3" <?= ($produk['id_kategori'] == 3) ? 'selected' : '' ?>>Kerajinan Tangan</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Deskripsi Produk</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <textarea name="deskripsi" rows="5" required 
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Spesifikasi Detail
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Material</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <input type="text" name="bahan" value="<?= $produk['bahan'] ?? ''; ?>" required class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Finishing</label>
                            <input type="text" name="finishing" value="<?= htmlspecialchars($produk['finishing']) ?>" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Dimensi (PxLxT)</label>
                            <input type="text" name="dimensi" value="<?= $produk['dimensi'] ?? ''; ?>" placeholder="Contoh: 100 x 50 x 40 cm" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Garansi</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <input type="text" name="garansi" value="<?= htmlspecialchars($produk['garansi']) ?>" required class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Data Penjualan
                    </h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Varian Produk</label>
                        <div id="variant-container" class="flex flex-wrap gap-2 mb-3">
                            <?php 
                            // Simulasi data varian dari DB (asumsi dipisah koma)
                            $varian_db = "Natural, Coklat Gelap";
                            $tag_varian = explode(", ", $varian_db);
                            foreach($tag_varian as $tag): 
                            ?>
                            <span class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-2">
                                <?= $tag ?>
                                <button type="button" onclick="this.parentElement.remove()" class="hover:text-red-400"><i class="fa-solid fa-xmark"></i></button>
                                <input type="hidden" name="varian[]" value="<?= $tag ?>">
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="variant-input" placeholder="Tambah varian baru..." class="flex-1 p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                            <button type="button" id="add-variant-btn" class="bg-blue-50 text-blue-900 font-bold px-5 rounded-xl hover:bg-blue-100 text-sm uppercase">Tambah</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Harga Jual (Rp)</label>
                            <label class="text-sm font-medium text-red-600 mb-1">*</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 font-bold text-slate-400">Rp</span>
                                <input type="text" name="harga" 
                                    value="<?= $produk['harga'] ?? 0; ?>" 
                                    required class="w-full p-3 pl-12 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none font-bold text-lg">
                            </div>
                        </div>
                        
                        <div>
                            <label class=" text-sm font-medium text-slate-600 mb-1">Stok Tersedia</label>
                            <label class=" text-sm font-medium text-red-600 mb-1">*</label>
                            <div class="flex items-center gap-1 bg-slate-100 border border-slate-200 rounded-xl p-1">
                                <input type="number" name="stok" 
                                        value="<?= $produk['stok'] ?? 0; ?>" 
                                        required class="h-11 flex-1 text-center bg-transparent font-bold text-xl outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center relative">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2 text-left">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Foto Produk Utama
                        <label class="text-red-600 mb-1">*</label>
                    </h3>
                    
                    <label for="foto_utama" class="absolute top-8 right-8 text-sm font-semibold text-blue-700 cursor-pointer hover:text-yellow-600 flex items-center gap-1.5 z-10">
                        <i class="fa-solid fa-pen text-xs"></i> Ubah Foto
                    </label>
                    
                    <div class="aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-inner mb-4 bg-slate-50 flex items-center justify-center">
                        <img id="preview-foto" src="../images/<?= $produk['foto_produk'] ?>" alt="Foto Produk" class="w-full h-full object-cover">
                    </div>

                    <input type="file" id="foto_utama" name="foto_utama" accept="images/*" class="hidden" onchange="previewImage(this)">
                </div>

                <div class="flex flex-col sm:flex-row xl:flex-col gap-4">
                    <a href="dashboard.php" class="flex-1 bg-white text-blue-900 font-bold py-4 rounded-xl text-center border border-slate-200 hover:scale-[1.02] transition duration-300 shadow-sm">
                        Batalkan
                    </a>
                    <button type="submit" name="submit" class="flex-1 bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        // 1. Logika Stok (+/-)
        const stokInput = document.getElementById('stok-input');
        function changeStok(amount) {
            let currentStok = parseInt(stokInput.value) || 0;
            let newStok = currentStok + amount;
            if (newStok < 0) newStok = 0; // Stok tidak boleh negatif
            stokInput.value = newStok;
        }

        // 2. Logika Varian (Tambah & Hapus Tag)
        const variantInput = document.getElementById('variant-input');
        const addVariantBtn = document.getElementById('add-variant-btn');
        const variantContainer = document.getElementById('variant-container');

        addVariantBtn.addEventListener('click', function() {
            const value = variantInput.value.trim();
            if (value) {
                // Buat elemen tag baru
                const tag = document.createElement('span');
                tag.className = "bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-2";
                tag.innerHTML = `
                    ${value}
                    <button type="button" onclick="this.parentElement.remove()" class="hover:text-red-400"><i class="fa-solid fa-xmark"></i></button>
                    <input type="hidden" name="varian[]" value="${value}">
                `;
                variantContainer.appendChild(tag);
                variantInput.value = ''; // Reset input
                variantInput.focus();
            }
        });

        // 3. Logika Preview Foto Utama saat diubah
        function previewImage(input) {
            const preview = document.getElementById('preview-foto');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>