<?php
require_once __DIR__ . '/../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    http_response_code(403);
    exit;
}

$iduser = (int)$_SESSION['user']['id'];
$year   = date('Y');

$stmt = $conn->prepare("
    INSERT IGNORE INTO birthday_popup_log (iduser, shown_year)
    VALUES (?, ?)
");
$stmt->bind_param("ii", $iduser, $year);
$stmt->execute();
$stmt->close();
