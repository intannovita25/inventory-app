<?php
session_start();
require_once '../config/database.php';
require_once '../helpers/escape.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong.";
    } else {
        // Menggunakan prepared statement untuk keamanan
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi password hash
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'] // 'admin' atau 'staff'
            ];
            
            // Redirect ke dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventory Management</title>
</head>
<body>
    <h2>Login Inventory System</h2>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo e($error); ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <div>
            <label>Username:</label><br>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>