<?php
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT')  ?: '10460';
$dbname   = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

$conn = mysqli_init();

mysqli_ssl_set($conn, null, null, __DIR__ . '/../ca.pem', null, null);

mysqli_real_connect(
    $conn,
    $host,
    $username,
    $password,
    $dbname,
    (int)$port,
    null,
    MYSQLI_CLIENT_SSL
);

if (mysqli_connect_errno()) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>