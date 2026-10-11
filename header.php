<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; color: #333; }
        nav { background: #333; color: #fff; padding: 10px; margin-bottom: 20px; }
        nav a { color: #fff; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <nav>
        <a href="/inventory-app/public/dashboard.php">Dashboard</a>
        <a href="/inventory-app/public/products/index.php">Produk</a>
        <a href="/inventory-app/public/stock/in.php">Barang Masuk</a>
        <a href="/inventory-app/public/stock/out.php">Barang Keluar</a>
        <a href="/inventory-app/public/logout.php" style="float: right; color: #ff9999;">Logout</a>
    </nav>
    <div class="container">