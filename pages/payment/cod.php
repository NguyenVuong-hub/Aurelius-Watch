<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/config/db.php";

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    die("Invalid order");
}

// COD → chưa thanh toán, chỉ xác nhận đơn
$stmt = $conn->prepare("
    UPDATE orders
    SET status = 'Đang xử lý'
    WHERE idorder = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();

// Chuyển thẳng sang success
header("Location: /AureliusWatch/pages/checkout/success.php?order_id=".$orderId);
exit;
