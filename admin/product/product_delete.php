<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID sản phẩm không hợp lệ.');
}

$idwatch = $_GET['id'];

// Lấy thông tin sản phẩm trước khi xóa (để xóa file ảnh và log)
$stmt = $conn->prepare("SELECT namewatch, image FROM watches WHERE idwatch=?");
$stmt->bind_param("s", $idwatch);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die('Sản phẩm không tồn tại.');
}

$product = $res->fetch_assoc();
$imagePath = __DIR__ . '/../../' . ltrim($product['image'], '/');// đường dẫn thực tế

// Xóa sản phẩm
$stmt = $conn->prepare("DELETE FROM watches WHERE idwatch=?");
$stmt->bind_param("s", $idwatch);
$stmt->execute();

// Xóa file ảnh nếu tồn tại
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// Ghi nhật ký
admin_log(
    'DELETE_PRODUCT',
    'product',
    null,
    'Xóa sản phẩm: ' . $product['namewatch']
);

// Redirect về trang quản lý sản phẩm
header("Location: product_manage.php");
exit;
?>
