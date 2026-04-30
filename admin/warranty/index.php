<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

/* ===============================
   AUTO SET EXPIRED
================================ */
$conn->query("
    UPDATE warranty
    SET status = 'expired'
    WHERE end_date < CURDATE()
      AND status != 'expired'
");

/* ===============================
   PAGINATION CONFIG
================================ */
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ===============================
   SEARCH & FILTER
================================ */
$where  = [];
$params = [];
$types  = "";

/* Keyword search */
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $where[] = "(
        w.warranty_code LIKE ?
        OR w.product_name LIKE ?
        OR wt.namewatch LIKE ?
        OR w.user_name LIKE ?
        OR w.guest_name LIKE ?
    )";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types   .= "sssss";
}

/* Status filter */
$status = $_GET['status'] ?? '';
if ($status !== '') {
    $where[] = "w.status = ?";
    $params[] = $status;
    $types   .= "s";
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

/* ===============================
   COUNT TOTAL
================================ */
$countSql = "
    SELECT COUNT(*) AS total
    FROM warranty w
    LEFT JOIN watches wt ON wt.idwatch = w.product_name
    $whereSql
";

$countStmt = $conn->prepare($countSql);
if ($params) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $limit));

/* ===============================
   GET DATA
================================ */
$dataSql = "
    SELECT
        w.*,
        COALESCE(wt.namewatch, w.product_name) AS product_display,
        COALESCE(w.user_name, w.guest_name, 'Khách vãng lai') AS customer_name
    FROM warranty w
    LEFT JOIN watches wt ON wt.idwatch = w.product_name
    $whereSql
    ORDER BY w.order_id DESC, w.created_at DESC
    LIMIT ? OFFSET ?
";

$dataParams = $params;
$dataTypes  = $types . "ii";
$dataParams[] = $limit;
$dataParams[] = $offset;

$dataStmt = $conn->prepare($dataSql);
$dataStmt->bind_param($dataTypes, ...$dataParams);
$dataStmt->execute();
$result = $dataStmt->get_result();
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/admin.css">

<div class="admin-container">
    <h1>Quản lý bảo hành</h1>

    <!-- SEARCH & FILTER -->
    <form method="get" class="warranty-filter">

        <input type="text"
               name="q"
               placeholder="Mã BH / Sản phẩm / Khách hàng"
               value="<?= htmlspecialchars($q) ?>">

        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="active" <?= $status=='active'?'selected':'' ?>>Active</option>
            <option value="processing" <?= $status=='processing'?'selected':'' ?>>Processing</option>
            <option value="completed" <?= $status=='completed'?'selected':'' ?>>Completed</option>
            <option value="expired" <?= $status=='expired'?'selected':'' ?>>Expired</option>
        </select>

        <button type="submit">Tìm kiếm</button>

        <?php if ($q !== '' || $status !== ''): ?>
            <a href="index.php" class="btn-reset">Xóa lọc</a>
        <?php endif; ?>
    </form>

    <!-- TABLE -->
    <table class="warranty-table">
        <thead>
            <tr>
                <th>Mã BH</th>
                <th>Sản phẩm</th>
                <th>Khách hàng</th>
                <th>Thời hạn</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="6" style="text-align:center;color:#888;padding:30px">
                    Không có dữ liệu phù hợp
                </td>
            </tr>
        <?php endif; ?>

        <?php while ($w = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($w['warranty_code']) ?></td>
                <td><?= htmlspecialchars($w['product_display']) ?></td>
                <td><?= htmlspecialchars($w['customer_name']) ?></td>
                <td>
                    <?= date('d/m/Y', strtotime($w['start_date'])) ?>
                    →
                    <?= date('d/m/Y', strtotime($w['end_date'])) ?>
                </td>
                <td>
                    <?php $st = strtolower(trim($w['status'])); ?>
                    <span class="status <?= $st ?>">
                        <?= ucfirst($st) ?>
                    </span>
                </td>
                <td>
                    <a href="detail.php?id=<?= $w['id'] ?>" class="btn-detail">
                        Xử lý
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<?php $query = $_GET; ?>

<div class="pagination-wrapper">
    <div class="pagination">

        <?php if ($page > 1): ?>
            <?php $query['page'] = $page - 1; ?>
            <a href="?<?= http_build_query($query) ?>">← Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $query['page'] = $i; ?>
            <a href="?<?= http_build_query($query) ?>"
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <?php $query['page'] = $page + 1; ?>
            <a href="?<?= http_build_query($query) ?>">Sau →</a>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
