<?php
session_start();
require 'koneksi.php';

if (!isset($_POST['submit'])) {
    header("Location: Daftar.php");
    exit;
}

$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$password = trim($_POST['password'] ?? '');
$konfirmasi_password = trim($_POST['konfirmasi_password'] ?? '');

$query_string = http_build_query([
    'nama'   => $nama_lengkap,
    'email'  => $email,
    'telp'   => $no_telp,
    'alamat' => $alamat
]);

if ($nama_lengkap === '' || $email === '' || $no_telp === '' || $alamat === '' || $password === '' || $konfirmasi_password === '') {
    header("Location: Daftar.php?error=empty&" . $query_string);
    exit;
}

if (!preg_match("/^[a-zA-Z'`.\\s]+$/", $nama_lengkap)) {
    header("Location: Daftar.php?error=nama_invalid&" . $query_string);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: Daftar.php?error=email_invalid&" . $query_string);
    exit;
}

if (!preg_match('/^[0-9]+$/', $no_telp)) {
    header("Location: Daftar.php?error=telp_invalid&" . $query_string);
    exit;
}

if (strlen($password) < 6) {
    header("Location: Daftar.php?error=password_short&" . $query_string);
    exit;
}

if ($password !== $konfirmasi_password) {
    header("Location: Daftar.php?error=password_mismatch&" . $query_string);
    exit;
}

$email_escape = mysqli_real_escape_string($koneksi, $email);
$cek_email = mysqli_query($koneksi, "SELECT id_user FROM user WHERE email = '$email_escape' LIMIT 1");

if (mysqli_num_rows($cek_email) > 0) {
    header("Location: Daftar.php?error=email_exists&" . $query_string);
    exit;
}

$nama_escape = mysqli_real_escape_string($koneksi, $nama_lengkap);
$telp_escape = mysqli_real_escape_string($koneksi, $no_telp);
$alamat_escape = mysqli_real_escape_string($koneksi, $alamat);
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO user (nama_lengkap, email, no_telp, alamat, password, role)
          VALUES ('$nama_escape', '$email_escape', '$telp_escape', '$alamat_escape', '$password_hash', 'user')";

if (mysqli_query($koneksi, $query)) {
    header("Location: login.php?success=register");
    exit;
} else {
    header("Location: Daftar.php?error=db&" . $query_string);
    exit;
}
?>