?>
<?php
// Lấy thông số từ biến môi trường trên Cloud, nếu chạy ở localhost thì dùng mặc định
$host = getenv('DB_HOST') ?: "mysql-1d2b6f33-vle00882-8940.h.aivencloud.com";
$user = getenv('DB_USER') ?: "avnadmin";
$pass = getenv('DB_PASS') ?: "";
$dbname = getenv('DB_NAME') ?: "watch";
$port = getenv('DB_PORT') ?: 10460;

// Kết nối đến MySQL có cấu hình Port (Rất quan trọng cho Cloud DB)
$conn = new mysqli($host, $user, $pass, $dbname, $port);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

<?php
$conn = new mysqli("localhost", "root", "", "watch");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

?>
