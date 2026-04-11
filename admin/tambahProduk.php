<?php
session_start();
require_once '../php/koneksi.php';

// 1. Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../php/login.php");
    exit();
}

// --- LOGIKA PROSES TAMBAH (Jika form disubmit) ---
if (isset($_POST['submit'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $id_kategori = $_POST['id_kategori'];
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $bahan       = mysqli_real_escape_string($koneksi, $_POST['bahan']);
    $finishing   = mysqli_real_escape_string($koneksi, $_POST['finishing']);
    $dimensi     = mysqli_real_escape_string($koneksi, $_POST['dimensi']);
    $garansi     = mysqli_real_escape_string($koneksi, $_POST['garansi']);

    // Proses Upload Foto
    $foto_name = $_FILES['foto_utama']['name'];
    $tmp_name  = $_FILES['foto_utama']['tmp_name'];
    $ext       = pathinfo($foto_name, PATHINFO_EXTENSION);
    $nama_file_baru = uniqid() . "." . $ext;
    
    move_uploaded_file($tmp_name, "../images/" . $nama_file_baru);

    // A. Simpan ke Tabel Produk Utama
    $sql_p = "INSERT INTO produk (nama_produk, id_kategori, deskripsi, bahan, finishing, dimensi, garansi, foto_produk) 
              VALUES ('$nama_produk', '$id_kategori', '$deskripsi', '$bahan', '$finishing', '$dimensi', '$garansi', '$nama_file_baru')";
    
    if (mysqli_query($koneksi, $sql_p)) {
        $id_produk_baru = mysqli_insert_id($koneksi);

        // B. Proses Tabel Varian
        if (isset($_POST['nama_varian'])) {
            $namas  = $_POST['nama_varian'];
            $hargas = $_POST['harga_varian'];
            $stoks  = $_POST['stok_varian'];

            foreach ($namas as $index => $nama) {
                $nama_v = mysqli_real_escape_string($koneksi, $nama);
                $h = preg_replace('/[^0-9]/', '', $hargas[$index]);
                $s = preg_replace('/[^0-9]/', '', $stoks[$index]);
                
                $h = ($h === '') ? 0 : $h;
                $s = ($s === '') ? 0 : $s;

                $query_v = "INSERT INTO produk_varian (id_produk, nama_varian, harga, stok) 
                            VALUES ('$id_produk_baru', '$nama_v', '$h', '$s')";
                mysqli_query($koneksi, $query_v);
            }
        }

        echo "<script>
                alert('BERHASIL! Produk baru telah ditambahkan.');
                window.location.href='dashboard.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru | Ruang Karya Admin</title>
    <link href="../RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .no-spinner::-webkit-outer-spin-button,
        .no-spinner::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .no-spinner { -moz-appearance: textfield; }
    </style>
</head>
<body class="font-sans-body bg-slate-100 text-slate-900">

    <main class="max-w-[90rem] mx-auto p-6 md:p-10">
        <h2 class="text-3xl font-bold text-blue-900 mb-10">Tambah Produk Baru</h2>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <div class="xl:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Informasi Dasar
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Nama Produk <span class="text-red-600">*</span></label>
                            <input type="text" name="nama_produk" placeholder="Masukkan nama produk" required 
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Kategori <span class="text-red-600">*</span></label>
                            <select name="id_kategori" required class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="1">Mebel</option>
                                <option value="2">Busana</option>
                                <option value="3">Kerajinan Tangan</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Deskripsi Produk <span class="text-red-600">*</span></label>
                            <textarea name="deskripsi" rows="5" placeholder="Ceritakan detail produk..." required 
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Spesifikasi Detail
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Material <span class="text-red-600">*</span></label>
                            <input type="text" name="bahan" placeholder="Contoh: Kayu Jati" required class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Finishing</label>
                            <input type="text" name="finishing" placeholder="Contoh: Melamine Gloss" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Dimensi (PxLxT)</label>
                            <input type="text" name="dimensi" placeholder="100 x 50 x 40 cm" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Garansi <span class="text-red-600">*</span></label>
                            <input type="text" name="garansi" placeholder="Contoh: 1 Tahun" required class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Data Penjualan <label class="text-lg text-red-600 mb-1">*</label>
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
                                <tr class="bg-slate-50 rounded-xl">
                                    <td class="px-4 py-4 first:rounded-l-xl">
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="nama_varian[]" value="Standard" class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px]">
                                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-500"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="text" name="harga_varian[]" value="0" oninput="validasiAngka(this)" class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 outline-none font-semibold">
                                    </td>
                                    <td class="px-4 py-4 last:rounded-r-xl text-center">
                                        <input type="number" name="stok_varian[]" value="0" class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center font-semibold">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <input type="text" id="new-variant-name" placeholder="Tambah Varian Baru..." class="flex-1 p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm">
                        <button type="button" onclick="addNewVariantRow()" class="bg-blue-900 text-white px-6 rounded-xl font-bold text-sm hover:bg-blue-800 transition">TAMBAH</button>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center relative">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2 text-left">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Foto Produk Utama <span class="text-red-600">*</span>
                    </h3>
                    
                    <label for="foto_utama" class="absolute top-8 right-8 text-sm font-semibold text-blue-700 cursor-pointer hover:text-yellow-600 flex items-center gap-1.5 z-10">
                        <i class="fa-solid fa-plus text-xs"></i> Pilih Foto
                    </label>
                    
                    <div class="aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-inner mb-4 bg-slate-50 flex items-center justify-center border-dashed border-2">
                        <img id="preview-foto" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                        <div id="placeholder-icon" class="text-slate-300">
                            <i class="fa-solid fa-image text-6xl"></i>
                            <p class="text-xs mt-2 font-medium text-slate-400">Belum ada foto dipilih</p>
                        </div>
                    
                    </div>

                    <input type="file" id="foto_utama" name="foto_utama" accept="image/*" required class="hidden" onchange="previewImage(this)">
                    <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, WEBP (Maks 2MB)</p>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" name="submit" class="w-full bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                        SIMPAN PRODUK
                    </button>
                    <a href="dashboard.php" class="w-full bg-white text-slate-500 font-bold py-4 rounded-xl text-center border border-slate-200 hover:bg-slate-50 transition duration-300">
                        Batalkan
                    </a>
                </div>
            </div>
        </form>
    </main>

    <script>
       function previewImage(input) {
        const preview = document.getElementById('preview-foto');
        const placeholder = document.getElementById('placeholder-icon'); // Tambahkan ini

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden'); // Munculkan gambar
                placeholder.classList.add('hidden'); // Sembunyikan ikon placeholder
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

        function validasiAngka(input) {
            let value = input.value;
            let validValue = value.replace(/[^0-9]/g, '');
            if (value !== validValue) {
                input.value = validValue;
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