<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk memastikan user sudah login
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: /inventory-app/public/login.php");
        exit;
    }
}

// Fungsi untuk membatasi akses khusus Admin
function requireAdmin() {
    requireLogin();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        exit("Akses ditolak: Halaman ini khusus untuk Administrator.");
    }
}
?>