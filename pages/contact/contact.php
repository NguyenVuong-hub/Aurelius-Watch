<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Lấy thông tin user từ session nếu đã đăng nhập
$userId    = $_SESSION['user']['id'] ?? null;
$userName  = $_SESSION['user']['hoten'] ?? '';
$userPhone = $_SESSION['user']['phone'] ?? '';

$success = '';
$error = '';
$showZalo = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nếu user đã đăng nhập, lấy từ session, nếu chưa thì lấy từ form
    $hoten = $userName ?: trim($_POST['hoten'] ?? '');
    $phone = $userPhone ?: trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$hoten || !$phone || !$message) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } else {
        $idUserValue = $userId ?? null;

        // Chèn vào CSDL
        $stmt = $conn->prepare("INSERT INTO contact (iduser, hoten, phone, message, status, created_at) VALUES (?, ?, ?, ?, 'Mới', NOW())");
        $stmt->bind_param("isss", $idUserValue, $hoten, $phone, $message);

        if ($stmt->execute()) {

            // Lấy ID contact vừa tạo
            $contactId = $conn->insert_id;

            // ==========================
            // CREATE ADMIN NOTIFICATION
            // ==========================
            $notiStmt = $conn->prepare("
                INSERT INTO admin_notifications (type, title, target_id)
                VALUES ('contact', ?, ?)
            ");

            $title = "Liên hệ mới từ khách hàng";
            $notiStmt->bind_param("si", $title, $contactId);
            $notiStmt->execute();

            $success = 'Cảm ơn bạn! Chúng tôi đã nhận được thông tin liên hệ của bạn.';
            $showZalo = true;

        } else {
            $error = 'Đã có lỗi xảy ra, vui lòng thử lại.';
        }

        $stmt->close();
    }
}
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/contact.css">

<!-- HERO SECTION -->
<section class="contact-hero">
    <div class="hero-content">
        <span class="hero-brand">AURELIUS WATCH</span>
        <h1>Liên hệ với chúng tôi</h1>
        <p>Chúng tôi luôn lắng nghe để nâng tầm trải nghiệm của bạn với đồng hồ xa xỉ.</p>
    </div>
</section>

<!-- CONTACT FORM -->
<section class="contact-form-section">
    <div class="form-container">
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
            <label>Họ và tên</label>
            <input 
                type="text" 
                name="hoten" 
                value="<?= htmlspecialchars($userName) ?>" 
                placeholder="Nhập họ tên" 
                <?= $userId ? 'readonly' : 'required' ?>
            >

            <label>Số điện thoại</label>
            <input 
                type="text" 
                name="phone" 
                value="<?= htmlspecialchars($userPhone) ?>" 
                placeholder="Nhập số điện thoại" 
                <?= $userId ? 'readonly' : 'required' ?>
            >

            <label>Nội dung liên hệ</label>
            <textarea name="message" rows="6" placeholder="Nhập nội dung liên hệ..." required></textarea>

            <button type="submit" class="btn-gold">Gửi liên hệ</button>
        </form>

        <?php if ($showZalo): ?>
            <div class="zalo-box">
                <p><strong>Cảm ơn bạn đã liên hệ với AURELIUS WATCH.</strong>
                   Chúng tôi trân trọng sự quan tâm của bạn. 
                   <strong>Để được phục vụ nhanh chóng và cá nhân hóa hơn, hãy liên hệ trực tiếp với chúng tôi qua Zalo:</strong></p>
                <a href="https://zalo.me/0362165567" target="_blank" class="btn-gold">Chat Zalo với AURELIUS WATCH</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>