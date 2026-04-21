<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    die("Không tìm thấy đơn hàng");
}

// LẤY ĐƠN HÀNG
$stmt = $conn->prepare("
    SELECT idorder, total_amount, status
    FROM orders
    WHERE idorder = ?
    LIMIT 1
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Đơn hàng không tồn tại");
}

// LẤY PAYMENT (NẾU CÓ)
$stmt = $conn->prepare("
    SELECT method, amount, payment_date
    FROM payments
    WHERE idorder = ?
    ORDER BY idpayment DESC
    LIMIT 1
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

// FALLBACK CHO COD
$method = $payment['method'] ?? 'cod';
$amount = $payment['amount'] ?? $order['total_amount'];
$date   = $payment['payment_date'] ?? date('Y-m-d');

// MAP TÊN PHƯƠNG THỨC
$methodLabel = [
    'momo'  => 'Ví MoMo',
    'vnpay' => 'VNPay',
    'visa'  => 'Thẻ Visa / MasterCard',
    'cod'   => 'Thanh toán khi nhận hàng'
][$method] ?? strtoupper($method);
?>

<div style="max-width:600px;margin:120px auto;text-align:center">
    <h2>ĐẶT HÀNG THÀNH CÔNG</h2>

    <p>Mã đơn hàng: <strong>#<?= htmlspecialchars($orderId) ?></strong></p>

    <p>Phương thức thanh toán:
        <strong><?= $methodLabel ?></strong>
    </p>

    <p>Số tiền:
        <strong><?= number_format($amount, 0, ',', '.') ?> ₫</strong>
    </p>

    <p>Ngày xác nhận:
        <?= date('d/m/Y', strtotime($date)) ?>
    </p>

    <p>Trạng thái đơn:
        <strong><?= htmlspecialchars($order['status']) ?></strong>
    </p>

    <a href="/AureliusWatch/pages/product/list.php"
       style="display:inline-block;margin-top:20px;padding:12px 26px;background:#111;color:#fff;text-decoration:none">
        Tiếp tục mua sắm
    </a>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
