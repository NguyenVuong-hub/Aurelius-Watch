<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$uid = (int)($_SESSION['user']['id'] ?? 0);
if ($uid <= 0) exit;

$stmt = $conn->prepare("
    UPDATE user_notifications
    SET is_read = 1
    WHERE iduser = ? AND is_read = 0
");
$stmt->bind_param("i", $uid);
$stmt->execute();
