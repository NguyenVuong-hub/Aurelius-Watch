<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once "../config/db.php";

$message = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!$username || !$newPassword || !$confirmPassword) {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Mật khẩu nhập lại không khớp.";
    } else {

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE user SET password = ? WHERE username = ?"
        );
        $stmt->bind_param("ss", $hashed, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Đổi mật khẩu thành công. Vui lòng đăng nhập lại.";
        } else {
            $message = "Không tìm thấy tài khoản hoặc mật khẩu không thay đổi.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="/AureliusWatch/assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form">
        <div class="auth-box">
            <h2>Quên mật khẩu</h2>

            <?php if ($message): ?>
                <div class="auth-error">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                <button type="submit">Cập nhật mật khẩu</button>
            </form>

            <div class="auth-actions">
                <a href="/AureliusWatch/config/login.php">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>

    <div class="auth-image">
        <img src="/AureliusWatch/assets/images/signin.jpg" alt="Aurelius Watch">
    </div>
</div>

</body>
</html>
