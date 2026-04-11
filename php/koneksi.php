<?php
$host = "localhost:3307"; 
$user = "root";
$pass = "safira2006";
$db   = "ruang_karya";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    // Kalau 3307 gagal, kita coba 3306 (standar)
    $koneksi = mysqli_connect($host, $user, $pass, $db);
    
    if(!$koneksi) {
        die("Koneksi Gagal Total: " . mysqli_connect_error());
    }
}
?>