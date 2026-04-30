<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

$idwatch  = $_POST['idwatch'];
$quantity = (int)($_POST['quantity'] ?? 1);
$buy_now  = isset($_POST['buy_now']);

if (!$idwatch) {
    header("Location: /AureliusWatch/pages/product/list.php");
    exit;
}

/* USER / SESSION */

$user_id = $_SESSION['user']['id'] ?? null;

if (!isset($_SESSION['cart_session'])) {
    $_SESSION['cart_session'] = session_id();
}
$session_id = $_SESSION['cart_session'];

/* 1. TÌM CART ACTIVE */

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT idcart FROM carts 
        WHERE user_id = ? AND status = 'active' 
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("
        SELECT idcart FROM carts 
        WHERE session_id = ? AND status = 'active' 
        LIMIT 1
    ");
    $stmt->bind_param("s", $session_id);
}

$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_assoc();

/* 2. NẾU CHƯA CÓ CART → TẠO MỚI */

if (!$cart) {
    if ($user_id) {
        $stmt = $conn->prepare("
            INSERT INTO carts (user_id, status) 
            VALUES (?, 'active')
        ");
        $stmt->bind_param("i", $user_id);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO carts (session_id, status) 
            VALUES (?, 'active')
        ");
        $stmt->bind_param("s", $session_id);
    }

    $stmt->execute();
    $idcart = $conn->insert_id;
} else {
    $idcart = $cart['idcart'];
}

/* 3. LẤY GIÁ SẢN PHẨM */

$stmt = $conn->prepare("
    SELECT price FROM watches WHERE idwatch = ? LIMIT 1
");
$stmt->bind_param("s", $idwatch);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại");
}

$price = $product['price'];

/* 4. THÊM / CỘNG SỐ LƯỢNG */

$stmt = $conn->prepare("
    INSERT INTO cart_items (idcart, idwatch, quantity, price)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
        quantity = quantity + VALUES(quantity)
");
$stmt->bind_param("isii", $idcart, $idwatch, $quantity, $price);
$stmt->execute();

/* 5. ĐIỀU HƯỚNG */

if ($buy_now) {
    header("Location: /AureliusWatch/pages/checkout/checkout.php?mode=buynow&idwatch=".$idwatch);
}
 else {
    header("Location: /AureliusWatch/pages/cart/cart.php");
}
exit;
