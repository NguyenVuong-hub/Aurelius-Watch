<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

/* ===== CONFIG ===== */
$limit  = 20;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ===== FILTER ===== */
$action  = $_GET['action'] ?? '';
$adminId = $_GET['admin_id'] ?? '';

$where = "WHERE 1";

if ($action !== '') {
    $a = $conn->real_escape_string($action);
    $where .= " AND l.action = '$a'";
}

if ($adminId !== '') {
    $where .= " AND l.admin_id = " . (int)$adminId;
}

/* ===== COUNT ===== */
$totalSql = "
    SELECT COUNT(*) AS total
    FROM admin_activity_log l
    $where
";
$total = $conn->query($totalSql)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

/* ===== LOGS ===== */
$sql = "
    SELECT 
        l.*,
        a.username AS admin_name
    FROM admin_activity_log l
    LEFT JOIN admins a ON a.id = l.admin_id
    $where
    ORDER BY l.created_at DESC
    LIMIT $limit OFFSET $offset
";
$logs = $conn->query($sql);

/* ===== ADMIN LIST ===== */
$admins = $conn->query("SELECT id, username FROM admins");

/* ===== ACTION LIST ===== */
$actions = [
    'ADMIN_LOGIN'     => 'Admin đăng nhập',
    'LOCK_USER'       => 'Khóa user',
    'UNLOCK_USER'     => 'Mở khóa user',
    'CREATE_PRODUCT'  => 'Thêm sản phẩm',
    'UPDATE_PRODUCT'  => 'Cập nhật sản phẩm',
    'DELETE_PRODUCT'  => 'Xóa sản phẩm',
    'CREATE_CATEGORY' => 'Thêm danh mục',
    'UPDATE_CATEGORY' => 'Cập nhật danh mục',
    'DELETE_CATEGORY' => 'Xóa danh mục',
    'REPLY_FEEDBACK'  => 'Trả lời Feedback',
    'UPDATE_CONTACT_STATUS' => 'Admin cập nhật trạng thái contact'
];

/* ===== ACTION STYLE ===== */
function actionStyle($action) {
    return match ($action) {
        'LOCK_USER', 'DELETE_PRODUCT', 'DELETE_CATEGORY' => 'danger',
        'UNLOCK_USER', 'CREATE_PRODUCT', 'CREATE_CATEGORY' => 'success',
        'UPDATE_PRODUCT', 'UPDATE_CATEGORY' => 'warning',
        'ADMIN_LOGIN' => 'info',
        'REPLY_FEEDBACK' => 'info',
        default => 'default'
    };
}
?>

<div class="dashboard">
    <h1>Lịch sử hoạt động Admin</h1>

    <!-- FILTER -->
    <form method="get" class="filter-box">
        <select name="action">
            <option value="">-- Hành động --</option>
            <?php foreach ($actions as $key => $label): ?>
                <option value="<?= $key ?>" <?= $action == $key ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn-filter">
            <i class="fa fa-filter"></i> Lọc
        </button>

        <a href="activity_log.php" class="btn-reset">
            Xóa bộ lọc
        </a>
    </form>

    <!-- TABLE -->
    <table class="order-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Admin</th>
                <th>Hành động</th>
                <th>Đối tượng</th>
                <th>Mô tả</th>
                <th>IP</th>
                <th>Thời gian</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($logs && $logs->num_rows > 0):
            $stt = $offset + 1;
            while ($row = $logs->fetch_assoc()):
        ?>
            <tr>
                <td><?= $stt++ ?></td>
                <td><?= htmlspecialchars($row['admin_name'] ?? 'Admin') ?></td>
                <td>
                    <span class="status <?= actionStyle($row['action']) ?>">
                        <?= $actions[$row['action']] ?? $row['action'] ?>
                    </span>
                </td>
                <td>
                    <?= $row['target_type'] ?>
                    <?= $row['target_id'] ? '#'.$row['target_id'] : '' ?>
                </td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['ip_address'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td colspan="7" style="text-align:center">
                    Chưa có lịch sử hoạt động
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&action=<?= $action ?>&admin_id=<?= $adminId ?>"
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
