<?php
require_once __DIR__ . '/../../config/db.php';

$conn->query("
    UPDATE admin_notifications
    SET is_read = 1
    WHERE is_read = 0
");
