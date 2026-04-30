<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

$id = intval($_GET['id'] ?? 0);
$status = intval($_GET['status'] ?? -1);

if ($id <= 0 || !in_array($status, [0,1])) {
    die('Dữ liệu không hợp lệ');
}

/* KHÔNG CHO TỰ KHÓA ADMIN */
$result = $conn->query("SELECT role FROM user WHERE iduser = $id");
$user = $result->fetch_assoc();

if ($user['role'] === 'admin') {
    die('Không thể khóa tài khoản admin');
}

/* UPDATE USER */
$conn->query("UPDATE user SET status = $status WHERE iduser = $id");

/* GHI LOG — FIX CỨNG ADMIN_ID */
$action = $status == 0 ? 'LOCK_USER' : 'UNLOCK_USER';
$desc   = $status == 0
    ? 'Khóa tài khoản người dùng'
    : 'Mở khóa tài khoản người dùng';

$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

$conn->query("
    INSERT INTO admin_activity_log
    (admin_id, action, target_type, target_id, description, ip_address)
    VALUES
    (1, '$action', 'user', $id, '$desc', '$ip')
");

header("Location: user_manage.php");
exit;
?>