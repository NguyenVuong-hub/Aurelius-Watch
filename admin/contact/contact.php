<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

// ==========================
// Xử lý cập nhật trạng thái
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcontact'], $_POST['status'])) {
    $idcontact = (int)$_POST['idcontact'];
    $status = $_POST['status'];
    $allowedStatus = ['Mới', 'Đang xử lý', 'Đã giải quyết'];
    
    if (in_array($status, $allowedStatus)) {
        mysqli_query($conn, "UPDATE contact SET status='$status' WHERE idcontact=$idcontact");

        // Ghi log
        admin_log(
            'UPDATE_CONTACT_STATUS',
            'contact',
            $idcontact,
            "Admin cập nhật trạng thái contact ID=$idcontact thành $status",
            null
        );

        // Refresh trang để tránh resubmit
        $currentUrl = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        header("Location: $currentUrl");
        exit;
    }
}
include __DIR__ . '/../includes_admin/header.php';
// ==========================
// Phân trang, tìm kiếm, lọc
// ==========================
$limit = 10;
$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = [];
if ($search) {
    $where[] = "(c.hoten LIKE '%$search%' OR c.phone LIKE '%$search%' OR c.message LIKE '%$search%')";
}
if (in_array($filterStatus, ['Mới', 'Đang xử lý', 'Đã giải quyết'])) {
    $where[] = "c.status='$filterStatus'";
}
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Đếm tổng
$totalRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact c $whereSQL");
$totalRow = mysqli_fetch_assoc($totalRes);
$totalContact = $totalRow['total'];
$totalPages = ceil($totalContact / $limit);

// Lấy danh sách (ưu tiên Mới → Đang xử lý → Đã giải quyết)
$sql = "
    SELECT c.idcontact, c.iduser, c.hoten, c.phone, c.message, c.status, c.created_at
    FROM contact c
    $whereSQL
    ORDER BY 
        CASE WHEN c.status='Mới' THEN 1
             WHEN c.status='Đang xử lý' THEN 2
             ELSE 3
        END ASC,
        c.created_at DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $sql);
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="dashboard">
    <h1>Quản lý Liên hệ</h1>

    <!-- FILTER & SEARCH -->
    <div class="filter-box">
        <form method="GET" class="search-filter-form" style="width:100%;">
            <input type="text" 
                   name="search" 
                   placeholder="Tìm theo tên, SĐT hoặc nội dung..." 
                   value="<?= htmlspecialchars($search) ?>"
                   style="flex: 1; min-width: 280px;">

            <select name="status">
                <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="Mới" <?= $filterStatus === 'Mới' ? 'selected' : '' ?>>Mới</option>
                <option value="Đang xử lý" <?= $filterStatus === 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                <option value="Đã giải quyết" <?= $filterStatus === 'Đã giải quyết' ? 'selected' : '' ?>>Đã giải quyết</option>
            </select>

            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>

            <?php if (isset($_GET['search']) || (isset($_GET['status']) && $_GET['status'] !== 'all')): ?>
                <a href="contact.php" class="btn-reset">
                    <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>

        </form>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="contact-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['idcontact'] ?></td>
                        <td><?= htmlspecialchars($row['hoten']) ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?: '—') ?></td>
                        <td>
                            <div class="contact-message">
                                <?= nl2br(htmlspecialchars($row['message'])) ?>
                            </div>
                        </td>
                        <td>
                            <form method="POST" class="status-form" style="margin:0;">
                                <input type="hidden" name="idcontact" value="<?= $row['idcontact'] ?>">
                                <select name="status" onchange="this.form.submit()" style="padding:6px 10px;">
                                    <option value="Mới" <?= $row['status'] === 'Mới' ? 'selected' : '' ?>>Mới</option>
                                    <option value="Đang xử lý" <?= $row['status'] === 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="Đã giải quyết" <?= $row['status'] === 'Đã giải quyết' ? 'selected' : '' ?>>Đã giải quyết</option>
                                </select>
                            </form>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td class="action" style="text-align:center;">
                            <?php if (!empty($row['phone'])): 
                                $zaloPhone = preg_replace('/\D/', '', $row['phone']);
                            ?>
                                <a href="https://zalo.me/<?= $zaloPhone ?>" 
                                    target="_blank" 
                                    class="action-zalo"
                                    title="Nhắn tin Zalo">
                                    <img src="/AureliusWatch/assets/images/zalo.png" alt="Zalo">
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <p style="color:#888; font-style:italic;">
                            <?= $search || $filterStatus !== 'all' 
                                ? 'Không tìm thấy liên hệ nào phù hợp.' 
                                : 'Chưa có liên hệ nào.' ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filterStatus) ?>"
                       class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('input[name="search"]').forEach(input => {
    input.addEventListener('input', function () {
        if (this.value === '') {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('status');
            window.location.href = url.pathname;
        }
    });
});
</script>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>