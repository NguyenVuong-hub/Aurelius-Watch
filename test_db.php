<?php
// Bật tính năng hiển thị lỗi để dễ bắt bệnh
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Đang thử kết nối tới Aiven...<br><br>";

// Lấy thông số từ biến môi trường của Railway
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$name = getenv('DB_NAME');
$port = getenv('DB_PORT');

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Thử kết nối
$success = mysqli_real_connect($conn, $host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);

if ($success) {
    echo "🎉 KẾT NỐI AIVEN THÀNH CÔNG RỰC RỠ!";
} else {
    echo "❌ LỖI KẾT NỐI: " . mysqli_connect_error();
}
?>
