<?php
session_start();
// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit;
?>