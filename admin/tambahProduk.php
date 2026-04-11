<?php
session_start();
include '../php/koneksi.php';

// Cek Role Admin (Opsional - buka komen jika sudah siap)
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

$pesan = '';
$tipe_pesan = '';

// PROSES SIMPAN DALAM FILE YANG SAMA
if (isset($_POST['submit'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk']));
    $id_kategori = (int) $_POST['id_kategori'];
    $deskripsi   = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $bahan       = mysqli_real_escape_string($koneksi, trim($_POST['bahan']));
    $finishing   = mysqli_real_escape_string($koneksi, trim($_POST['finishing'] ?? ''));
    $dimensi     = mysqli_real_escape_string($koneksi, trim($_POST['dimensi'] ?? ''));
    $garansi     = mysqli_real_escape_string($koneksi, trim($_POST['garansi']));
    $harga       = (float) preg_replace('/[^0-9]/', '', $_POST['harga']);
    $stok        = (int) $_POST['stok'];

    $varian = $_POST['varian'] ?? [];
    if (empty($varian)) {
        $varian = ['Standard'];
    }

    if (empty($nama_produk) || empty($id_kategori) || empty($deskripsi) || empty($bahan) || empty($garansi)) {
        $pesan = "Mohon lengkapi data wajib terlebih dahulu.";
        $tipe_pesan = "error";
    } elseif ($harga <= 0) {
        $pesan = "Harga jual harus lebih dari 0.";
        $tipe_pesan = "error";
    } elseif ($stok < 0) {
        $pesan = "Stok tidak boleh kurang dari 0.";
        $tipe_pesan = "error";
    } elseif (!isset($_FILES['foto_utama']) || $_FILES['foto_utama']['error'] !== 0) {
        $pesan = "Foto utama wajib diupload.";
        $tipe_pesan = "error";
    } else {
        $nama_file = $_FILES['foto_utama']['name'];
        $tmp_file  = $_FILES['foto_utama']['tmp_name'];
        $ukuran    = $_FILES['foto_utama']['size'];

        $folder_tujuan = "../images/";

        if (!is_dir($folder_tujuan)) {
            mkdir($folder_tujuan, 0777, true);
        }

        $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $pesan = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
            $tipe_pesan = "error";
        } elseif ($ukuran > 2 * 1024 * 1024) {
            $pesan = "Ukuran foto terlalu besar. Maksimal 2MB.";
            $tipe_pesan = "error";
        } else {
            $nama_baru = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($nama_file));

            if (!move_uploaded_file($tmp_file, $folder_tujuan . $nama_baru)) {
                $pesan = "Upload foto gagal.";
                $tipe_pesan = "error";
            } else {
                mysqli_begin_transaction($koneksi);

                try {
                    // Simpan ke tabel produk
                    $query_produk = "INSERT INTO produk 
                        (id_kategori, nama_produk, deskripsi, bahan, finishing, dimensi, garansi, foto_produk)
                        VALUES
                        ('$id_kategori', '$nama_produk', '$deskripsi', '$bahan', '$finishing', '$dimensi', '$garansi', '$nama_baru')";

                    $simpan_produk = mysqli_query($koneksi, $query_produk);

                    if (!$simpan_produk) {
                        throw new Exception("Gagal simpan produk: " . mysqli_error($koneksi));
                    }

                    $id_produk = mysqli_insert_id($koneksi);

                    // Simpan ke tabel produk_varian
                    foreach ($varian as $nama_varian) {
                        $nama_varian = trim($nama_varian);

                        if ($nama_varian === '') {
                            continue;
                        }

                        $nama_varian = mysqli_real_escape_string($koneksi, $nama_varian);

                        $query_varian = "INSERT INTO produk_varian (id_produk, nama_varian, harga, stok)
                                         VALUES ('$id_produk', '$nama_varian', '$harga', '$stok')";

                        $simpan_varian = mysqli_query($koneksi, $query_varian);

                        if (!$simpan_varian) {
                            throw new Exception("Gagal simpan varian: " . mysqli_error($koneksi));
                        }
                    }

                    mysqli_commit($koneksi);
                    $pesan = "Produk berhasil ditambahkan.";
                    $tipe_pesan = "success";

                    // Biar form kosong lagi setelah sukses
                    $_POST = [];
                } catch (Exception $e) {
                    mysqli_rollback($koneksi);
                    $pesan = $e->getMessage();
                    $tipe_pesan = "error";
                }
            }
        }
    }
}

// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru | Ruang Karya Admin</title>
    <link href="../RuangKaryaCSS/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="font-sans-body bg-slate-100 font-sans text-slate-900">

    <main class="max-w-[90rem] mx-auto p-6 md:p-10">
        <h2 class="text-3xl font-bold text-blue-900 mb-10">Tambah Produk Baru</h2>

        <?php if (!empty($pesan)): ?>
            <div class="mb-6 p-4 rounded-xl border <?= $tipe_pesan === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <div class="xl:col-span-2 space-y-8">
                
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Informasi Dasar
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Nama Produk <span class="text-red-600">*</span></label>
                            <input type="text" name="nama_produk" placeholder="Masukkan nama produk..." required 
                                value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>"
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Kategori <span class="text-red-600">*</span></label>
                            <select name="id_kategori" required class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                                <option value="" disabled <?= !isset($_POST['id_kategori']) ? 'selected' : '' ?>>Pilih Kategori</option>
                                <?php while($row = mysqli_fetch_assoc($result_kategori)): ?>
                                    <option value="<?= $row['id_kategori'] ?>" <?= (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $row['id_kategori']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['nama_kategori']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Deskripsi Produk <span class="text-red-600">*</span></label>
                            <textarea name="deskripsi" rows="5" placeholder="Jelaskan detail produk kamu..." required 
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
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
                            <input type="text" name="bahan" placeholder="Contoh: Kayu Jati" required
                                value="<?= htmlspecialchars($_POST['bahan'] ?? '') ?>"
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Finishing</label>
                            <input type="text" name="finishing" placeholder="Contoh: Melamine Glossy"
                                value="<?= htmlspecialchars($_POST['finishing'] ?? '') ?>"
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Dimensi (PxLxT)</label>
                            <input type="text" name="dimensi" placeholder="Contoh: 100 x 50 x 40 cm"
                                value="<?= htmlspecialchars($_POST['dimensi'] ?? '') ?>"
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Garansi <span class="text-red-600">*</span></label>
                            <input type="text" name="garansi" placeholder="Contoh: 1 Tahun" required
                                value="<?= htmlspecialchars($_POST['garansi'] ?? '') ?>"
                                class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
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
                            if (!empty($_POST['varian']) && is_array($_POST['varian'])):
                                foreach ($_POST['varian'] as $v):
                                    $v = trim($v);
                                    if ($v === '') continue;
                            ?>
                                <span class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-2">
                                    <?= htmlspecialchars($v) ?>
                                    <button type="button" onclick="this.parentElement.remove()" class="hover:text-red-400">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <input type="hidden" name="varian[]" value="<?= htmlspecialchars($v) ?>">
                                </span>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="variant-input" placeholder="Tambah varian (Warna/Ukuran)..." class="flex-1 p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                            <button type="button" id="add-variant-btn" class="bg-blue-50 text-blue-900 font-bold px-5 rounded-xl hover:bg-blue-100 text-sm uppercase">Tambah</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Harga Jual (Rp) <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 font-bold text-slate-400">Rp</span>
                                <input type="text" id="harga" name="harga" placeholder="0" required inputmode="numeric"
                                    value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>"
                                    class="w-full p-3 pl-12 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none font-bold text-lg">
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-slate-600 mb-1">Stok Awal <span class="text-red-600">*</span></label>
                            <input type="number" name="stok" value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>" required 
                                class="w-full p-3 pl-12 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-yellow-400 outline-none font-bold text-lg appearance-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center relative">
                    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2 text-left">
                        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Foto Produk Utama <span class="text-red-600">*</span>
                    </h3>
                    
                    <label for="foto_utama" class="absolute top-8 right-8 text-sm font-semibold text-blue-700 cursor-pointer hover:text-yellow-600 flex items-center gap-1.5 z-10">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Upload
                    </label>
                    
                    <div class="aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-inner mb-4 bg-slate-50 flex items-center justify-center border-dashed border-2">
                        <img id="preview-foto" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                        <div id="placeholder-icon" class="text-slate-300">
                            <i class="fa-solid fa-image text-6xl"></i>
                            <p class="text-xs mt-2 font-medium text-slate-400">Belum ada foto dipilih</p>
                        </div>
                    </div>

                    <input type="file" id="foto_utama" name="foto_utama" accept="image/*" class="hidden" required onchange="previewImage(this)">
                    <p class="text-[10px] text-slate-400 italic text-left">* Ukuran maksimal 2MB (JPG, PNG, WEBP)</p>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" name="submit" class="flex-1 bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                        Upload Produk
                    </button>
                    <a href="dashboard.php" class="w-full bg-white text-slate-500 font-bold py-4 rounded-xl text-center border border-slate-200 hover:bg-slate-50 transition duration-300">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </form>
    </main>

    <script>
        const variantInput = document.getElementById('variant-input');
        const addVariantBtn = document.getElementById('add-variant-btn');
        const variantContainer = document.getElementById('variant-container');
        const hargaInput = document.getElementById('harga');

        addVariantBtn.addEventListener('click', function() {
            const value = variantInput.value.trim();
            if (value) {
                const existingVariants = [...document.querySelectorAll('input[name="varian[]"]')]
                    .map(input => input.value.toLowerCase());

                if (existingVariants.includes(value.toLowerCase())) {
                    variantInput.value = '';
                    variantInput.focus();
                    return;
                }

                const tag = document.createElement('span');
                tag.className = "bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-2";
                tag.innerHTML = `
                    ${value}
                    <button type="button" onclick="this.parentElement.remove()" class="hover:text-red-400"><i class="fa-solid fa-xmark"></i></button>
                    <input type="hidden" name="varian[]" value="${value}">
                `;
                variantContainer.appendChild(tag);
                variantInput.value = '';
                variantInput.focus();
            }
        });

        variantInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addVariantBtn.click();
            }
        });

        function previewImage(input) {
            const preview = document.getElementById('preview-foto');
            const placeholder = document.getElementById('placeholder-icon');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        hargaInput.addEventListener('input', function () {
            let angka = this.value.replace(/\D/g, '');
            this.value = angka ? new Intl.NumberFormat('id-ID').format(angka) : '';
        });
    </script>
</body>
</html>