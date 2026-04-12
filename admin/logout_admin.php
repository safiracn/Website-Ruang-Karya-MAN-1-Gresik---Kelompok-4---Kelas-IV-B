/* digunakan untuk mengakhiri sesi login admin dengan cara menghapus seluruh data session, 
sehingga status login hilang dan pengguna akan diarahkan kembali ke halaman login. */
<?php
session_start();

/* Hapus semua session */
$_SESSION = [];
session_unset();
session_destroy();

/* Redirect ke login */
header("Location: ../php/login.php");
exit;