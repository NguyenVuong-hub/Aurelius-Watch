<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$showBirthdayPopup = false;

if (isset($_SESSION['user'], $conn)) {

    $user = $_SESSION['user'];
    $userId = (int)($user['iduser'] ?? $user['id'] ?? 0);

    // Lấy ngày sinh từ DB
    $stmt = $conn->prepare("
        SELECT ngaysinh 
        FROM user 
        WHERE iduser = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($ngaysinh);
    $stmt->fetch();
    $stmt->close();

    if (!empty($ngaysinh)) {

        if (date('m-d') === date('m-d', strtotime($ngaysinh))) {

            $year = date('Y');

            $check = $conn->prepare("
                SELECT 1 
                FROM birthday_popup_log
                WHERE iduser = ? AND shown_year = ?
                LIMIT 1
            ");
            $check->bind_param("ii", $userId, $year);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {
                $showBirthdayPopup = true;
                $notiStmt = $conn->prepare("
    INSERT INTO user_notifications (iduser, type, title, message)
    VALUES (?, 'birthday', 'Chúc mừng sinh nhật',
    'Aurelius Watch kính chúc Quý khách một ngày sinh nhật trọn vẹn.')
");

if ($notiStmt) {
    $notiStmt->bind_param("i", $userId);
    $notiStmt->execute();
    $notiStmt->close();
}
            }

            $check->close();
        }
    }
}

$cartCount = 0;

if (isset($_SESSION['user']['id'])) {
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(ci.quantity), 0)
        FROM carts c
        JOIN cart_items ci ON c.idcart = ci.idcart
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->bind_param("i", $_SESSION['user']['id']);
} else {
    $sid = $_SESSION['cart_session'] ?? session_id();
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(ci.quantity), 0)
        FROM carts c
        JOIN cart_items ci ON c.idcart = ci.idcart
        WHERE c.session_id = ? AND c.status = 'active'
    ");
    $stmt->bind_param("s", $sid);
}

$stmt->execute();
$cartCount = (int)($stmt->get_result()->fetch_row()[0] ?? 0);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Aurelius Watch</title>

    <link rel="stylesheet" href="/AureliusWatch/assets/css/main.css">

    <?php
    if (strpos($_SERVER['REQUEST_URI'], '/blog/') !== false) {
        echo '<link rel="stylesheet" href="/AureliusWatch/assets/css/blog.css">';
    }
    ?>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?
family=Playfair+Display:ital,wght@0,400;0,600;1,500&
family=Cormorant+Garamond:wght@400;500;600&
family=Inter:wght@300;400;500&
display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?
family=Great+Vibes&
display=swap" rel="stylesheet">

</head>

<body>

<header class="site-header">

<!-- ===== TOP BAR ===== -->
<div class="top-bar">
    <div class="container top-bar-content">

        <div class="top-left">
            Chào mừng bạn đến với <strong>AURELIUS WATCH!</strong>
        </div>

        <div class="top-right">
        <?php if (!isset($_SESSION['user'])): ?>
            <!-- GUEST -->
            <a href="/AureliusWatch/config/login.php">Đăng nhập</a>
            <span>|</span>
            <a href="/AureliusWatch/config/register.php">Đăng ký</a>

        <?php else: ?>
            <!-- ĐÃ ĐĂNG NHẬP -->
            <div class="user-box">
    <div class="user-dropdown">
        <span class="username">
            <?=$_SESSION['user']['username']?> 
            <i class="fa-solid fa-caret-down"></i>
        </span>

        <div class="dropdown-menu">
            <a href="/AureliusWatch/pages/order/my_order.php">
                Đơn hàng của tôi
            </a>
            <a href="/AureliusWatch/config/change_password.php">
                Đổi mật khẩu
            </a>
            <a href="/AureliusWatch/config/logout.php">
                Đăng xuất
            </a>
        </div>
    </div>
</div>

        <?php endif; ?>
        </div>

    </div>
</div>

<!-- ===== NAVBAR ===== -->
<div class="navbar">
    <div class="container navbar-content">

        <div class="logo">
            <a href="/AureliusWatch/index.php">
                <img src="/AureliusWatch/assets/images/logo.png" alt="Aurelius Watch">
            </a>
        </div>

        <ul class="menu">
            <li><a href="/AureliusWatch/index.php">Trang chủ</a></li>
            <li><a href="/AureliusWatch/pages/product/list.php">Sản phẩm</a></li>
            <li><a href="/AureliusWatch/pages/blog/list.php">Bài viết</a></li>
            <li><a href="/AureliusWatch/pages/contact/contact.php">Liên hệ</a></li>
            <li><a href="/AureliusWatch/pages/about.php">Về chúng tôi</a></li>
        </ul>

        <div class="nav-icons">
            <a href="javascript:void(0)" id="btnSearch">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>
            
            <a href="/AureliusWatch/pages/cart/cart.php">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <div class="header-search" id="headerSearch">
    <form action="/AureliusWatch/pages/product/list.php" method="GET">
        <input
            type="text"
            name="q"
            placeholder="Tìm kiếm đồng hồ..."
            autocomplete="off"
        >
    </form>
</div>

            <!-- 🔔 USER NOTIFICATION -->
            <?php if (isset($_SESSION['user'])): ?>
    <a class="user-notification" id="userNoti" href="javascript:void(0)">
        <i class="fa-solid fa-bell"></i>
        <span class="notify-badge" id="userNotiBadge" style="display:none">0</span>

        <div class="notify-dropdown">
            <div class="notify-header">Thông báo</div>
            <div id="userNotiList">
                <div class="notify-item">Không có thông báo</div>
            </div>
        </div>
            </a>
    <?php endif; ?>
        </div>
        
        <div class="header-search" id="headerSearch">
    <form action="/AureliusWatch/pages/product/list.php" method="GET">
        <input
            type="text"
            name="q"
            placeholder="Tìm kiếm đồng hồ..."
            autocomplete="off"
        >
    </form>
</div>

    </div>
</div>

</header>

<?php if ($showBirthdayPopup): ?>
<div class="birthday-overlay" id="birthdayOverlay">
    <div class="birthday-popup luxury-dark">

        <button class="birthday-close" id="birthdayClose">&times;</button>

        <div class="birthday-brand">AURELIUS WATCH</div>

        <div class="birthday-divider"></div>

        <h2>Chúc mừng sinh nhật</h2>

        <p class="birthday-message">
            <b>Kính chúc Quý khách</b><br>
            <strong><?= htmlspecialchars($user['hoten']) ?></strong>
            một ngày sinh nhật trọn vẹn,<br>
            nhiều sức khỏe và những khoảnh khắc đáng nhớ <br>
            bên những giá trị bền vững.<br>
            Chúng tôi tự hào khi được hiện diện <br>
            trong những khoảnh khắc có ý nghĩa của Quý khách.
        </p>

        <div class="birthday-signature">
            <div class="ceo-sign">Alexander Aurelius</div>
            <div class="ceo-title">Chief Executive Officer</div>
        </div>

    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const overlay = document.getElementById('birthdayOverlay');
    const closeBtn = document.getElementById('birthdayClose');

    if (!overlay || !closeBtn) return;

    setTimeout(() => {
    overlay.classList.add('show');

    fetch('/AureliusWatch/pages/user/log_birthday_popup.php', {
        method: 'POST'
    });

    }, 500);


    closeBtn.addEventListener('click', () => {
        overlay.remove();
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const btn = document.getElementById('userNoti');
    if (!btn) return;

    const box   = btn.querySelector('.notify-dropdown');
    const badge = document.getElementById('userNotiBadge');
    const list  = document.getElementById('userNotiList');

    if (!box || !badge || !list) return;

    function loadNoti() {
        fetch('/AureliusWatch/pages/noti/noti.php')
            .then(res => res.json())
            .then(data => {
                list.innerHTML = data.html;
                badge.innerText = data.unread;
                badge.style.display = data.unread > 0 ? 'flex' : 'none';
            });
    }

    loadNoti();
    setInterval(loadNoti, 20000);

    btn.addEventListener('click', e => {
        e.stopPropagation();
        box.classList.toggle('show');
    });

    document.addEventListener('click', () => {
        box.classList.remove('show');
    });

    box.addEventListener('click', e => {
        e.stopPropagation();

        const item = e.target.closest('.notify-item');
        if (!item) return;

        const id   = item.dataset.id;
        const link = item.dataset.link;

        fetch('/AureliusWatch/pages/noti/noti_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id)
        }).finally(() => {
            if (link && link !== '#' && link !== 'null') {
                window.location.href = link;
            }
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnSearch');
    const box = document.getElementById('headerSearch');

    if (!btn || !box) return;

    btn.addEventListener('click', e => {
        e.stopPropagation();
        box.style.display = box.style.display === 'block' ? 'none' : 'block';
        box.querySelector('input')?.focus();
    });

    document.addEventListener('click', () => {
        box.style.display = 'none';
    });

    box.addEventListener('click', e => e.stopPropagation());
});
</script>
