<?php
session_start();
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

$idorder = intval($_GET['id'] ?? 0);
if ($idorder <= 0) die('Đơn hàng không hợp lệ');

/* ====== ORDER INFO ====== */
$orderQuery = "
    SELECT 
        o.*,
        COALESCE(u.hoten, o.guest_name, 'Khách vãng lai') AS customer_name,
        COALESCE(u.phone, o.guest_phone) AS phone,
        COALESCE(u.email, o.guest_email) AS email
    FROM orders o
    LEFT JOIN user u ON o.iduser = u.iduser
    WHERE o.idorder = $idorder
";
$order = $conn->query($orderQuery)->fetch_assoc();
if (!$order) die('Không tìm thấy đơn hàng');

/* ====== ORDER ITEMS (THÊM watch_id) ====== */
$itemSql = "
    SELECT 
        oi.watch_id,
        w.namewatch,
        oi.quantity,
        oi.price,
        (oi.quantity * oi.price) AS subtotal
    FROM order_items oi
    JOIN watches w ON oi.watch_id = w.idwatch
    WHERE oi.order_id = $idorder
";
$items = $conn->query($itemSql);

/* ====== BACK URL ====== */
$backUrl = 'order_manage.php';
if (!empty($_GET['return'])) {
    $backUrl = urldecode($_GET['return']);
}
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <h1>Chi tiết đơn hàng #<?= $idorder ?></h1>

    <!-- CUSTOMER INFO CARD -->
    <div class="detail-card">
        <div class="detail-title">
            <i class="fa-solid fa-user-circle"></i> Thông tin khách hàng
        </div>

        <div class="customer-info">
            <p><strong>Họ tên:</strong> <span><?= htmlspecialchars($order['customer_name']) ?></span></p>
            <p><strong>Email:</strong> <span><?= htmlspecialchars($order['email'] ?: '—') ?></span></p>
            <p><strong>Số điện thoại:</strong> <span><?= htmlspecialchars($order['phone'] ?: '—') ?></span></p>
            <p><strong>Ngày đặt hàng:</strong> <span><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></span></p>
            <?php if (!empty($order['note'])): ?>
                <p><strong>Ghi chú:</strong> <span style="color:#d32f2f;"><?= htmlspecialchars($order['note']) ?></span></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ORDER INFO CARD -->
    <div class="detail-card" style="margin-top:20px;">
        <div class="detail-title">
            <i class="fa-solid fa-receipt"></i> Thông tin đơn hàng
        </div>

        <div class="customer-info">
            <p><strong>Họ và tên:</strong> <span><?= htmlspecialchars($order['guest_name'] ?? '—') ?></span></p>
            <p><strong>Email:</strong> <span><?= htmlspecialchars($order['guest_email'] ?? '—') ?></span></p>
            <p><strong>Số điện thoại:</strong> <span><?= htmlspecialchars($order['guest_phone'] ?? '—') ?></span></p>
            <p><strong>Địa chỉ:</strong> <span><?= htmlspecialchars($order['guest_address'] ?? '—') ?></span></p>

            <?php if (!empty($order['note'])): ?>
                <p>
                    <strong>Ghi chú khách hàng:</strong>
                    <span style="color:#c62828;">
                        <?= nl2br(htmlspecialchars($order['note'])) ?>
                    </span>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- UPDATE STATUS -->
    <div class="detail-card" style="margin-top:20px;">
        <div class="detail-title">
            <i class="fa-solid fa-gear"></i> Trạng thái đơn hàng
        </div>

        <form method="post" action="order_update_status.php" style="display:flex; gap:12px; align-items:center;">
            <input type="hidden" name="idorder" value="<?= $idorder ?>">

            <select name="status" required>
                <?php
                $statuses = [
                    'Đang xử lý',
                    'Đã xác nhận',
                    'Đang giao',
                    'Hoàn thành',
                    'Đã hủy'
                ];
                foreach ($statuses as $st):
                ?>
                    <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>>
                        <?= $st ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-save"></i> Cập nhật
            </button>
        </form>
    </div>

    <!-- EXPORT PDF BUTTON -->
    <div class="export-box" style="margin: 25px 0; text-align: right;">
        <a href="export_order.php?id=<?= $idorder ?>" 
           class="btn-export-pdf" 
           target="_blank">
            <i class="fa-solid fa-file-pdf"></i> Xuất hóa đơn PDF
        </a>
    </div>

    <!-- ORDER ITEMS TABLE -->
    <div class="table-wrapper">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                    <th>Bảo hành</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $grandTotal = 0;
            while ($row = $items->fetch_assoc()): 
                $subtotal = $row['subtotal'];
                $grandTotal += $subtotal;

                /* ====== TÌM WARRANTY ====== */
                $wStmt = $conn->prepare("
                    SELECT id
                    FROM warranty
                    WHERE order_id = ?
                      AND product_name = ?
                    LIMIT 1
                ");
                $wStmt->bind_param("is", $idorder, $row['watch_id']);
                $wStmt->execute();
                $warranty = $wStmt->get_result()->fetch_assoc();
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['namewatch']) ?></td>
                    <td class="text-center"><?= $row['quantity'] ?></td>
                    <td class="text-right"><?= number_format($row['price']) ?> VNĐ</td>
                    <td class="text-right"><strong><?= number_format($subtotal) ?> VNĐ</strong></td>
                    <td class="text-center">
                        <?php if ($warranty): ?>
                            <a href="/AureliusWatch/admin/warranty/detail.php?id=<?= $warranty['id'] ?>"
                               class="btn-detail"
                               style="white-space:nowrap">
                                Chi tiết
                            </a>
                        <?php else: ?>
                            <span style="color:#999;font-style:italic">Chưa có</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>

            <?php if ($items->num_rows === 0): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <p style="color:#888; font-style:italic;">Không có sản phẩm trong đơn hàng này.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right; font-weight:600;">Tổng cộng:</td>
                    <td style="font-weight:700; color:var(--gold); font-size:16px;">
                        <?= number_format($grandTotal ?: $order['total_amount']) ?> VNĐ
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- BACK BUTTON -->
    <div style="margin-top:30px; text-align:center;">
        <a href="order_manage.php" class="btn-primary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
