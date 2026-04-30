<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";
$orderId = $_GET['order_id'] ?? null;
if (!$orderId) die("Invalid order");

$stmt = $conn->prepare("
    SELECT total_amount 
    FROM orders 
    WHERE idorder = ?
    LIMIT 1
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found");
}

$order = $result->fetch_assoc();
$totalAmount = $order['total_amount'];

$stmtItems = $conn->prepare("
    SELECT 
        oi.watch_id,
        oi.quantity,
        oi.price,
        w.namewatch,
        w.image
    FROM order_items oi
    JOIN watches w ON oi.watch_id = w.idwatch
    WHERE oi.order_id = ?
");
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();

?>
<link rel="stylesheet" href="/AureliusWatch/assets/css/payment.css">

<div class="payment-page">

    <!-- LEFT: PAYMENT FORM -->
    <div class="payment-left">
        <h2>Thanh toán Visa</h2>

        <form action="/AureliusWatch/pages/checkout/process.php" method="post">
            <input type="hidden" name="order_id" value="<?= $orderId ?>">
            <input type="hidden" name="payment_method" value="visa">
            <input type="hidden" name="payment_status" value="paid">

            <div class="input-group">
                <label>Số thẻ</label>
                <input type="text" placeholder="1234 5678 9012 3456" required>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Ngày hết hạn</label>
                    <input type="text" placeholder="MM / YY" required>
                </div>
                <div class="input-group">
                    <label>Mã CVV</label>
                    <input type="text" placeholder="CVV" required>
                </div>
            </div>

            <h3>Thông tin</h3>

            <div class="input-group">
                <label>Họ và tên</label>
                <input type="text" placeholder="Họ và tên" required>
            </div>

            <div class="input-group">
                <label>Quốc tịch</label>
                <select required>
                    <option>Việt Nam</option>
                    <option>English</option>
                </select>
            </div>

            <div class="input-group">
                <label>Địa chỉ</label>
                <input type="text" placeholder="Street address" required>
            </div>

            <button type="submit" class="pay-btn">
                XÁC NHẬN THANH TOÁN
            </button>
        </form>
    </div>

    <!-- RIGHT: ORDER SUMMARY -->
<div class="payment-right">
    <h2>Đơn hàng</h2>

    <div class="summary-box">

        <div class="summary-row order-id">
            <span>Mã đơn #<?= $orderId ?></span>
        </div>

        <!-- PRODUCT LIST -->
<div class="product-list">
    <?php while ($item = $itemsResult->fetch_assoc()): ?>
        <div class="product-item">

            <div class="product-info">
                <span class="product-name">
                    <?= htmlspecialchars($item['namewatch']) ?>
                </span>
                <span class="product-qty">
                    Số lượng: <?= $item['quantity'] ?>
                </span>
            </div>

            <div class="product-price">
                <?= number_format($item['price'], 0, ',', '.') ?> ₫
            </div>

        </div>
    <?php endwhile; ?>
</div>

        <!-- TOTAL -->
        <div class="summary-total">
            <span>Tổng cộng</span>
            <span><?= number_format($totalAmount, 0, ',', '.') ?> ₫</span>
        </div>

    </div>
</div>

</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>