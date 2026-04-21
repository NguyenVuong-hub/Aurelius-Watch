<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chống cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (
    empty($_SESSION['admin']) ||
    !is_array($_SESSION['admin']) ||
    empty($_SESSION['admin']['id'])
) {
    header("Location: /AureliusWatch/admin/config_admin/login.php");
    exit;
}

// Timeout
$timeout = 1800;
if (
    isset($_SESSION['admin_last_activity']) &&
    time() - $_SESSION['admin_last_activity'] > $timeout
) {
    session_destroy();
    header("Location: /AureliusWatch/admin/config_admin/login.php?timeout=1");
    exit;
}

$_SESSION['admin_last_activity'] = time();
