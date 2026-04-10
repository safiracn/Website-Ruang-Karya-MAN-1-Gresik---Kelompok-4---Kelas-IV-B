<?php
session_start();
require './php/koneksi.php';

// Simulasi data keranjang jika datang dari Katalog/Detail/Keranjang
// Jika data kosong, kita buat array kosong agar tidak error
$items = $_SESSION['checkout_items'] ?? []; 

// Hitung Total Awal
$total_akhir = 0;
foreach($items as $item) {
    $total_akhir += ($item['harga'] * $item['jumlah']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian | Ruang Karya MAN 1 Gresik</title>
    <link rel="stylesheet" href="./RuangKaryaCSS/output.css">
</head>
<body class="font-sans-body p-4 md:p-12 flex items-center justify-center min-h-screen bg-slate-100">
    <div class="bg-gray-50 w-full max-w-6xl mx-auto rounded-xl shadow-2xl overflow-hidden flex flex-col">
        
        <header class="bg-white px-6 py-5 flex items-center gap-4">
            <img src="images/LOGO.jpeg" alt="Logo" class="h-14 w-14 object-contain">
            <div>
                <h1 class="text-xl font-serif-heading font-bold text-blue-900 leading-tight">Ruang Karya</h1>
                <h2 class="text-sm italic font-semibold text-blue-900">MAN 1 Gresik</h2>
                <p class="text-[11px] md:text-sm italic text-blue-900 leading-snug">
                    Islami, Cerdas, Unggul, Kompetitif, &amp; Peduli Lingkungan
                </p>
            </div>
        </header>

        <form id="orderForm" action="proses_simpan.php" method="POST" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h2 class="text-blue-900 uppercase font-bold mb-6 tracking-wider">Informasi Pengiriman</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Penerima</label>
                            <input type="text" id="nama" name="nama" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">No. Telepon</label>
                            <input type="text" id="noTelp" name="noTelp" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Provinsi</label>
                                <input type="text" id="provinsi" name="provinsi" required class="w-full p-2.5 border border-slate-300 rounded-lg outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Kota/Kabupaten</label>
                                <input type="text" id="kota" name="kota" required class="w-full p-2.5 border border-slate-300 rounded-lg outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Kode Pos</label>
                            <input type="text" id="kodePos" name="kodePos" required class="w-full p-2.5 border border-slate-300 rounded-lg outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Detail Alamat (Nama Jalan/No Rumah)</label>
                            <textarea id="detail" name="detail" rows="3" required class="w-full p-2.5 border border-slate-300 rounded-lg outline-none"></textarea>
                        </div>

                        <div>
                            <h3 class="font-bold text-blue-900">Metode Pengiriman</h3>
                            <p class="italic text-xs mb-3 text-blue-900 opacity-80">Pilih salah satu metode pengiriman di bawah!</p>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="pengiriman" value="Ambil" checked class="hidden peer">
                                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:bg-yellow-100 peer-checked:border-yellow-400 font-semibold transition-all">Ambil</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="pengiriman" value="Antar" class="hidden peer">
                                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:bg-yellow-100 peer-checked:border-yellow-400 font-semibold transition-all">Antar</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-50 text-white border border-slate-200 p-6 rounded-xl shadow-lg">
                    <h2 class="text-blue-900 uppercase font-bold mb-6 tracking-wider">Ringkasan Produk</h2>
                    
                    <div id="product-list" class="max-h-[400px] overflow-y-auto space-y-4 pr-2">
                        <?php if(empty($items)): ?>
                            <p class="italic text-blue-900 opacity-70">Belum ada barang yang dipilih!</p>
                        <?php else: ?>
                            <?php foreach($items as $index => $item): ?>
                            <div class="flex items-center gap-4 bg-white/10 p-3 rounded-lg border border-white/10">
                                <img src="images/<?= $item['gambar'] ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <p class="font-bold"><?= $item['nama'] ?></p>
                                    <p class="text-xs text-blue-900">Jumlah: <?= $item['jumlah'] ?> pcs</p>
                                    <p class="text-yellow-500 font-bold">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <div class="flex justify-between items-center text-xl font-black">
                            <span class="text-blue-900">TOTAL</span>
                            <span class="text-yellow-500">Rp <?= number_format($total_akhir, 0, ',', '.') ?></span>
                        </div>
                        <input type="hidden" name="total_final" value="<?= $total_akhir ?>">
                        
                        <button type="button" onclick="validateAndSubmit()" class="w-full bg-blue-900 text-white font-black py-4 rounded-xl mt-6 hover:scale-[1.02] transition shadow-xl hover:bg-yellow-500 uppercase">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <footer class="p-4 text-center border-t border-slate-200 bg-white">
            <p class="text-gray-400 text-xs tracking-widest uppercase">&copy; 2026 Ruang Karya MAN 1 Gresik</p>
        </footer>
    </div>

    <script>
        function validateAndSubmit() {
            const fields = [
                { id: 'nama', label: 'Nama', type: 'alpha' },
                { id: 'noTelp', label: 'No. Telepon', type: 'num' },
                { id: 'provinsi', label: 'Provinsi', type: 'alpha' },
                { id: 'kota', label: 'Kota', type: 'alpha' },
                { id: 'kodePos', label: 'Kode Pos', type: 'num' },
                { id: 'detail', label: 'Detail Alamat', type: 'required' }
            ];

            const alphaRegex = /^[a-zA-Z\s']+$/;
            const numRegex = /^[0-9]+$/;

            for (let field of fields) {
                const element = document.getElementById(field.id);
                const val = element.value.trim();

                // Cek Kosong
                if (!val) {
                    alert(`Peringatan: ${field.label} tidak boleh kosong!`);
                    element.focus();
                    return false;
                }

                // Cek Alfabet (Nama, Kota, Provinsi)
                if (field.type === 'alpha' && !alphaRegex.test(val)) {
                    alert(`Peringatan: ${field.label} hanya boleh berisi huruf dan tanda petik (')`);
                    element.focus();
                    return false;
                }

                // Cek Nomor (NoTelp, KodePos)
                if (field.type === 'num' && !numRegex.test(val)) {
                    alert(`Peringatan: ${field.label} hanya boleh berisi angka!`);
                    element.focus();
                    return false;
                }
            }

            // Jika semua lolos, kirim form
            document.getElementById('orderForm').submit();
        }
    </script>
</body>
</html>