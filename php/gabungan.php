<?php 
include 'koneksi.php';
// Header ini harus berisi bagian Logo sampai Navigasi agar tidak double
include 'header.php'; 

// Ambil data kategori untuk tombol filter di atas
$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<main class="min-h-screen bg-slate-50">
  <section class="pt-4 pb-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

      <div class="mt-0 mb-8 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
            <span class="text-sm font-semibold text-slate-500">Kategori</span>
            <div class="flex flex-wrap gap-2">
              <button onclick="filterCategory('all')" class="category-btn rounded-xl bg-blue-900 px-4 py-2 text-sm font-medium text-white shadow-md">
                Semua
              </button>
              <?php while($kat = mysqli_fetch_assoc($query_kategori)): ?>
                <button onclick="filterCategory('<?= strtolower($kat['nama_kategori']) ?>')" 
                        class="category-btn rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50 transition">
                  <?= $kat['nama_kategori'] ?>
                </button>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>

      <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        $sql = "SELECT p.*, k.nama_kategori, MIN(v.harga) as harga_mulai 
                FROM produk p 
                JOIN kategori k ON p.id_kategori = k.id_kategori 
                JOIN produk_varian v ON p.id_produk = v.id_produk 
                GROUP BY p.id_produk";
        $result = mysqli_query($koneksi, $sql);

        while($row = mysqli_fetch_assoc($result)):
        ?>
        <a href="DetailProduk.php?id=<?= $row['id_produk'] ?>" class="product-item block group" data-category="<?= strtolower($row['nama_kategori']) ?>"> 
        <article class="product-item flex h-full flex-col overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                 data-category="<?= strtolower($row['nama_kategori']) ?>">
          
          <div class="relative bg-slate-100 p-6 pt-8">
            <img src="../images/<?= $row['foto_produk'] ?>" 
                 alt="<?= $row['nama_produk'] ?>"
                 class="mx-auto h-44 w-full object-contain" />
          </div>

          <div class="flex flex-1 flex-col p-5">
            <h2 class="text-lg font-bold text-blue-900 line-clamp-1"><?= $row['nama_produk'] ?></h2>
            
            <p class="mt-1 min-h-[80px] text-sm text-slate-500 line-clamp-3">
              <?= $row['deskripsi'] ?>
            </p>

            <div class="mt-3">
              <span class="text-xl font-extrabold text-blue-900">
                Rp<?= number_format($row['harga_mulai'], 0, ',', '.') ?>
              </span>
            </div>

            <div class="mt-auto pt-5 flex gap-3">
              <!-- <a href="FormCheckout.php?id=<?= $row['id_produk'] ?>" -->
                <div class="flex-1 rounded-xl bg-blue-900 px-4 py-3 font-semibold text-white transition hover:bg-yellow-500 hover:text-blue-900 text-center block ">
                 Lihat Detail
               </div>
            </div>
          </div>
        </article>

        </a>
        <?php endwhile; ?>
      </div>
    </div>
  </section>
</main>

<script>
// Fungsi filter agar kategori bisa diklik tanpa reload
function filterCategory(category) {
    const products = document.querySelectorAll('.product-item');
    const buttons = document.querySelectorAll('.category-btn');

    buttons.forEach(btn => {
        btn.classList.remove('bg-blue-900', 'text-white');
        btn.classList.add('bg-white', 'text-slate-700', 'border');
    });
    event.currentTarget.classList.add('bg-blue-900', 'text-white');
    event.currentTarget.classList.remove('bg-white', 'text-slate-700', 'border');

    products.forEach(item => {
        if (category === 'all' || item.getAttribute('data-category') === category) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php include 'footer.php'; ?>