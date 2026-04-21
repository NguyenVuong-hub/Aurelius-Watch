<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$uid = (int)($_SESSION['user']['id'] ?? 0);

if ($uid <= 0) {
    echo json_encode([
        'unread' => 0,
        'html' => '<div class="notify-item">Không có thông báo</div>'
    ]);
    exit;
}

/* ===== UNREAD COUNT ===== */
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM user_notifications 
    WHERE iduser = ? AND is_read = 0
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($unread);
$stmt->fetch();
$stmt->close();

/* ===== LIST NOTI ===== */
$stmt = $conn->prepare("
    SELECT id, title, message, link, is_read, created_at
    FROM user_notifications
    WHERE iduser = ?
    ORDER BY created_at DESC
    LIMIT 6
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();

$html = '';
if ($res->num_rows === 0) {
    $html = '<div class="notify-item">Không có thông báo</div>';
} else {
    while ($n = $res->fetch_assoc()) {
        $unreadClass = $n['is_read'] == 0 ? 'unread' : '';
        $html .= '
        <div class="notify-item '.$unreadClass.'"
             data-id="'.$n['id'].'"
             data-link="'.$n['link'].'">
            <strong>'.$n['title'].'</strong><br>
            <small style="opacity:.6">'.$n['message'].'</small>
        </div>';
    }
}

echo json_encode([
    'unread' => (int)$unread,
    'html'   => $html
]);
exit;
