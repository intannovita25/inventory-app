<?php
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int) $_POST['product_id'];
    $quantity   = (int) $_POST['quantity'];

    try {
        if ($quantity <= 0) {
            throw new Exception("Jumlah barang keluar harus lebih dari nol.");
        }

        // Mulai database transaction
        $pdo->beginTransaction();

        // 1. Ambil data stok produk dan kunci baris data (FOR UPDATE)
        $stmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Produk tidak ditemukan.");
        }

        // 2. Validasi semantik: pastikan stok mencukupi
        if ($quantity > $product['stock']) {
            throw new Exception("Stok tidak mencukupi! Stok saat ini untuk '{$product['name']}' adalah {$product['stock']}.");
        }

        // 3. Kurangi stok produk
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");
        $updateStmt->execute(['qty' => $quantity, 'id' => $product_id]);

        // 4. Catat histori ke tabel stock_movements (tipe 'OUT')
        $historyStmt = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, user_id) VALUES (:product_id, 'OUT', :quantity, :user_id)");
        $historyStmt->execute([
            'product_id' => $product_id,
            'quantity'   => $quantity,
            'user_id'    => $_SESSION['user']['id']
        ]);

        // Commit transaksi jika sukses
        $pdo->commit();
        $message = "Transaksi barang keluar berhasil dicatat dan stok diperbarui!";

    } catch (Throwable $e) {
        // Rollback transaksi jika ada kegagalan
        $pdo->rollBack();
        $error = "Transaksi gagal: " . $e->getMessage();
    }
}

// Ambil daftar produk untuk pilihan dropdown
$products = $pdo->query("SELECT id, sku, name, stock FROM products ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Barang Keluar</title>
</head>
<body>
    <h1>Form Barang Keluar (Stock Out)</h1>
    <p><a href="../dashboard.php">Kembali ke Dashboard</a> | <a href="in.php">Ke Barang Masuk</a></p>
    <hr>

    <?php if (!empty($message)): ?>
        <p style="color: green; font-weight: bold;"><?php echo e($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo e($error); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div>
            <label>Pilih Produk:</label><br>
            <select name="product_id" required style="width: 350px; padding: 5px;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?php echo $p['id']; ?>">
                        [<?php echo e($p['sku']); ?>] <?php echo e($p['name']); ?> (Stok Tersedia: <?php echo e($p['stock']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>Jumlah Keluar (Quantity):</label><br>
            <input type="number" name="quantity" min="1" required style="width: 350px; padding: 5px;">
        </div>
        <br>
        <button type="submit" style="padding: 8px 15px;">Proses Barang Keluar</button>
    </form>
</body>
</html>