/* digunakan untuk mengakhiri sesi login user dengan cara menghapus seluruh data session, 
sehingga status login hilang dan pengguna akan diarahkan kembali ke halaman login. */
<?php
session_start();

/* hapus semua session */
$_SESSION = [];
session_unset();
session_destroy();

/* kembali ke login */
header("Location: login.php");
exit;
?>