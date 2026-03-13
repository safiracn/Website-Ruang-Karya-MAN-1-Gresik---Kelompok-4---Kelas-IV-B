const btn = document.getElementById("mobile-menu-button");
const closeBtn = document.getElementById("close-menu-button"); // Ambil tombol silang
const menu = document.getElementById("mobile-menu");

// Buka menu saat tombol hamburger dipencet
btn.addEventListener("click", function () {
  menu.classList.remove("hidden");
});

// Tutup menu saat tombol silang dipencet
closeBtn.addEventListener("click", function () {
  menu.classList.add("hidden");
});