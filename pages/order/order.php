<?php
function createOrder(mysqli $conn, array $post, array $session)
{
    $conn->begin_transaction();

    try {

        $mode    = $post['mode'] ?? 'cart';
        $user_id = $session['user']['id'] ?? null;

        $items = [];
        $total = 0;

        /* =====================
           MODE: BUY NOW
        ===================== */
        if ($mode === 'buynow') {

            if (empty($post['idwatch'])) {
                throw new Exception("Thiếu sản phẩm mua ngay");
            }

            $stmt = $conn->prepare("
                SELECT idwatch, price
                FROM watches
                WHERE idwatch = ?
                LIMIT 1
            ");
            $stmt->bind_param("s", $post['idwatch']);
            $stmt->execute();
            $watch = $stmt->get_result()->fetch_assoc();

            if (!$watch) {
                throw new Exception("Sản phẩm không tồn tại");
            }

            $items[] = [
                'idwatch'  => $watch['idwatch'],
                'quantity' => 1,
                'price'    => $watch['price']
            ];

            $total = $watch['price'];
        }

        /* =====================
           MODE: CART (CHỈ ITEM ĐƯỢC CHỌN)
        ===================== */
        else {

            if (empty($post['cart_items'])) {
                throw new Exception("Không có sản phẩm được chọn");
            }

            $cartItems = $post['cart_items'];

            $placeholders = implode(',', array_fill(0, count($cartItems), '?'));
            $types = str_repeat('i', count($cartItems));

            $sql = "
                SELECT ci.iditem, ci.idwatch, ci.quantity, ci.price
                FROM cart_items ci
                WHERE ci.iditem IN ($placeholders)
                FOR UPDATE
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$cartItems);
            $stmt->execute();
            $rs = $stmt->get_result();

            if ($rs->num_rows === 0) {
                throw new Exception("Không có sản phẩm hợp lệ");
            }

            while ($row = $rs->fetch_assoc()) {
                $total += $row['price'] * $row['quantity'];
                $items[] = $row;
            }
        }

        /* =====================
           INSERT ORDER
        ===================== */
        $stmt = $conn->prepare("
            INSERT INTO orders
            (iduser, total_amount, status, guest_name, guest_phone, guest_email, guest_address, note, order_date)
            VALUES (?, ?, 'Đang xử lý', ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "idsssss",
            $user_id,
            $total,
            $post['fullname'],
            $post['phone'],
            $post['email'],
            $post['address'],
            $post['note']
        );

        $stmt->execute();
        $orderId = $conn->insert_id;

        /* =====================
           INSERT ORDER ITEMS
        ===================== */
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, watch_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($items as $i) {
            $stmt->bind_param(
                "isii",
                $orderId,
                $i['idwatch'],
                $i['quantity'],
                $i['price']
            );
            $stmt->execute();
        }

        /* =====================
           DELETE ONLY SELECTED CART ITEMS
        ===================== */
        if ($mode === 'cart') {
            $stmt = $conn->prepare("
                DELETE FROM cart_items
                WHERE iditem IN ($placeholders)
            ");
            $stmt->bind_param($types, ...$cartItems);
            $stmt->execute();
        }

        $conn->commit();
        return $orderId;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}