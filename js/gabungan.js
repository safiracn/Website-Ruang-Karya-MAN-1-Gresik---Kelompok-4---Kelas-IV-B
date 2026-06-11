// 1. DATA PRODUK & hitung harga
const params = new URLSearchParams(window.location.search);

const namaProduk = params.get("nama") || "Meja Aksara";
const hargaProduk = parseInt(params.get("harga")) || 100000;
const imgProduk = params.get("img") || "meja belajar kotak.jpeg";

const produkTerpilih = {
    nama: namaProduk,
    harga: hargaProduk
};

const inputJumlah = document.getElementById("jumlah");
const displaySubtotal = document.getElementById("res-total");
const displayTotalFinal = document.getElementById("res-total-final");
const elemenNamaProduk = document.getElementById("checkout-nama");
const elemenImgProduk = document.getElementById("checkout-img");

if (elemenNamaProduk) {
    elemenNamaProduk.innerText = produkTerpilih.nama;
}

if (elemenImgProduk) {
    elemenImgProduk.src = "images/" + imgProduk;
    elemenImgProduk.alt = produkTerpilih.nama;
}

const hargaAwal = "Rp " + produkTerpilih.harga.toLocaleString("id-ID") + ",00";
if (displaySubtotal) displaySubtotal.innerText = hargaAwal;
if (displayTotalFinal) displayTotalFinal.innerText = hargaAwal;

if (inputJumlah) {
    inputJumlah.addEventListener("input", function () {
        let qty = parseInt(inputJumlah.value);

        if (isNaN(qty) || qty < 1) qty = 1;
        if (qty > 10) qty = 10;

        let totalHarga = qty * produkTerpilih.harga;
        let formatHarga = "Rp " + totalHarga.toLocaleString("id-ID") + ",00";

        if (displaySubtotal) displaySubtotal.innerText = formatHarga;
        if (displayTotalFinal) displayTotalFinal.innerText = formatHarga;
    });
}

// 2. VALIDASI & SUBMIT PESANAN
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
    Produk: ${inputJumlah.value}x ${produkTerpilih.nama}
    Total: ${displayTotalFinal.innerText}
    Metode: ${metode}`;

    alert(ringkasan);

    // Reset Form
    if (formElemen) formElemen.reset();
}

// js fira
const products = document.querySelectorAll(".product");
const categoryButtons = document.querySelectorAll(".category-btn");
const sortButtons = document.querySelectorAll(".sort-btn");

let activeCategory = "all";
let activeSort = null;

function updateCategoryButtons() {
  categoryButtons.forEach((btn) => {
    const category = btn.dataset.category;

    if (category === activeCategory) {
      btn.classList.remove("border", "border-slate-300", "text-slate-700");
      btn.classList.add("bg-blue-900", "text-white");
    } else {
      btn.classList.remove("bg-blue-900", "text-white");
      btn.classList.add("border", "border-slate-300", "text-slate-700");
    }
  });
}

function updateSortButtons() {
  sortButtons.forEach((btn) => {
    const sort = btn.dataset.sort;

    if (sort === activeSort) {
      btn.classList.remove("text-blue-900");
      btn.classList.add("bg-blue-900", "text-white");
    } else {
      btn.classList.remove("bg-blue-900", "text-white");
      btn.classList.add("border", "border-blue-900", "text-blue-900");
    }
  });
}

function applyFilters() {
  products.forEach((product) => {
    const matchCategory =
      activeCategory === "all" || product.dataset.category === activeCategory;

    let matchSort = true;

    if (activeSort === "popular") {
      matchSort = product.dataset.popular === "true";
    } else if (activeSort === "new") {
      matchSort = product.dataset.new === "true";
    }

    if (matchCategory && matchSort) {
      product.style.display = "block";
    } else {
      product.style.display = "none";
    }
  });
}

function filterCategory(category) {
  activeCategory = category;

  updateCategoryButtons();
  applyFilters();
}

function filterSort(sort) {
  if (activeSort === sort) {
    activeSort = null;
  } else {
    activeSort = sort;
  }

  updateSortButtons();
  applyFilters();
}

// tampilan mobile 
const mobileMenuButton = document.getElementById("mobile-menu-button");
const closeMenuButton = document.getElementById("close-menu-button");
const closeMenuBackdrop = document.getElementById("close-menu-backdrop");
const mobileMenu = document.getElementById("mobile-menu");
const mobileMenuContent = document.getElementById("mobile-menu-content");

if (mobileMenuButton && mobileMenu && mobileMenuContent) {
  mobileMenuButton.addEventListener("click", function () {
    mobileMenu.classList.remove("hidden");
    setTimeout(() => {
      mobileMenuContent.classList.remove("translate-x-full");
    }, 10);
  });
}

function closeMobileMenu() {
  if (mobileMenuContent) {
    mobileMenuContent.classList.add("translate-x-full");
  }

  setTimeout(() => {
    if (mobileMenu) {
      mobileMenu.classList.add("hidden");
    }
  }, 300);
}

if (closeMenuButton) {
  closeMenuButton.addEventListener("click", closeMobileMenu);
}

if (closeMenuBackdrop) {
  closeMenuBackdrop.addEventListener("click", closeMobileMenu);
}