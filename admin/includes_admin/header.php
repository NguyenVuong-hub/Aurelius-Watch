<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

/* ======================
   ADMIN NOTIFICATIONS
====================== */

// Đếm số chưa đọc
$notiCount = 0;
$resCount = $conn->query("
    SELECT COUNT(*) AS total 
    FROM admin_notifications 
    WHERE is_read = 0
");
if ($resCount) {
    $notiCount = $resCount->fetch_assoc()['total'];
}

// Lấy danh sách thông báo
$notiList = [];
$resNoti = $conn->query("
    SELECT * 
    FROM admin_notifications 
    ORDER BY created_at DESC 
    LIMIT 6
");
if ($resNoti) {
    while ($row = $resNoti->fetch_assoc()) {
        $notiList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin | Aurelius Watch</title>

    <link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <img src="/AureliusWatch/assets/images/logo.png" alt="Aurelius">
        </div>

        <nav class="admin-menu">
            <a href="/AureliusWatch/admin/dashboard.php">
                <i class="fa-solid fa-chart-line"></i> Tổng quan
            </a>

            <a href="/AureliusWatch/admin/product/product_manage.php">
                <i class="fa-solid fa-clock"></i> Sản phẩm
            </a>

            <a href="/AureliusWatch/admin/category/category.php">
                <i class="fa-solid fa-layer-group"></i> Danh mục
            </a>

            <a href="/AureliusWatch/admin/order/order_manage.php">
                <i class="fa-solid fa-box"></i> Đơn hàng
            </a>

            <a href="/AureliusWatch/admin/warranty/index.php">
                <i class="fa-solid fa-shield"></i> Bảo hành
            </a>

            <a href="/AureliusWatch/admin/user/user_manage.php">
                <i class="fa-solid fa-users"></i> Người dùng
            </a>

            <a href="/AureliusWatch/admin/report/report.php">
                <i class="fa-solid fa-chart-pie"></i> Báo cáo
            </a>

            <a href="/AureliusWatch/admin/contact/contact.php">
                <i class="fa-solid fa-envelope"></i> Liên hệ
            </a>

            <a href="/AureliusWatch/admin/review/review.php">
                <i class="fa-solid fa-pen-to-square"></i> Review
            </a>

            <a href="/AureliusWatch/admin/includes_admin/activity_log.php">
                <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử hoạt động
            </a>

            <a href="/AureliusWatch/admin/config_admin/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </nav>
    </aside>

    <!-- MAIN -->
    <div class="admin-main">

        <!-- TOPBAR -->
        <header class="admin-topbar">
            <div class="admin-title">
                AURELIUS WATCH — Administration
            </div>

            <div class="admin-notification" id="adminNoti">
    <i class="fa-solid fa-bell"></i>

    <span class="notify-badge" id="notiBadge" style="display:none">0</span>

    <div class="notify-dropdown">
        <div class="notify-header">Thông báo</div>

        <div id="notiList">
            <div class="notify-item">Loading...</div>
        </div>
    </div>
</div>
        </header>

        <main class="admin-content">

<script>
document.addEventListener("DOMContentLoaded", function () {

    const noti     = document.getElementById("adminNoti");
    const badge    = document.getElementById("notiBadge");
    const list     = document.getElementById("notiList");
    const dropdown = document.querySelector(".notify-dropdown");

    /* =========================
       TOGGLE DROPDOWN (CHỈ CLICK CHUÔNG)
    ========================= */
    noti.addEventListener("click", function (e) {

        // Nếu click vào item → KHÔNG toggle
        if (e.target.closest(".notify-item")) return;

        e.stopPropagation();
        this.classList.toggle("active");

        if (this.classList.contains("active")) {
            loadNotifications();

            // Mark all read
            fetch("/AureliusWatch/admin/noti/noti_read.php");

            badge.style.display = "none";
            badge.innerText = "0";
        }
    });

    /* =========================
       CLICK NGOÀI → ĐÓNG
    ========================= */
    document.addEventListener("click", function () {
        noti.classList.remove("active");
    });

    /* =========================
       CHẶN BUBBLE TRONG DROPDOWN
    ========================= */
    dropdown.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    /* =========================
       LOAD NOTIFICATIONS
    ========================= */
    function loadNotifications() {
        fetch("/AureliusWatch/admin/noti/notification.php")
            .then(res => res.json())
            .then(data => {

                if (data.count > 0) {
                    badge.innerText = data.count;
                    badge.style.display = "flex";
                }

                list.innerHTML = "";

                if (!data.items || data.items.length === 0) {
                    list.innerHTML = `<div class="notify-item">Không có thông báo</div>`;
                    return;
                }

                data.items.forEach(n => {

                    let icon = "fa-bell";
                    let link = "#";

                    if (n.type === "order") {
                        icon = "fa-cart-shopping";
                        link = "/AureliusWatch/admin/order/order_detail.php?id=" + n.target_id;
                    }

                    if (n.type === "review") {
                        icon = "fa-star";
                        link = "/AureliusWatch/admin/review/review.php";
                    }
                    if (n.type === "user") {
                        icon = "fa-user";
                        link = "/AureliusWatch/admin/user/user_manage.php";
                    }
                    if (n.type === "contact") {
                        icon = "fa-envelope";
                        link = "/AureliusWatch/admin/contact/contact.php";
                    }
                    const unread = n.is_read == 0 ? "unread" : "";

                    list.insertAdjacentHTML("beforeend", `
                        <div class="notify-item ${unread}"
                             data-id="${n.id}"
                             data-link="${link}">
                            <i class="fa-solid ${icon}"></i>
                            <div>
                                <strong>${n.title}</strong><br>
                                <small style="opacity:.6">${n.time ?? ""}</small>
                            </div>
                        </div>
                    `);
                });

                bindItemClick();
            });
    }

    /* =========================
       CLICK ITEM → MARK READ → REDIRECT (ÉP)
    ========================= */
    function bindItemClick() {
        document.querySelectorAll(".notify-item[data-id]").forEach(item => {

            item.addEventListener("click", function (e) {
                e.stopPropagation();

                const link = this.dataset.link;
                const id   = this.dataset.id;

                // Mark read
                fetch("/AureliusWatch/admin/noti/_noti_read.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + id
                });

                // 🔥 ÉP REDIRECT (SAU 1 TICK)
                setTimeout(() => {
                    window.location.href = link;
                }, 30);
            });
        });
    }

    loadNotifications();
});
</script>

