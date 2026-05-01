<?php
$host     = getenv('DB_HOST');
$user     = getenv('DB_USER');
$pass     = getenv('DB_PASS');
$dbname   = getenv('DB_NAME');
$port     = (int) getenv('DB_PORT');

$conn = mysqli_init();

// Bỏ qua xác thực certificate
mysqli_ssl_set($conn, null, null, null, null, null);

mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $dbname,
    $port,
    null,
    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
);

if (mysqli_connect_errno()) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
