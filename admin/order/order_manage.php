<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

$_SESSION['order_manage_url'] = $_SERVER['REQUEST_URI'];

/* ====== CONFIG ====== */
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$keyword = trim($_GET['keyword'] ?? '');
$isSearching = $keyword !== '';

/* ====== SEARCH CONDITION ====== */
/* ====== SEARCH CONDITION ====== */
$where = "";
if ($isSearching) {
    $safe = $conn->real_escape_string($keyword);

    $where = "
        WHERE 
            u.hoten LIKE '%$safe%'
            OR u.phone LIKE '%$safe%'
            OR u.email LIKE '%$safe%'
    ";
}

/* ====== COUNT TOTAL ORDERS ====== */
$totalQuery = "
    SELECT COUNT(DISTINCT o.idorder) AS total
    FROM orders o
    LEFT JOIN user u ON o.iduser = u.iduser
    LEFT JOIN order_items oi ON o.idorder = oi.order_id
    $where
";
$total = $conn->query($totalQuery)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

/* ====== GET ORDERS ====== */
$sql = "
    SELECT 
        o.idorder,
        o.order_date,
        o.status,
        COALESCE(u.hoten, o.guest_name, 'Khách vãng lai') AS customer_name,
        COALESCE(u.phone, o.guest_phone) AS phone,
        COALESCE(u.email, o.guest_email) AS email,
        SUM(oi.quantity) AS total_items,
        GROUP_CONCAT(
            CONCAT(w.namewatch, ' (x', oi.quantity, ')')
            SEPARATOR ', '
        ) AS products,
        SUM(oi.price * oi.quantity) AS total_amount
    FROM orders o
    LEFT JOIN user u ON o.iduser = u.iduser
    LEFT JOIN order_items oi ON o.idorder = oi.order_id
    LEFT JOIN watches w ON oi.watch_id = w.idwatch
    $where
    GROUP BY o.idorder
    ORDER BY o.order_date DESC
    LIMIT $limit OFFSET $offset
";
$orders = $conn->query($sql);
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <h1>Quản lý đơn hàng</h1>

    <!-- SEARCH & FILTER -->
    <div class="filter-box order-search">
        <form method="get" style="width: 100%; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <input type="text"
                   name="keyword"
                   placeholder="Tìm theo tên khách, SĐT hoặc email..."
                   value="<?= htmlspecialchars($keyword) ?>"
                   style="flex: 1; min-width: 280px;">

            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>

            <?php if ($isSearching): ?>
                <a href="order_manage.php" class="btn-reset">
                    <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="order-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Sản phẩm</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($orders && $orders->num_rows > 0):
                $stt = $offset + 1;
                while ($row = $orders->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><strong>#<?= $row['idorder'] ?></strong></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?: '—') ?></td>
                    <td title="<?= htmlspecialchars($row['products']) ?>">
                        <?= (int)$row['total_items'] ?> sản phẩm
                    </td>
                    <td class="text-right">
                        <strong><?= number_format($row['total_amount']) ?> VNĐ</strong>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
                    <td class="action">
                        <a href="order_detail.php?id=<?= $row['idorder'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                           class="btn-detail"
                           title="Xem chi tiết">
                             Chi tiết
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <p style="color: #888; font-style: italic;">Chưa có đơn hàng nào<?= $isSearching ? ' phù hợp với từ khóa' : '' ?>.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <?php $queryParams = $_GET; ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination">
                <?php if ($page > 1): ?>
    <?php $queryParams['page'] = $page - 1; ?>
    <a href="?<?= http_build_query($queryParams) ?>">
        <i class="fa-solid fa-chevron-left"></i> Trước
    </a>
<?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <?php $queryParams['page'] = $i; ?>
    <a href="?<?= http_build_query($queryParams) ?>"
       class="<?= $i == $page ? 'active' : '' ?>">
        <?= $i ?>
    </a>
<?php endfor; ?>

                <?php if ($page < $totalPages): ?>
    <?php $queryParams['page'] = $page + 1; ?>
    <a href="?<?= http_build_query($queryParams) ?>">
        Sau <i class="fa-solid fa-chevron-right"></i>
    </a>
<?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>