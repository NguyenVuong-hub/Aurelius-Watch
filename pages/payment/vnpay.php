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

// Lấy danh sách sản phẩm trong đơn
$stmtItems = $conn->prepare("
    SELECT 
        oi.quantity,
        oi.price,
        w.namewatch
    FROM order_items oi
    JOIN watches w ON oi.watch_id = w.idwatch
    WHERE oi.order_id = ?
");
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/payment.css">
<link rel="stylesheet" href="/AureliusWatch/assets/css/payment.css">

<div class="payment-page">

    <!-- LEFT: VNPAY QR -->
    <div class="payment-left">

        <h2>Quét mã QR VNPay</h2>

        <p class="payment-amount">
            Số tiền cần thanh toán
            <span><?= number_format($totalAmount, 0, ',', '.') ?> ₫</span>
        </p>

        <div class="qr-box">
            <img src="/AureliusWatch/assets/images/vnpay_qr.jpg" alt="VNPay QR">
        </div>

        <form action="/AureliusWatch/pages/checkout/process.php" method="post">
            <input type="hidden" name="order_id" value="<?= $orderId ?>">
            <input type="hidden" name="payment_method" value="vnpay">
            <input type="hidden" name="payment_status" value="paid">

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
                            <span class="amount">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                            </span>
                            <span class="currency">₫</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="summary-total">
                <span>Tổng cộng</span>
                <span class="price">
                    <?= number_format($totalAmount, 0, ',', '.') ?> ₫
                </span>
            </div>

        </div>
    </div>

</div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>