<?php
require_once '../config/database.php';
require_once '../helpers/auth.php';
require_once '../helpers/escape.php';

// Pastikan user sudah login
requireLogin();

// 1. Total Produk (COUNT)
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// 2. Total Nilai Inventory (SUM stock * price)
$totalInventoryValue = $pdo->query("SELECT SUM(stock * price) FROM products")->fetchColumn() ?? 0;

// 3. Produk Stok Rendah (stock <= minimum_stock)
$stmtLowStock = $pdo->query("SELECT * FROM products WHERE stock <= minimum_stock");
$lowStockProducts = $stmtLowStock->fetchAll();
$totalLowStock = count($lowStockProducts);

// 4. Total Barang Masuk & Keluar Hari Ini / Keseluruhan (SUM stock_movements)
$summaryMovement = $pdo->query("
    SELECT 
        SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) AS total_in,
        SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) AS total_out
    FROM stock_movements
")->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Inventory Management System</title>
</head>
<body>
    <h1>Dashboard Inventory</h1>
    <p>Selamat datang, <strong><?php echo e($_SESSION['user']['username']); ?></strong> (Role: <?php echo e($_SESSION['user']['role']); ?>)</p>
    
    <!-- Navigasi Menu -->
    <nav style="margin-bottom: 20px;">
        <a href="products/index.php">Kelola Produk</a> | 
        <a href="stock/in.php">Barang Masuk</a> | 
        <a href="stock/out.php">Barang Keluar</a> | 
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <!-- Menu khusus admin -->
            <span style="color: gray;">|</span> <span style="color: blue;">Panel Admin Aktif</span>
        <?php endif; ?>
        <a href="logout.php" style="color: red; float: right;">Logout</a>
    </nav>
    <hr>

    <h2>Statistik Ringkas</h2>
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div style="border: 1px solid #ccc; padding: 15px; width: 200px;">
            <h3>Total Produk</h3>
            <p style="font-size: 24px; font-weight: bold;"><?php echo e($totalProducts); ?></p>
        </div>
        <div style="border: 1px solid #ccc; padding: 15px; width: 200px;">
            <h3>Nilai Inventory</h3>
            <p style="font-size: 20px; font-weight: bold;">Rp <?php echo number_format($totalInventoryValue, 0, ',', '.'); ?></p>
        </div>
        <div style="border: 1px solid #ccc; padding: 15px; width: 200px;">
            <h3>Barang Masuk (Total)</h3>
            <p style="font-size: 24px; font-weight: bold; color: green;"><?php echo e($summaryMovement['total_in'] ?? 0); ?></p>
        </div>
        <div style="border: 1px solid #ccc; padding: 15px; width: 200px;">
            <h3>Barang Keluar (Total)</h3>
            <p style="font-size: 24px; font-weight: bold; color: red;"><?php echo e($summaryMovement['total_out'] ?? 0); ?></p>
        </div>
    </div>

    <h2>Peringatan Stok Rendah ($\le$ Minimum Stok)</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Stok Saat Ini</th>
                <th>Minimum Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($totalLowStock > 0): ?>
                <?php foreach ($lowStockProducts as $lp): ?>
                <tr>
                    <td><?php echo e($lp['sku']); ?></td>
                    <td><?php echo e($lp['name']); ?></td>
                    <td style="color: red; font-weight: bold;"><?php echo e($lp['stock']); ?></td>
                    <td><?php echo e($lp['minimum_stock']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Aman! Tidak ada produk dengan stok rendah.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>