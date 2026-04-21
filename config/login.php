<?php
session_start();
require_once "../config/db.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    $stmt = $conn->prepare("
        SELECT iduser, username, password, hoten, phone
        FROM user 
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {

        /* ===== 1. SET SESSION USER ===== */
        $_SESSION["user"] = [
            "id"       => $user["iduser"],
            "username" => $user["username"],
            "hoten"    => $user["hoten"],
            "phone"    => $user["phone"]
        ];

        $userId = $user["iduser"];

        /* ===== 2. MERGE CART SESSION -> USER ===== */
        if (!isset($_SESSION['cart_session'])) {
            $_SESSION['cart_session'] = session_id();
        }
        $sessionId = $_SESSION['cart_session'];

        // Nếu user chưa có cart active → gán cart session cho user
        $stmt = $conn->prepare("
            SELECT idcart FROM carts 
            WHERE user_id = ? AND status='active'
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userCart = $stmt->get_result()->fetch_assoc();

        if (!$userCart) {
            $stmt = $conn->prepare("
                UPDATE carts 
                SET user_id = ?
                WHERE session_id = ? 
                  AND status='active'
                  AND user_id IS NULL
            ");
            $stmt->bind_param("is", $userId, $sessionId);
            $stmt->execute();
        }
        
        // Nếu user vẫn chưa có cart active → tạo mới
$stmt = $conn->prepare("
    SELECT idcart FROM carts
    WHERE user_id = ? AND status = 'active'
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();

if (!$stmt->get_result()->fetch_assoc()) {
    $stmt = $conn->prepare("
        INSERT INTO carts (user_id, status)
        VALUES (?, 'active')
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

        /* ===== 3. REDIRECT ===== */
        header("Location: /AureliusWatch/index.php");
        exit;
    }

    $error = "Thông tin đăng nhập chưa chính xác. Vui lòng thử lại.";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/AureliusWatch/assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form">
        <div class="auth-box">
            <h2>Đăng nhập</h2>

            <?php if ($error): ?>
                <div class="auth-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <button type="submit">Đăng nhập</button>
            </form>

            <p>
                Chưa có tài khoản?
                <a href="register.php">Đăng ký</a>
            </p>

            <div class="auth-actions">
                <a href="/AureliusWatch/index.php" class="back-home">
                    Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <div class="auth-image">
        <img src="/AureliusWatch/assets/images/signin.jpg" alt="Aurelius Watch">
    </div>
</div>

</body>
</html>
