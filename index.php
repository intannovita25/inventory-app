<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

// Konfigurasi Pagination
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Ambil parameter search dan filter
$search = trim($_GET['search'] ?? '');
$categoryId = (int) ($_GET['category_id'] ?? 0);

// Buat query dasar
$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(p.name LIKE :search OR p.sku LIKE :search)";
    $params['search'] = "%$search%";
}

if ($categoryId > 0) {
    $whereClauses[] = "p.category_id = :category_id";
    $params['category_id'] = $categoryId;
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Hitung total data untuk pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $whereSql");
$countStmt->execute($params);
$totalData = $countStmt->fetchColumn();
$totalPages = ceil($totalData / $perPage);

// Ambil data produk dengan JOIN ke kategori
$query = "SELECT p.*, c.name AS category_name 
          FROM products p 
          JOIN categories c ON c.id = p.category_id 
          $whereSql 
          ORDER BY p.id DESC 
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Ambil daftar kategori untuk dropdown filter
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Produk</title>
</head>
<body>
    <h1>Daftar Produk Inventory</h1>
    <a href="../dashboard.php">Kembali ke Dashboard</a> | <a href="create.php">Tambah Produk</a>
    
    <!-- Form Search & Filter -->
    <form method="GET" style="margin: 20px 0;">
        <input type="text" name="search" placeholder="Cari nama atau SKU..." value="<?php echo e($search); ?>">
        <select name="category_id">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $categoryId === $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo e($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter & Cari</button>
    </form>

    <!-- Tabel Data Produk -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo e($p['sku']); ?></td>
                    <td><?php echo e($p['name']); ?></td>
                    <td><?php echo e($p['category_name']); ?></td>
                    <td>Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                    <td><?php echo e($p['stock']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Tidak ada data produk ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Navigasi Pagination -->
    <div style="margin-top: 20px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $categoryId; ?>" 
               style="padding: 5px 10px; border: 1px solid #ccc; <?php echo $page === $i ? 'background: #ddd;' : ''; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>