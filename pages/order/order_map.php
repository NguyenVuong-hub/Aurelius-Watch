<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

$idorder = (int)($_GET['id'] ?? 0);
if ($idorder <= 0) die('Invalid order');

/* Lấy địa chỉ giao hàng */
$stmt = $conn->prepare("
    SELECT
        COALESCE(o.guest_address, '') AS guest_address
    FROM orders o
    WHERE o.idorder = ?
    LIMIT 1
");
$stmt->bind_param("i", $idorder);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) die('Order not found');

/* Địa chỉ shop (hard-code cho đồ án) */
$shopAddress = "12 District, Ho Chi Minh City";
$customerAddress = $order['guest_address'];

$mapUrl = "https://www.google.com/maps/dir/"
    . urlencode($shopAddress) . "/"
    . urlencode($customerAddress);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bản đồ giao hàng</title>
</head>
<body style="margin:0">
<iframe
    src="<?= $mapUrl ?>"
    width="100%"
    height="100vh"
    style="border:0"
    loading="lazy"
></iframe>
</body>
</html>
