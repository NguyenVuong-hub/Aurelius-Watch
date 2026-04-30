<?php
require_once __DIR__ . '/config_admin/admin_auth.php';
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/includes_admin/header.php';

/* ======================
   THỐNG KÊ TỔNG QUAN
====================== */

// Tổng người dùng
$userRes = $conn->query("SELECT COUNT(*) AS total FROM user");
$userCount = $userRes ? $userRes->fetch_assoc()['total'] : 0;

// Tổng sản phẩm
$productRes = $conn->query("SELECT COUNT(*) AS total FROM watches");
$productCount = $productRes ? $productRes->fetch_assoc()['total'] : 0;

// Tổng đơn hàng
$orderRes = $conn->query("SELECT COUNT(*) AS total FROM orders");
$orderCount = $orderRes ? $orderRes->fetch_assoc()['total'] : 0;

// Tổng doanh thu
$revenueRes = $conn->query("SELECT SUM(total_amount) AS total FROM orders");
$revenue = $revenueRes ? $revenueRes->fetch_assoc()['total'] : 0;

/* ======================
   ĐƠN HÀNG GẦN ĐÂY
====================== */

$sqlOrders = "
SELECT 
    o.idorder,
    o.order_date,
    o.status,

    COALESCE(u.hoten, o.guest_name) AS customer_name,
    COALESCE(u.email, o.guest_email) AS email,
    COALESCE(u.phone, o.guest_phone) AS phone,

    GROUP_CONCAT(
        CONCAT(w.namewatch, ' (x', oi.quantity, ')')
        SEPARATOR ', '
    ) AS product_names,

    SUM(oi.price * oi.quantity) AS total_amount

FROM orders o
LEFT JOIN user u ON o.iduser = u.iduser
LEFT JOIN order_items oi ON o.idorder = oi.order_id
LEFT JOIN watches w ON oi.watch_id = w.idwatch

GROUP BY o.idorder
ORDER BY o.order_date DESC
LIMIT 5
";

$orders = $conn->query($sqlOrders);
if (!$orders) {
    die("SQL ERROR: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">
    <h1>Admin Dashboard</h1>

    <!-- THỐNG KÊ -->
    <div class="stats">

        <div class="card">
            <i class="fa-solid fa-users"></i>
            <div>
                <h3>Người dùng</h3>
                <p><?= number_format($userCount) ?></p>
            </div>
        </div>

        <div class="card">
            <i class="fa-solid fa-clock"></i>
            <div>
                <h3>Sản phẩm</h3>
                <p><?= number_format($productCount) ?></p>
            </div>
        </div>

        <div class="card">
            <i class="fa-solid fa-box"></i>
            <div>
                <h3>Đơn hàng</h3>
                <p><?= number_format($orderCount) ?></p>
            </div>
        </div>

        <div class="card revenue">
            <i class="fa-solid fa-coins"></i>
            <div>
                <h3>Doanh thu</h3>
                <p><?= number_format($revenue) ?> VNĐ</p>
            </div>
        </div>

    </div>

    <!-- ĐƠN HÀNG GẦN ĐÂY -->
    <h2>Đơn hàng gần đây</h2>

    <table class="order-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Tổng tiền</th>
            <th>Ngày</th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row['idorder'] ?></td>
                <td><?= $row['customer_name'] ?? 'Khách vãng lai' ?></td>
                <td><?= $row['product_names'] ?? '—' ?></td>
                <td><?= $row['email'] ?? '—' ?></td>
                <td><?= $row['phone'] ?? '—' ?></td>
                <td><?= number_format($row['total_amount']) ?> VNĐ</td>
                <td><?= date("d/m/Y", strtotime($row['order_date'])) ?></td>
                <td class="action">
                    <a href="/AureliusWatch/admin/order/order_detail.php?id=<?= $row['idorder'] ?>" class="btn-detail">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- XEM TẤT CẢ -->
    <div class="view-all-wrapper">
        <a href="/AureliusWatch/admin/order/order_manage.php" class="btn-view-all">
            <i class="fa-solid fa-list"></i>
            Xem tất cả đơn hàng
        </a>
    </div>

</div>

</body>
</html>

<?php include __DIR__ . "/includes_admin/footer.php"; ?>