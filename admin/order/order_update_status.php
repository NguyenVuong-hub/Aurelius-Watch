<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../config_admin/admin_auth.php';

$idorder = (int)($_POST['idorder'] ?? 0);
$status  = trim($_POST['status'] ?? '');

$allowed = [
    'Đang xử lý',
    'Đã xác nhận',
    'Đang giao',
    'Hoàn thành',
    'Đã hủy'
];

if ($idorder <= 0 || !in_array($status, $allowed)) {
    die('Dữ liệu không hợp lệ');
}

/* =========================
   LẤY TRẠNG THÁI CŨ (FIX #1)
========================= */
$stmt = $conn->prepare("
    SELECT status 
    FROM orders 
    WHERE idorder = ?
");
$stmt->bind_param("i", $idorder);
$stmt->execute();
$oldStatus = $stmt->get_result()->fetch_assoc()['status'] ?? '';
$stmt->close();

/* =========================
   UPDATE ORDER STATUS
========================= */
$stmt = $conn->prepare("
    UPDATE orders 
    SET status = ?
    WHERE idorder = ?
");
$stmt->bind_param("si", $status, $idorder);
$stmt->execute();
$stmt->close();

/* =========================
   LẤY USER CỦA ĐƠN HÀNG
========================= */
$stmt = $conn->prepare("
    SELECT iduser
    FROM orders
    WHERE idorder = ?
    LIMIT 1
");
$stmt->bind_param("i", $idorder);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =========================
   USER NOTIFICATION
========================= */
if ($user && !empty($user['iduser'])) {

    $uid = (int)$user['iduser'];

    $title   = "Cập nhật đơn hàng #$idorder";
    $message = "Trạng thái đơn hàng của bạn đã được cập nhật thành: \"$status\"";
    $link    = "/AureliusWatch/pages/order/order_detail.php?id=$idorder";

    $stmt = $conn->prepare("
        INSERT INTO user_notifications
        (iduser, type, title, message, link)
        VALUES (?, 'order', ?, ?, ?)
    ");
    $stmt->bind_param("isss", $uid, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}

/* ==================================================
   TẠO BẢO HÀNH
   CHỈ KHI: KHÁC → HOÀN THÀNH (FIX #2)
================================================== */
if ($oldStatus !== 'Hoàn thành' && $status === 'Hoàn thành') {

    /* Lấy thông tin order + user */
    $stmt = $conn->prepare("
        SELECT
            o.idorder,
            o.order_date,
            o.guest_name,
            o.guest_phone,
            u.hoten AS user_name,
            u.phone AS user_phone
        FROM orders o
        LEFT JOIN user u ON u.iduser = o.iduser
        WHERE o.idorder = ?
    ");
    $stmt->bind_param("i", $idorder);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($order) {

        /* Lấy danh sách sản phẩm trong đơn */
        $items = $conn->prepare("
            SELECT watch_id
            FROM order_items
            WHERE order_id = ?
        ");
        $items->bind_param("i", $idorder);
        $items->execute();
        $itemResult = $items->get_result();
        $items->close();

        while ($item = $itemResult->fetch_assoc()) {

            $watchId = $item['watch_id'];

            /* CHECK WARRANTY (FIX #3 – KHÔNG ĐỔI DB) */
            $check = $conn->prepare("
                SELECT id 
                FROM warranty
                WHERE order_id = ? AND product_name = ?
                LIMIT 1
            ");
            $check->bind_param("is", $idorder, $watchId);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {

                $insert = $conn->prepare("
                    INSERT INTO warranty (
                        warranty_code,
                        order_id,
                        product_name,
                        user_name,
                        guest_name,
                        phone,
                        start_date,
                        end_date,
                        status,
                        created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW()
                    )
                ");

                $warrantyCode = "AW-$idorder-$watchId";

                $userName  = $order['user_name'];
                $guestName = $order['guest_name'];
                $phone     = $order['user_phone'] ?? $order['guest_phone'];

                $startDate = date('Y-m-d', strtotime($order['order_date']));
                $endDate   = date('Y-m-d', strtotime('+24 months', strtotime($startDate)));

                $insert->bind_param(
                    "sissssss",
                    $warrantyCode,
                    $idorder,
                    $watchId,
                    $userName,
                    $guestName,
                    $phone,
                    $startDate,
                    $endDate
                );

                $insert->execute();
                $insert->close();
            }

            $check->close();
        }
    }
}

/* =========================
   REDIRECT
========================= */
header("Location: order_detail.php?id=$idorder");
exit;
