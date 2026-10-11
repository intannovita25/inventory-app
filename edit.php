<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Ambil data produk yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Produk tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id   = (int) $_POST['category_id'];
    $sku           = trim($_POST['sku']);
    $name          = trim($_POST['name']);
    $price         = (float) $_POST['price'];
    $minimum_stock = (int) $_POST['minimum_stock'];

    if (empty($sku) || empty($name) || $category_id <= 0) {
        $error = "Semua kolom wajib diisi dengan benar.";
    } elseif ($price < 0 || $minimum_stock < 0) {
        $error = "Harga dan minimum stok tidak boleh bernilai negatif.";
    } else {
        try {
            $updateStmt = $pdo->prepare("UPDATE products SET category_id = :category_id, sku = :sku, name = :name, price = :price, minimum_stock = :minimum_stock WHERE id = :id");
            $updateStmt->execute([
                'category_id'   => $category_id,
                'sku'           => $sku,
                'name'          => $name,
                'price'         => $price,
                'minimum_stock' => $minimum_stock,
                'id'            => $id
            ]);
            $success = "Data produk berhasil diperbarui!";
            
            // Refresh data produk terbaru
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui: SKU kemungkinan sudah digunakan produk lain.";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
</head>
<body>
    <h1>Edit Produk: <?php echo e($product['name']); ?></h1>
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
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo e($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>SKU (Kode Unik Produk):</label><br>
            <input type="text" name="sku" value="<?php echo e($product['sku']); ?>" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Nama Produk:</label><br>
            <input type="text" name="name" value="<?php echo e($product['name']); ?>" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Harga (Rp):</label><br>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo e($product['price']); ?>" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <div>
            <label>Minimum Stok (Peringatan):</label><br>
            <input type="number" name="minimum_stock" min="0" value="<?php echo e($product['minimum_stock']); ?>" required style="width: 300px; padding: 5px;">
        </div>
        <br>
        <button type="submit" style="padding: 8px 15px;">Simpan Perubahan</button>
    </form>
</body>
</html>