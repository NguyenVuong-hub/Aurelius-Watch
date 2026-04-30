<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$data = [
    'count' => 0,
    'items' => []
];

$sql = "
    SELECT id, type, title, target_id, is_read, created_at
    FROM admin_notifications
    ORDER BY created_at DESC
    LIMIT 5
";

$res = $conn->query($sql);

if ($res) {
    while ($row = $res->fetch_assoc()) {

        if ((int)$row['is_read'] === 0) {
            $data['count']++;
        }

        $data['items'][] = [
            'id'        => (int)$row['id'],
            'type'      => $row['type'],
            'title'     => $row['title'],
            'target_id' => $row['target_id'],
            'is_read'   => (int)$row['is_read'],
            'time'      => date('d/m/Y H:i', strtotime($row['created_at']))
        ];
    }
}

echo json_encode($data);
exit;
