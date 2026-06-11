<?php
session_start();

/* Hapus semua session */
$_SESSION = [];
session_unset();
session_destroy();

/* Redirect ke login */
header("Location: ../php/login.php");
exit;