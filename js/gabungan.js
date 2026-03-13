// 1. DATA PRODUK
const produkTerpilih = {
    nama: "Meja Aksara",
    harga: 100000
};

// 2. LOGIKA MENU MOBILE (Dikasih pengaman agar tidak merusak script lain)
const btn = document.getElementById("mobile-menu-button");
const closeBtn = document.getElementById("close-menu-button");
const menu = document.getElementById("mobile-menu");

if (btn && menu) {
    btn.addEventListener("click", function () {
        menu.classList.remove("hidden");
    });
}

if (closeBtn && menu) {
    closeBtn.addEventListener("click", function () {
        menu.classList.add("hidden");
    });
}

// 3. ELEMEN HITUNG HARGA
const inputJumlah = document.getElementById('jumlah');
const displaySubtotal = document.getElementById('res-total');
const displayTotalFinal = document.getElementById('res-total-final');

if (inputJumlah) {
    // Gunakan event 'input' agar harga berubah seketika saat angka diketik/diklik
    inputJumlah.addEventListener('input', function() {
        let qty = parseInt(inputJumlah.value);
        
        // Proteksi jika input kosong atau bukan angka
        if (isNaN(qty) || qty < 1) qty = 1;
        if (qty > 10) qty = 10;

        let totalHarga = qty * produkTerpilih.harga;
        let formatHarga = "Rp " + totalHarga.toLocaleString('id-ID') + ",00";

        // Update tampilan ke HTML
        if (displaySubtotal) displaySubtotal.innerText = formatHarga;
        if (displayTotalFinal) displayTotalFinal.innerText = formatHarga;
    });
}

// 4. VALIDASI & SUBMIT PESANAN
function submitFinal() {
    // A. Ambil elemen form-nya dulu
    const formElemen = document.getElementById('po-form');
    
    // B. Ambil VALUE (isinya) secara langsung dan teliti
    const namaValue = document.getElementById('nama').value.trim();
    const noTelpValue = document.getElementById('noTelp').value.trim();
    const provinsiValue = document.getElementById('provinsi').value.trim();
    const kotaValue = document.getElementById('kota').value.trim();
    const kodePosValue = document.getElementById('kodePos').value.trim();
    const detailValue = document.getElementById('detail').value.trim();
    
    // C. Ambil Radio Button Pengiriman
    const elPengiriman = document.querySelector('input[name="pengiriman"]:checked');
    const metode = elPengiriman ? elPengiriman.value : "Ambil";

    // D. Pola Regex
    const hanyaHuruf = /^[a-zA-Z\s]+$/;
    const hanyaAngka = /^[0-9]+$/;

    // --- PROSES VALIDASI ---

    // 1. Cek Kosong
    if (!namaValue || !noTelpValue || !provinsiValue || !kotaValue || !kodePosValue || !detailValue) {
        alert("Gagal! Semua kolom harus diisi.");
        return;
    }

    // 2. Validasi Nama
    if (!hanyaHuruf.test(namaValue)) {
        alert("Gagal! Nama harus berupa huruf (A-Z).");
        return;
    }

    // 3. Validasi No Telp
    if (!hanyaAngka.test(noTelpValue)) {
        alert("Gagal! No Telepon harus berupa angka saja.");
        return;
    }

    // 4. Validasi Provinsi
    if (!hanyaHuruf.test(provinsiValue)) {
        alert("Gagal! Provinsi harus berupa huruf.");
        return;
    }

    // 5. Validasi Kota
    if (!hanyaHuruf.test(kotaValue)) {
        alert("Gagal! Kota harus berupa huruf.");
        return;
    }

    // 6. Validasi Kode Pos
    if (!hanyaAngka.test(kodePosValue)) {
        alert("Gagal! Kode Pos harus angka.");
        return;
    }

    // --- JIKA LOLOS SEMUA ---
    const ringkasan = `Pesanan Berhasil!
-----------------------
Nama: ${namaValue}
Produk: ${inputJumlah.value}x Meja Belajar
Total: ${displayTotalFinal.innerText}
Metode: ${metode}`;

    alert(ringkasan);

    // Reset Form
    if (formElemen) formElemen.reset();
}