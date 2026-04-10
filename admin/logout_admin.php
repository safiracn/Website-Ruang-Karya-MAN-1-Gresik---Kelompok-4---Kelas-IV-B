<?php
session_start();

/* hapus semua session */
$_SESSION = [];
session_unset();
session_destroy();

/* hapus cookie login kalau nanti dipakai */
setcookie('remember_email', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');
setcookie('login_user', '', time() - 3600, '/');

/* kembali ke login */
header("Location: login.php");
exit;
?>