<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

/* =========================
   CHECK LOGIN (ĐÚNG CHUẨN)
========================= */
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    header("Location: /AureliusWatch/config/login.php");
    exit;
}

/* =========================
   VALIDATE INPUT
========================= */
$idorder = intval($_POST['idorder'] ?? 0);
if ($idorder <= 0) {
    die("Đơn hàng không hợp lệ");
}

/* =========================
   CANCEL ORDER (CHỈ ĐANG XỬ LÝ)
========================= */
$stmt = $conn->prepare("
    UPDATE orders
    SET status = 'Đã hủy'
    WHERE idorder = ?
      AND iduser = ?
      AND status = 'Đang xử lý'
");
$stmt->bind_param("ii", $idorder, $userId);
$stmt->execute();

/* =========================
   REDIRECT
========================= */
header("Location: my_order.php");
exit;
