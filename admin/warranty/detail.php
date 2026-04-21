<?php
require_once $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/admin/includes_admin/header.php";

/* AUTO SET EXPIRED */
$conn->query("
    UPDATE warranty
    SET status = 'expired'
    WHERE end_date < CURDATE()
      AND status != 'expired'
");

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p style='padding:40px'>Không tìm thấy bảo hành.</p>";
    exit;
}

/* SELECT WARRANTY – FIX CUSTOMER NAME */
$stmt = $conn->prepare("
    SELECT
        *,
        COALESCE(user_name, guest_name, 'Khách vãng lai') AS customer_name
    FROM warranty
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$w = $stmt->get_result()->fetch_assoc();

if (!$w) {
    echo "<p style='padding:40px'>Bảo hành không tồn tại.</p>";
    exit;
}
?>

<link rel="stylesheet" href="/AureliusWatch/admin/warranty/warranty.css">

<div class="admin-container">

    <div class="detail-header">
        <h1>Bảo hành #<?= htmlspecialchars($w['warranty_code']) ?></h1>
        <span class="status <?= $w['status'] ?>">
            <?= strtoupper($w['status']) ?>
        </span>
    </div>

    <!-- INFO GRID -->
    <div class="detail-grid">

        <div class="detail-card">
            <h3>Thông tin sản phẩm</h3>
            <p><strong>Sản phẩm:</strong> <?= htmlspecialchars($w['product_name']) ?></p>
            <p><strong>Đơn hàng:</strong> #<?= $w['order_id'] ?></p>
            <p><strong>Thời hạn:</strong>
                <?= date('d/m/Y', strtotime($w['start_date'])) ?> →
                <?= date('d/m/Y', strtotime($w['end_date'])) ?>
            </p>
        </div>

        <div class="detail-card">
            <h3>Thông tin khách hàng</h3>
            <p><strong>Khách:</strong> <?= htmlspecialchars($w['customer_name']) ?></p>
            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($w['phone'] ?: '—') ?></p>
        </div>

    </div>

    <!-- UPDATE FORM -->
    <div class="detail-card full">
        <h3>Xử lý bảo hành</h3>

        <form action="update_status.php" method="post" class="warranty-form">

            <label>Trạng thái</label>
            <select name="status">
                <option value="active" <?= $w['status']=='active'?'selected':'' ?>>Active</option>
                <option value="processing" <?= $w['status']=='processing'?'selected':'' ?>>Processing</option>
                <option value="completed" <?= $w['status']=='completed'?'selected':'' ?>>Completed</option>
                <option value="expired" <?= $w['status']=='expired'?'selected':'' ?>>Expired</option>
            </select>

            <label>Ghi chú kỹ thuật (nội bộ)</label>
            <textarea name="admin_note" rows="5"
                placeholder="Ví dụ: Thay pin, test chống nước, đánh bóng vỏ..."><?= htmlspecialchars($w['admin_note']) ?></textarea>

            <input type="hidden" name="id" value="<?= $w['id'] ?>">

            <div class="form-actions">
                <button type="submit" class="btn-save">Lưu thay đổi</button>
                <a href="index.php" class="btn-back">Quay lại</a>
            </div>

        </form>
    </div>

    <!-- WARRANTY HISTORY -->
    <?php
    $his = $conn->prepare("
        SELECT *
        FROM warranty_history
        WHERE warranty_id = ?
        ORDER BY created_at DESC
    ");
    $his->bind_param("i", $w['id']);
    $his->execute();
    $history = $his->get_result();
    ?>

    <div class="detail-card full">
        <h3>Lịch sử bảo hành</h3>

        <?php if ($history->num_rows === 0): ?>
            <p style="color:#888;font-style:italic">Chưa có lịch sử xử lý.</p>
        <?php endif; ?>

        <?php while ($h = $history->fetch_assoc()): ?>
            <div class="timeline-item">
                <span class="timeline-date">
                    <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?>
                </span>
                <span class="timeline-status"><?= strtoupper($h['status']) ?></span>
                <p><?= nl2br(htmlspecialchars($h['note'])) ?></p>
            </div>
        <?php endwhile; ?>
    </div>

</div>
<div style="margin-top:30px; text-align:center;">
        <a href="index.php" class="btn-primary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
<?php include __DIR__ . '/../includes_admin/footer.php'; ?>