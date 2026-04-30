<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: /AureliusWatch/config/login.php");
    exit;
}

$message = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $oldPassword     = $_POST['old_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$oldPassword || !$newPassword || !$confirmPassword) {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Mật khẩu mới không khớp.";
    } else {

        $userId = $_SESSION['user']['id'];

        $stmt = $conn->prepare("SELECT password FROM user WHERE iduser = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($currentHash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($oldPassword, $currentHash)) {
            $message = "Mật khẩu cũ không đúng.";
        } else {

            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE user SET password = ? WHERE iduser = ?");
            $stmt->bind_param("si", $newHash, $userId);
            $stmt->execute();
            $stmt->close();

            session_destroy();
            header("Location: /AureliusWatch/config/login.php?changed=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="/AureliusWatch/assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form">
        <div class="auth-box">
            <h2>Đổi mật khẩu</h2>

            <?php if ($message): ?>
                <div class="auth-error">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="password" name="old_password" placeholder="Mật khẩu cũ" required>
                <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>

                <button type="submit">Cập nhật mật khẩu</button>
            </form>

            <div class="auth-actions">
                <a href="/AureliusWatch/index.php">← Quay lại trang chủ</a>
            </div>
        </div>
    </div>

    <div class="auth-image">
        <img src="/AureliusWatch/assets/images/signin.jpg" alt="Aurelius Watch">
    </div>
</div>

</body>
</html>
