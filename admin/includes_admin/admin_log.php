<?php
if (!function_exists('admin_log')) {

function admin_log(
    string $action,
    string $target_type = null,
    int $target_id = null,
    string $description = null
) {
    global $conn;

    // Lấy admin_id từ session
    if (!isset($_SESSION['admin']['id'])) {
        return; // không có admin thì không log
    }

    $admin_id = (int) $_SESSION['admin']['id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $stmt = $conn->prepare("
        INSERT INTO admin_activity_log
        (admin_id, action, target_type, target_id, description, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ississ",
        $admin_id,
        $action,
        $target_type,
        $target_id,
        $description,
        $ip
    );

    $stmt->execute();
}
}
