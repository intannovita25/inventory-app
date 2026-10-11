<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

requireLogin();

$product_id = (int) ($_GET['product_id'] ?? 0);
if ($product_id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data produk
$stmtProd = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmtProd->execute(['id' => $product_id]);
$product = $stmtProd->fetch();

if (!$product) {
    die("Produk tidak ditemukan.");
}

// Proses tambah relasi supplier jika ada form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = (int) $_POST['supplier_id'];
    $supplier_price = (float) $_POST['supplier_price'];

    if ($supplier_id > 0 && $supplier_price >= 0) {
        $stmtIns = $pdo->prepare("INSERT INTO product_suppliers (product_id, supplier_id, supplier_price) VALUES (:pid, :sid, :price) ON DUPLICATE KEY UPDATE supplier_price = :price");
        $stmtIns->execute([
            'pid' => $product_id,
            'sid' => $supplier_id,
            'price' => $supplier_price
        ]);
        $success = "Supplier berhasil dihubungkan ke produk.";
    }
}

// Ambil daftar supplier yang sudah terhubung dengan produk ini
$stmtSup = $pdo->prepare("
    SELECT s.*, ps.supplier_price 
    FROM suppliers s
    JOIN product_suppliers ps ON ps.supplier_id = s.id
    WHERE ps.product_id = :pid
");
$stmtSup->execute(['pid' => $product_id]);
$productSuppliers = $stmtSup->fetchAll();

// Ambil semua supplier untuk pilihan dropdown form
$allSuppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Supplier Produk</title>
</head>
<body>
    <h1>Kelola Supplier untuk Produk: <?php echo e($product['name']); ?> (SKU: <?php echo e($product['sku']); ?>)</h1>
    <p><a href="index.php">Kembali ke Daftar Produk</a></p>
    <hr>

    <?php if (!empty($success)): ?>
        <p style="color: green; font-weight: bold;"><?php echo e($success); ?></p>
    <?php endif; ?>

    <h3>Tambah / Hubungkan Supplier</h3>
    <form action="" method="POST">
        <div>
            <label>Pilih Supplier:</label><br>
            <select name="supplier_id" required style="width: 300px; padding: 5px;">
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($allSuppliers as $sup): ?>
                    <option value="<?php echo $sup['id']; ?>"><?php echo e($sup['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>Harga dari Supplier (Rp):</label><br>
            <input type="number" name="supplier_price" step="0.01" min="0" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <button type="submit" style="padding: 6px 12px;">Hubungkan Supplier</button>
    </form>

    <h3 style="margin-top: 30px;">Daftar Supplier untuk Produk Ini</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Supplier</th>
                <th>Kontak</th>
                <th>Harga Supplier</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($productSuppliers) > 0): ?>
                <?php foreach ($productSuppliers as $ps): ?>
                <tr>
                    <td><?php echo e($ps['name']); ?></td>
                    <td><?php echo e($ps['contact']); ?></td>
                    <td>Rp <?php echo number_format($ps['supplier_price'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">Belum ada supplier yang terhubung dengan produk ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>