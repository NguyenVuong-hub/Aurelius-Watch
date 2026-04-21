<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

/* ================= AUTH ================= */
$userId = $_SESSION['user']['id'] ?? 0;
if (!$userId) {
    header("Location: /AureliusWatch/pages/login.php");
    exit;
}

/* ================= GET ORDER ================= */
$idorder = (int)($_GET['id'] ?? 0);
if (!$idorder) {
    header("Location: /AureliusWatch/pages/order/list.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE idorder=? AND iduser=?
    LIMIT 1
");
$stmt->bind_param("ii", $idorder, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<p style='text-align:center'>Đơn hàng không tồn tại</p>";
    include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php";
    exit;
}

/* ================= ORDER ITEMS ================= */
$stmt = $conn->prepare("
    SELECT 
        oi.id AS idorder_item,
        oi.watch_id,
        oi.price,
        oi.quantity,
        w.namewatch,
        w.image
    FROM order_items oi
    JOIN watches w ON oi.watch_id = w.idwatch
    WHERE oi.order_id=?
");
$stmt->bind_param("i", $idorder);
$stmt->execute();
$items = $stmt->get_result();

/* ================= MAP URL (CHỈ DÙNG KHI ĐANG GIAO) ================= */
$mapUrl = '';
if ($order['status'] === 'Đang giao' && !empty($order['guest_address'])) {
    $shopAddress = "Aurelius Watch Ho Chi Minh City";
    $customerAddress = $order['guest_address'];

    $mapUrl = "https://www.google.com/maps/dir/"
        . urlencode($shopAddress) . "/"
        . urlencode($customerAddress);
}
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/order.css">

<body class="page-my-orders">
<div class="my-orders">

    <h1>CHI TIẾT ĐƠN HÀNG #<?= $idorder ?></h1>

    <div class="order-card">
        <div class="order-left">
            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
            <p class="order-status">
                Trạng thái: <?= htmlspecialchars($order['status']) ?>
            </p>

            <?php if ($order['status'] === 'Đang giao' && $mapUrl): ?>
                <a
                    href="<?= $mapUrl ?>"
                    target="_blank"
                    style="
                        display:inline-block;
                        margin-top:10px;
                        padding:8px 14px;
                        background:#c9a44c;
                        color:#fff;
                        border-radius:6px;
                        font-size:14px;
                        text-decoration:none;
                    "
                >
                Xem bản đồ giao hàng
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php while ($item = $items->fetch_assoc()): ?>

        <?php
        // check đã đánh giá chưa
        $stmtCheck = $conn->prepare("
            SELECT 1
            FROM product_reviews
            WHERE iduser=? AND idorder=? AND idorder_item=?
            LIMIT 1
        ");
        $stmtCheck->bind_param("iii", $userId, $idorder, $item['idorder_item']);
        $stmtCheck->execute();
        $reviewed = $stmtCheck->get_result()->num_rows > 0;

        $image = $item['image']
            ? "/AureliusWatch/" . $item['image']
            : "/AureliusWatch/uploads/no-image.png";
        ?>

        <div class="order-card">

            <div class="order-left" style="display:flex;gap:20px;align-items:center">

                <img src="<?= $image ?>" width="90" style="border-radius:6px">

                <div>
                    <p><strong><?= htmlspecialchars($item['namewatch']) ?></strong></p>
                    <p>Giá: <?= number_format($item['price'],0,',','.') ?> ₫</p>
                    <p>Số lượng: <?= $item['quantity'] ?></p>
                </div>

            </div>

            <div class="order-actions">
                <?php if ($order['status'] === 'Hoàn thành'): ?>
                    <?php if ($reviewed): ?>
                        <span style="color:#2e7d32;font-weight:600">Đã đánh giá</span>
                    <?php else: ?>
                        <a class="btn-review"
                           href="/AureliusWatch/pages/review/review.php
                           ?idorder=<?= $idorder ?>
                           &idwatch=<?= $item['watch_id'] ?>
                           &idorder_item=<?= $item['idorder_item'] ?>">
                           Đánh giá
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>

    <?php endwhile; ?>

    <div class="order-card">
        <div class="order-left">
            <p style="font-size:18px">
                <strong>Tổng tiền:</strong>
                <?= number_format($order['total_amount'],0,',','.') ?> ₫
            </p>
        </div>
    </div>

</div>
</body>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
