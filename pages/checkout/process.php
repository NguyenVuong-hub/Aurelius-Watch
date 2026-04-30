<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
require $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/pages/order/order.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid access');
}

try {

    /* =====================================================
       CASE 1: PAYMENT CONFIRM (ƯU TIÊN TRƯỚC)
    ===================================================== */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && !empty($_POST['order_id'])
        && !empty($_POST['payment_method'])
    ) {

        $orderId = (int)$_POST['order_id'];
        $method  = $_POST['payment_method'];

        // Lấy tổng tiền
        $stmt = $conn->prepare("
            SELECT total_amount
            FROM orders
            WHERE idorder = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            throw new Exception("Đơn hàng không tồn tại");
        }

        // Ghi payment
        $stmt = $conn->prepare("
            INSERT INTO payments (idorder, payment_date, amount, method)
            VALUES (?, CURDATE(), ?, ?)
        ");
        $stmt->bind_param("ids", $orderId, $order['total_amount'], $method);
        $stmt->execute();

        // Update order
        $stmt = $conn->prepare("
            UPDATE orders
            SET status = 'Đã thanh toán'
            WHERE idorder = ?
        ");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        header("Location: /AureliusWatch/pages/checkout/success.php?order_id=$orderId");
        exit; // 🔥 CỰC KỲ QUAN TRỌNG
    }

    /* =====================================================
       CASE 2: CHECKOUT → TẠO ĐƠN (CHẠY SAU)
    ===================================================== */
    $orderId = createOrder($conn, $_POST, $_SESSION);
    $_SESSION['last_order_id'] = $orderId;

    /* ==========================
       ADMIN NOTIFICATION: NEW ORDER
    ========================== */
    $notiStmt = $conn->prepare(" INSERT INTO admin_notifications (type, title, target_id) VALUES ('order', ?, ?) "); 
    $title = "Đơn hàng mới #" . $orderId; 
    $notiStmt->bind_param("si", $title, $orderId); 
    $notiStmt->execute();

    $paymentMethod = $_POST['payment_method'] ?? 'cod';

    if (in_array($paymentMethod, ['momo', 'vnpay', 'visa'])) {
        header("Location: /AureliusWatch/pages/payment/$paymentMethod.php?order_id=$orderId");
        exit;
    }

    header("Location: /AureliusWatch/pages/payment/cod.php?order_id=$orderId");
    exit;

} catch (Exception $e) {
    die("PROCESS ERROR: " . $e->getMessage());
}
