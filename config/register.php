<?php
session_start();
require_once "../config/db.php";

$error = null;

// Giữ lại dữ liệu khi submit lỗi
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$hoten    = $_POST['hoten'] ?? '';
$phone    = $_POST['phone'] ?? '';
$ngaysinh = $_POST['ngaysinh'] ?? '';
$gioitinh = $_POST['gioitinh'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($username);
    $email    = trim($email);
    $hoten    = trim($hoten);
    $phone    = trim($phone);
    $ngaysinh = trim($ngaysinh);
    $gioitinh = trim($gioitinh);
    $password = $_POST["password"] ?? '';
    $confirm  = $_POST["confirm"] ?? '';

    /* =========================
       VALIDATE BẮT BUỘC
    ========================= */
    if (
        $username === '' ||
        $email === '' ||
        $hoten === '' ||
        $phone === '' ||
        $ngaysinh === '' ||
        $gioitinh === '' ||
        $password === '' ||
        $confirm === ''
    ) {
        $error = "Vui lòng nhập đầy đủ tất cả thông tin.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ.";
    }
    elseif (!preg_match('/^0[0-9]{9}$/', $phone)) {
        $error = "Số điện thoại không hợp lệ.";
    }
    elseif (!in_array($gioitinh, ['0', '1'], true)) {
        $error = "Giới tính không hợp lệ.";
    }
    elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    elseif ($password !== $confirm) {
        $error = "Mật khẩu nhập lại không khớp.";
    }
    else {

        /* =========================
           CHECK USERNAME
        ========================= */
        $check = $conn->prepare("SELECT iduser FROM user WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại.";
        } else {

            /* =========================
               INSERT USER
            ========================= */
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("
                INSERT INTO user
                    (hoten, username, phone, ngaysinh, gioitinh, password, email, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'customer')
            ");

            $stmt->bind_param(
                "ssssiss",
                $hoten,
                $username,
                $phone,
                $ngaysinh,
                $gioitinh,
                $hash,
                $email
            );

            if ($stmt->execute()) {

                /* =========================
                   ADMIN NOTIFICATION
                ========================= */
                $userId = $conn->insert_id;

                $notiStmt = $conn->prepare("
                    INSERT INTO admin_notifications (type, title, target_id)
                    VALUES ('user', ?, ?)
                ");
                $title = "Đăng ký người dùng mới";
                $notiStmt->bind_param("si", $title, $userId);
                $notiStmt->execute();

                header("Location: /AureliusWatch/index.php");
                exit;
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="/AureliusWatch/assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form">
        <div class="auth-box">
            <h2>Đăng ký</h2>

            <?php if ($error): ?>
                <div class="auth-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="auth-grid">

    <div class="form-group">
        <label>Tên đăng nhập</label>
        <input type="text" name="username" placeholder="Tên đăng nhập"
               value="<?= htmlspecialchars($username) ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Email"
               value="<?= htmlspecialchars($email) ?>" required>
    </div>

    <div class="form-group">
        <label>Họ và tên</label>
        <input type="text" name="hoten" placeholder="Họ và tên"
               value="<?= htmlspecialchars($hoten) ?>" required>
    </div>

    <div class="form-group">
        <label>Số điện thoại</label>
        <input type="text" name="phone" placeholder="Số điện thoại"
               value="<?= htmlspecialchars($phone) ?>" required>
    </div>

    <div class="form-group">
        <label>Giới tính</label>
        <select name="gioitinh" placeholder="Giới tính" required>
            <option value="">-- Chọn --</option>
            <option value="1" <?= $gioitinh === '1' ? 'selected' : '' ?>>Nam</option>
            <option value="0" <?= $gioitinh === '0' ? 'selected' : '' ?>>Nữ</option>
        </select>
    </div>

    <div class="form-group">
        <label>Ngày sinh</label>
        <input type="date" name="ngaysinh"
               value="<?= htmlspecialchars($ngaysinh) ?>" required>
    </div>

    <div class="form-group">
        <label>Mật khẩu</label> 
        <input type="password" name="password" placeholder="Mật khẩu" required>
    </div>

    <div class="form-group">
        <label>Nhập lại mật khẩu</label>
        <input type="password" name="confirm" placeholder="Nhập lại mật khẩu" required>
    </div>

    <div class="form-actions">
        <button type="submit">Tạo tài khoản</button>
    </div>

</form>

            <p>
                Đã có tài khoản?
                <a href="login.php">Đăng nhập</a>
            </p>

            <div class="auth-actions">
                <a href="/AureliusWatch/login.php" class="back-home">Quay lại trang chủ</a>
            </div>
        </div>
    </div>

    <div class="auth-image">
        <img src="/AureliusWatch/assets/images/signup.jpg" alt="Aurelius Watch">
    </div>
</div>

</body>
</html>
