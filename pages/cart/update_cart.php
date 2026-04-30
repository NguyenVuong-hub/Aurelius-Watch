<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
header('Content-Type: application/json');

$iditem = $_POST['iditem'] ?? null;
$qty    = $_POST['quantity'] ?? null;

if (!$iditem || $qty < 1) {
    echo json_encode(["success" => false]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE cart_items 
    SET quantity = ? 
    WHERE iditem = ?
");

if (!$stmt) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

$stmt->bind_param("ii", $qty, $iditem);
$stmt->execute();

echo json_encode(["success" => true]);
