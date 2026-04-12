create database ruang_karya;
use ruang_karya;

-- 1. Tabel Kategori
CREATE TABLE kategori ( 
id_kategori INT AUTO_INCREMENT PRIMARY KEY, 
nama_kategori VARCHAR(100) NOT NULL 
);

-- 2. Tabel Produk 
CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    bahan VARCHAR(100),
    finishing VARCHAR(100),
    garansi VARCHAR(50),
    foto_produk VARCHAR(255),
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

-- 3. Tabel User   
CREATE TABLE user (
id_user INT AUTO_INCREMENT PRIMARY KEY, 
password VARCHAR(255) NOT NULL, 
nama_lengkap VARCHAR(100) NOT NULL, 
email VARCHAR(100) NOT NULL UNIQUE, 
no_telp VARCHAR(20), 
alamat TEXT, 
role ENUM('admin','user') NOT NULL DEFAULT 'user' 
);

-- 4. Tabel Produk Varian (Kunci agar tidak redundan)
-- Untuk Kursi Kartini: Isinya cuma 1 baris (All Size / Custom).
-- Untuk Baju: Isinya banyak baris (S, M, L, XL).
CREATE TABLE produk_varian (
    id_varian INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT,
    nama_varian VARCHAR(50), -- Contoh: 'S', 'M', atau '45x45x85'
    harga DECIMAL(12,2) NOT NULL,
    stok INT DEFAULT 0,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE
);

