<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    echo "<p style='text-align:center;margin:80px 0'>Vui lòng đăng nhập để xem đơn hàng.</p>";
    include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php";
    exit;
}

/* =========================
   GET ORDERS OF USER
========================= */
$sql = "
    SELECT 
        o.idorder,
        o.order_date,
        o.status,
        o.total_amount,
        COUNT(oi.order_id) AS total_items
    FROM orders o
    JOIN order_items oi ON o.idorder = oi.order_id
    WHERE o.iduser = ?
    GROUP BY o.idorder
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result();
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/order.css">
<body class="page-my-orders">
<div class="my-orders">
    <h1>ĐƠN HÀNG CỦA TÔI</h1>

    <?php if ($orders->num_rows === 0): ?>
        <p style="text-align:center;color:#aaa">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>

    <?php while ($o = $orders->fetch_assoc()): ?>
        <div class="order-card">

            <div class="order-left">
                <p><strong>Mã đơn:</strong> #<?= $o['idorder'] ?></p>
                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></p>
                <p><strong>Số sản phẩm:</strong> <?= $o['total_items'] ?></p>
                <p><strong>Tổng tiền:</strong> <?= number_format($o['total_amount']) ?> ₫</p>
                <p class="order-status">Trạng thái: <?= $o['status'] ?></p>
            </div>

            <div class="order-actions">
                <a href="/AureliusWatch/pages/order/order_detail.php?id=<?= $o['idorder'] ?>" class="btn-detail">
                    Chi tiết
                </a>
                <?php if ($o['status'] === 'Đang xử lý'): ?>
                    <form action="/AureliusWatch/pages/order/cancel_order.php" method="post" style="display:inline">
                        <input type="hidden" name="idorder" value="<?= $o['idorder'] ?>">
                        <button class="btn-cancel" onclick="return confirm('Bạn có chắc muốn hủy đơn này?')">
                            Hủy đơn
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    <?php endwhile; ?>
</div>
                </body>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
