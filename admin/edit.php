<?php
session_start();
require_once '../php/koneksi.php';

// 1. Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../php/login.php");
    exit();
}

$id_produk = $_GET['id'] ?? null;

// --- LOGIKA PROSES UPDATE (Jika form disubmit) ---
if (isset($_POST['submit'])) {
    $id_target   = $_POST['id'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $id_kategori = $_POST['id_kategori'];
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $bahan       = mysqli_real_escape_string($koneksi, $_POST['bahan']);
    $finishing   = mysqli_real_escape_string($koneksi, $_POST['finishing']);
    $dimensi     = mysqli_real_escape_string($koneksi, $_POST['dimensi']);
    $garansi     = mysqli_real_escape_string($koneksi, $_POST['garansi']);

    // A. Update Tabel Produk Utama
    $sql_p = "UPDATE produk SET 
                nama_produk = '$nama_produk', 
                id_kategori = '$id_kategori', 
                deskripsi   = '$deskripsi',
                bahan       = '$bahan',
                finishing   = '$finishing',
                dimensi     = '$dimensi',
                garansi     = '$garansi'
              WHERE id_produk = '$id_target'";
    mysqli_query($koneksi, $sql_p);

    // B. Proses Tabel Varian (Harga & Stok per Varian)
    if (isset($_POST['nama_varian'])) {
        $namas  = $_POST['nama_varian'];
        $hargas = $_POST['harga_varian'];
        $stoks  = $_POST['stok_varian'];
        $ids    = $_POST['id_varian'] ?? []; // ID unik dari tabel produk_varian

        foreach ($namas as $index => $nama) {
            $nama_v = mysqli_real_escape_string($koneksi, $nama);
            
            // Satpam Angka: Hapus karakter selain angka
            $h = preg_replace('/[^0-9]/', '', $hargas[$index]);
            $s = preg_replace('/[^0-9]/', '', $stoks[$index]);
            
            // Default ke 0 jika kosong
            $h = ($h === '') ? 0 : $h;
            $s = ($s === '') ? 0 : $s;

            if (isset($ids[$index]) && !empty($ids[$index])) {
                // Update varian yang sudah ada
                $id_v = $ids[$index];
                $query_v = "UPDATE produk_varian SET 
                              nama_varian = '$nama_v', 
                              harga = '$h', 
                              stok = '$s' 
                            WHERE id_varian = '$id_v'";
            } else {
                // Insert sebagai varian baru
                $query_v = "INSERT INTO produk_varian (id_produk, nama_varian, harga, stok) 
                            VALUES ('$id_target', '$nama_v', '$h', '$s')";
            }
            mysqli_query($koneksi, $query_v);
        }
    }

    echo "<script>
            alert('BERHASIL! Data produk dan varian terbaru telah tersimpan.');
            window.location.href='dashboard.php';
          </script>";
    exit;
}

// --- LOGIKA AMBIL DATA (Untuk isi Form saat pertama load) ---
if ($id_produk) {
    $id_produk = (int)$id_produk;

    // Ambil data produk & kategori
    $query = "SELECT p.*, k.nama_kategori 
              FROM produk p 
              JOIN kategori k ON p.id_kategori = k.id_kategori 
              WHERE p.id_produk = '$id_produk' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    $produk = mysqli_fetch_assoc($result);

    if (!$produk) {
        die("Produk tidak ditemukan!");
    }

    // Ambil semua varian untuk tabel Data Penjualan
    $query_varian = "SELECT * FROM produk_varian WHERE id_produk = '$id_produk'";
    $res_varian = mysqli_query($koneksi, $query_varian);
    $list_varian = [];
    while($row_v = mysqli_fetch_assoc($res_varian)) {
        $list_varian[] = $row_v;
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
    <style>
    /* Hanya menghilangkan panah untuk input dengan class 'no-spinner' */
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spinner {
        -moz-appearance: textfield; /* Untuk Firefox */
    }
</style>
</head>
<body class="font-sans-body bg-slate-100 text-slate-900">

    <main class="max-w-[90rem] mx-auto p-6 md:p-10">
        <h2 class="text-3xl font-bold text-blue-900 mb-10">Ubah Informasi Produk</h2>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
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
    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-slate-600 text-sm">
                                <th class="px-4 py-2 font-medium">Varian Produk</th>
                                <th class="px-4 py-2 font-medium">Harga Jual (Rp)</th>
                                <th class="px-4 py-2 font-medium">Stok Tersedia</th>
                            </tr>
                        </thead>
                        <tbody id="variant-table-body">
                        <?php 
                        // Ambil semua data varian dari database
                        $query_v = "SELECT * FROM produk_varian WHERE id_produk = '$id_produk'";
                        $res_v = mysqli_query($koneksi, $query_v);

                        if (mysqli_num_rows($res_v) > 0):
                        while($v = mysqli_fetch_assoc($res_v)): 
                        ?>
                        <tr class="bg-slate-50 rounded-xl">
                            <td class="px-4 py-4 first:rounded-l-xl">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                        name="nama_varian[]" 
                                        value="<?= htmlspecialchars($v['nama_varian']) ?>" 
                                        placeholder="Nama Varian"
                                        class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px] focus:ring-2 focus:ring-yellow-400">
        
                                    <input type="hidden" name="id_varian[]" value="<?= $v['id_varian'] ?>">
        
                                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">
                                        <i class="fa-solid fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text" 
                                    name="harga_varian[]" 
                                    value="<?= $v['harga'] ?>" 
                                    oninput="validasiAngka(this)"
                                    class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 focus:border-blue-900 outline-none font-semibold">
                            </td>
                            <td class="px-4 py-4 last:rounded-r-xl text-center">
                                <input type="number" name="stok_varian[]" value="<?= $v['stok'] ?>" class="w-20 p-2 bg-transparent border-b border-slate-300 focus:border-blue-900 outline-none text-center font-semibold">
                            </td>
                        </tr>
                        <?php 
                        endwhile; 
                        else: 
                        // TAMPILAN JIKA TIDAK ADA VARIAN (DEFAULT)
                        ?>
                        <tr class="bg-slate-50 rounded-xl">
                            <td class="px-4 py-4 first:rounded-l-xl">
                                <span class="text-slate-400 italic text-sm">Tanpa Varian (Standard)</span>
                                <input type="hidden" name="nama_varian[]" value="Standard">
                            </td>
                            <td class="px-4 py-4">
                                <input type="text" 
                                    name="harga_varian[]" 
                                    value="<?= $v['harga'] ?>" 
                                    class="input-harga w-full p-2 bg-transparent border-b border-slate-300 focus:border-blue-900 outline-none font-semibold"
                                    oninput="validasiAngka(this)">
                            </td>
                            <td class="px-4 py-4 last:rounded-r-xl text-center">
                                <input type="number" name="stok_varian[]" placeholder="0" class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center">
                            </td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex gap-2">
                    <input type="text" id="new-variant-name" placeholder="Tambah Varian Baru..." class="flex-1 p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm">
                    <button type="button" onclick="addNewVariantRow()" class="bg-blue-900 text-white px-6 rounded-xl font-bold text-sm">TAMBAH</button>
                </div>
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

        function validasiAngka(input) {
            // Simpan nilai asli
            let value = input.value;
    
            // Hapus semua karakter yang BUKAN angka (0-9)
            let validValue = value.replace(/[^0-9]/g, '');
    
            // Jika nilai berubah (artinya ada huruf yang dihapus), beri peringatan
            if (value !== validValue) {
                alert("Kolom Harga Jual hanya boleh diisi dengan angka!");
                input.value = validValue; // Kembalikan ke nilai yang hanya angka
            }
        }

        function addNewVariantRow() {
            const input = document.getElementById('new-variant-name');
            const name = input.value.trim();
            if (name === "") return;

            const tbody = document.getElementById('variant-table-body');
            const newRow = document.createElement('tr');
            newRow.className = "bg-slate-50 rounded-xl";
            newRow.innerHTML = `
            <td class="px-4 py-4 first:rounded-l-xl">
            <div class="flex items-center gap-2">
                <input type="text" name="nama_varian[]" value="${name}" 
                       class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px]">
                <input type="hidden" name="id_varian[]" value="">
                <button type="button" onclick="this.closest('tr').remove()" class="text-red-500"><i class="fa-solid fa-trash"></i></button>
            </div>
            </td>
            <td class="px-4 py-4">
                <input type="text" name="harga_varian[]" value="0" oninput="validasiAngka(this)" class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 outline-none font-semibold">
            </td>
            <td class="px-4 py-4 last:rounded-r-xl text-center">
                <input type="number" name="stok_varian[]" value="0" class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center font-semibold">
            </td>
            `;
            tbody.appendChild(newRow);
            input.value = "";
        }
    </script>
</body>
</html>