-- 5. Tabel Pembelian 
CREATE TABLE pembelian (
    id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    tgl_pembelian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nama_penerima VARCHAR(100) NOT NULL,
    no_telp_penerima VARCHAR(20) NOT NULL,
    provinsi VARCHAR(100) NOT NULL,
    kota_kabupaten VARCHAR(100) NOT NULL,
    kode_pos VARCHAR(10) NOT NULL,
    detail_alamat TEXT NOT NULL,
    metode_pengiriman ENUM('Ambil', 'Antar') NOT NULL,
    status_pembayaran ENUM('Sudah dibayar', 'Belum Dibayar') NOT NULL DEFAULT 'Belum Dibayar',
    status_kirim ENUM('Belum dikirim', 'Dikirim', 'Diterima') NOT NULL DEFAULT 'Belum dikirim',
    status_pesanan ENUM('Pending', 'Diproses', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Pending',
    total_harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

-- 6. Tabel Pembelian Detail
CREATE TABLE pembelian_detail (
    id_pembelian_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pembelian INT,
    id_varian INT, -- Hubungkan ke varian, bukan ke produk langsung
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_pembelian) REFERENCES pembelian(id_pembelian),
    FOREIGN KEY (id_varian) REFERENCES produk_varian(id_varian)
);

INSERT INTO kategori (id_kategori, nama_kategori) VALUES
(1, 'Mebel'),
(2, 'Busana'),
(3, 'Kerajinan Tangan');

INSERT INTO produk (id_kategori, nama_produk, deskripsi, bahan, finishing, garansi, foto_produk) VALUES
-- Card 1: Meja Aksara (Kategori Mebel, misal id_kategori = 1)
(1, 'Meja Aksara', 'Meja minimalis ukuran 110 × 60 cm yang kokoh dan cocok untuk belajar, bekerja, maupun kebutuhan sehari-hari.', 'Kayu Jati Belanda', 'Natural Glossy', '6 Bulan', 'meja belajar kotak.jpeg'),
-- Card 2: Meja Ksatria
(1, 'Meja Ksatria', 'Meja kayu elegan berukuran 110 × 70 cm dengan desain modern yang memberikan kenyamanan saat digunakan.', 'Kayu Mahoni', 'Dark Brown Doft', '1 Tahun', 'meja 2 lapis.jpg'),
-- Card 3: Meja Bulong
(1, 'Meja Bulong', 'Meja serbaguna ukuran 120 × 60 cm dengan tampilan simpel yang cocok melengkapi berbagai ruangan.', 'Kayu Pinus', 'Clear Gloss', '3 Bulan', 'meja lucu.jpg'),
-- Card 4: Kursi Kartini
(1, 'Kursi Kartini', 'Kursi kayu minimalis ukuran ±45 × 45 × 85 cm yang nyaman digunakan untuk berbagai kebutuhan.', 'Kayu Jati', 'Politur Tradisional', '1 Tahun', 'kursi.jpg'),
-- Card 5: Kursi Rahayu
(1, 'Kursi Rahayu', 'Kursi kayu elegan ukuran ±45 × 45 × 90 cm dengan desain sederhana dan kokoh.', 'Kayu Sonokeling', 'Natural Wax', '1 Tahun', 'kursi 2.jpg'),
-- Card 6: Batik Nusantara (Kategori Busana, misal id_kategori = 2)
(2, 'Batik Nusantara', 'Batik berkualitas dengan bahan nyaman dipakai tersedia ukuran S, M, dan L.', 'Katun Primisima', 'Hand Printing', 'Tidak Ada', 'batik 1.jpg'),
-- Card 7: Batik Nuansa Bening
(2, 'Batik Nuansa Bening', 'Kemeja batik motif elegan dengan bahan adem tersedia ukuran S, M, dan L.', 'Katun Silk', 'Cap Tradisional', 'Tidak Ada', 'batik 2.jpg'),
-- Card 8: Vas Bunga Anyam Premium (Kategori Kerajinan, misal id_kategori = 3)
(3, 'Vas Bunga Anyam Premium', 'Vas bunga anyaman handmade dengan ukuran 20 × 20 × 30 cm yang cocok untuk dekorasi ruangan.', 'Rotan Alam', 'Melamine Coating', '1 Bulan', 'vas bunga.jpg');

INSERT INTO produk_varian (id_produk, nama_varian, harga, stok) VALUES
-- =========================================================
-- PRODUK MEBEL (Hanya 1 Tipe/Standard)
-- =========================================================
-- Meja Aksara (id_produk: 1)
(1, 'Standard', 100000, 20),

-- Meja Ksatria (id_produk: 2)
(2, 'Standard', 180000, 15),

-- Meja Bulong (id_produk: 3)
(3, 'Standard', 150000, 10),

-- Kursi Kartini (id_produk: 4)
(4, 'Standard', 99999, 25),

-- Kursi Rahayu (id_produk: 5)
(5, 'Standard', 129000, 12),

-- =========================================================
-- PRODUK BUSANA / BAJU (Varian Ukuran S, M, L, XL, XXL)
-- =========================================================
-- Batik Nusantara (id_produk: 6)
(6, 'S', 100000, 10),
(6, 'M', 100000, 15),
(6, 'L', 100000, 20),
(6, 'XL', 105000, 10),
(6, 'XXL', 110000, 5),

-- Batik Nuansa Bening (id_produk: 7)
(7, 'S', 100000, 8),
(7, 'M', 100000, 12),
(7, 'L', 100000, 15),
(7, 'XL', 105000, 10),
(7, 'XXL', 110000, 5),

-- =========================================================
-- PRODUK KERAJINAN (Hanya 1 Tipe/Standard)
-- =========================================================
-- Vas Bunga Anyam Premium (id_produk: 8)
(8, 'Standard', 50000, 30);

INSERT INTO `user` (password, nama_lengkap, email, no_telp, alamat, role) VALUES 
(
  '12345', -- Ganti dengan password yang kamu mau
  'Admin', 
  'admin@mangresik.go.id', 
  '081332783500', 
  'Jl Anggrek', 
  'admin' -- Menentukan akun ini sebagai admin
);

CREATE TABLE keranjang (
    id_keranjang INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

CREATE TABLE keranjang_detail (
    id_keranjang_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_keranjang INT NOT NULL,
    id_varian INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_keranjang) REFERENCES keranjang(id_keranjang) ON DELETE CASCADE,
    FOREIGN KEY (id_varian) REFERENCES produk_varian(id_varian) ON DELETE CASCADE
);

UPDATE user 
SET password = '123456'
WHERE email = 'admin@mangresik.go.id';

UPDATE user
SET alamat = 'Jalan Raya Bungah No. 46, Kecamatan Bungah, Kabupaten Gresik, Jawa Timur'
WHERE id_user = 1;

ALTER TABLE produk ADD COLUMN dimensi VARCHAR(100) AFTER finishing;

select * from produk_varian;

select * from produk;
select * from keranjang;
select * from keranjang_detail;
select * from produk_varian;
select * from pembelian;
select * from pembelian_detail;