<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

$error = null;

// Nếu admin đã đăng nhập → vào dashboard
if (isset($_SESSION['admin'])) {
    header("Location: /AureliusWatch/admin/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password, status 
             FROM admins 
             WHERE username = ? 
             LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (!$admin || !password_verify($password, $admin['password'])) {
            $error = "Sai tài khoản hoặc mật khẩu.";
        } 
        elseif ($admin['status'] != 1) {
            $error = "Tài khoản admin đã bị khóa.";
        } 
        else {

            // ✅ SET SESSION ADMIN (QUAN TRỌNG)
            $_SESSION['admin'] = [
                'id'       => (int)$admin['id'],
                'username' => $admin['username']
            ];

            $_SESSION['admin_last_activity'] = time();

            // Update last_login
            $update = $conn->prepare(
                "UPDATE admins SET last_login = NOW() WHERE id = ?"
            );
            $update->bind_param("i", $admin['id']);
            $update->execute();

            // ✅ LOG ĐĂNG NHẬP (SAU KHI CÓ SESSION)
            admin_log(
                'ADMIN_LOGIN',
                'ADMIN',
                $admin['id'],
                'Admin đăng nhập hệ thống'
            );

            header("Location: /AureliusWatch/admin/dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Aurelius Watch</title>
    <link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin_auth.css">
</head>
<body class="admin-login-body">

<div class="admin-auth-container">
    <h2>ADMIN LOGIN</h2>

    <?php if (!empty($error)): ?>
        <div class="auth-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text"
               name="username"
               placeholder="Admin username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               required>

        <input type="password"
               name="password"
               placeholder="Password"
               required>

        <button type="submit">ĐĂNG NHẬP</button>
    </form>

    <div class="auth-actions">
        <a href="/AureliusWatch/index.php" class="back-home">
            Quay lại trang chủ
        </a>
    </div>

    <div class="admin-auth-footer">
        © 2025 Aurelius Watch
    </div>
</div>
</body>
</html>
