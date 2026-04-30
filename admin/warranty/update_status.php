<?php
require_once $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/config/db.php";

$id     = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$note   = trim($_POST['admin_note'] ?? '');

if (!$id || !$status) {
    die("Dữ liệu không hợp lệ");
}

/* Lấy thông tin bảo hành */
$stmt = $conn->prepare("
    SELECT status, end_date
    FROM warranty
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$warranty = $stmt->get_result()->fetch_assoc();

if (!$warranty) {
    die("Bảo hành không tồn tại");
}

/* ❌ Không cho xử lý nếu đã hết hạn */
if ($warranty['status'] === 'expired') {
    header("Location: detail.php?id=".$id."&error=expired");
    exit;
}

/* Xác định trạng thái lưu DB */
$newStatus = $status;

/* Nếu completed mà còn hạn → quay về active */
if ($status === 'completed') {
    if (strtotime($warranty['end_date']) >= strtotime(date('Y-m-d'))) {
        $newStatus = 'active';
    } else {
        $newStatus = 'expired';
    }
}

/* Cập nhật warranty */
$update = $conn->prepare("
    UPDATE warranty
    SET status = ?, admin_note = ?
    WHERE id = ?
");
$update->bind_param("ssi", $newStatus, $note, $id);
$update->execute();

/* Ghi lịch sử (giữ nguyên status gốc để audit) */
$log = $conn->prepare("
    INSERT INTO warranty_history (warranty_id, status, note, created_at)
    VALUES (?, ?, ?, NOW())
");
$log->bind_param("iss", $id, $status, $note);
$log->execute();

header("Location: detail.php?id=".$id);
exit;
