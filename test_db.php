<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Bắt đầu test...<br>";

// Dùng $_ENV hoặc getenv để chắc chắn lấy được biến
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
$user = $_ENV['DB_USER'] ?? getenv('DB_USER');
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');
$name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT');

if (!$host) {
    die("❌ Chưa nhận được biến môi trường (DB_HOST) từ Railway. Hãy kiểm tra lại tab Variables!");
}

$conn = mysqli_init();

// Lệnh chốt hạ: Nạp file chứng chỉ ca.pem vào để Aiven mở cửa
mysqli_ssl_set($conn, NULL, NULL, __DIR__ . '/ca.pem', NULL, NULL);

echo "Đang gọi kết nối Aiven...<br>";
$success = mysqli_real_connect($conn, $host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);

if ($success) {
    echo "🎉 KẾT NỐI OK! DỮ LIỆU ĐÃ THÔNG!";
} else {
    echo "❌ LỖI KẾT NỐI: " . mysqli_connect_error();
}
?>
