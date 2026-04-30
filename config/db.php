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
// Lấy thông tin từ Biến môi trường của Railway
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$name = getenv('DB_NAME');
$port = getenv('DB_PORT');

// Khởi tạo đối tượng kết nối
$conn = mysqli_init();

// BẬT TÍNH NĂNG BẢO MẬT SSL (Bắt buộc đối với Aiven)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Thực hiện kết nối với cờ MYSQLI_CLIENT_SSL
mysqli_real_connect($conn, $host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);

// Kiểm tra lỗi nếu có
if (mysqli_connect_errno()) {
    die("Kết nối Aiven thất bại: " . mysqli_connect_error());
}
// Chèn code xử lý web của bạn ở bên dưới dòng này...
?>
<?php
$conn = new mysqli("localhost", "root", "", "watch");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

?>
