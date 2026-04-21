<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

$iditem = $_GET['iditem'] ?? null;

if ($iditem) {
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE iditem = ?");
    $stmt->bind_param("i", $iditem);
    $stmt->execute();
}

header("Location: /AureliusWatch/pages/cart/cart.php");
exit;
