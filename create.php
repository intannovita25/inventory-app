<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

requireLogin(); // Wajib login

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id   = (int) $_POST['category_id'];
    $sku           = trim($_POST['sku']);
    $name          = trim($_POST['name']);
    $price         = (float) $_POST['price'];
    $stock         = (int) $_POST['stock'];
    $minimum_stock = (int) $_POST['minimum_stock'];

    // Validasi dasar
    if (empty($sku) || empty($name) || $category_id <= 0) {
        $error = "Semua kolom wajib diisi dengan benar.";
    } elseif ($price < 0 || $stock < 0 || $minimum_stock < 0) {
        $error = "Harga, stok, dan minimum stok tidak boleh bernilai negatif.";
    } else {
        try {
            // Gunakan prepared statement untuk keamanan dari SQL Injection
            $stmt = $pdo->prepare("INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES (:category_id, :sku, :name, :price, :stock, :minimum_stock)");
            $stmt->execute([
                'category_id'   => $category_id,
                'sku'           => $sku,
                'name'          => $name,
                'price'         => $price,
                'stock'         => $stock,
                'minimum_stock' => $minimum_stock
            ]);
            $success = "Produk baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            // Tangkap error jika SKU duplikat (UNIQUE constraint)
            $error = "Gagal menyimpan: SKU kemungkinan sudah terdaftar.";
        }
    }
}

// Ambil data kategori untuk pilihan dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru</title>
</head>
<body>
    <h1>Tambah Produk</h1>
    <p><a href="index.php">Kembali ke Daftar Produk</a> | <a href="../dashboard.php">Dashboard</a></p>
    <hr>

    <?php if (!empty($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo e($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green; font-weight: bold;"><?php echo e($success); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div>
            <label>Kategori:</label><br>
            <select name="category_id" required style="width: 300px; padding: 5px;">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo e($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>SKU (Kode Unik Produk):</label><br>
            <input type="text" name="sku" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Nama Produk:</label><br>
            <input type="text" name="name" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Harga (Rp):</label><br>
            <input type="number" name="price" step="0.01" min="0" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Stok Awal:</label><br>
            <input type="number" name="stock" min="0" value="0" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Minimum Stok (Peringatan):</label><br>
            <input type="number" name="minimum_stock" min="0" value="5" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <button type="submit" style="padding: 8px 15px;">Simpan Produk</button>
    </form>
</body>
</html>