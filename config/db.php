<?php
$host   = getenv('DB_HOST');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port   = (int) getenv('DB_PORT');

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    error_log("DB Error: " . $conn->connect_error);
    $conn = null;
}

if ($conn) {
    mysqli_set_charset($conn, "utf8mb4");
}
?>
