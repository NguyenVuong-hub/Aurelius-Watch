<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/admin/review/review_reply.php";

/* =========================
   DUYỆT / ẨN REVIEW
========================= */
if (isset($_GET['action'], $_GET['id'])) {
    $idreview = (int)$_GET['id'];
    $action   = $_GET['action'];

    /* ===== DUYỆT REVIEW ===== */
    if ($action === 'approve') {

        /* LẤY REVIEW */
        $r = $conn->prepare("
            SELECT iduser, idwatch, rating
            FROM product_reviews
            WHERE idreview = ?
            LIMIT 1
        ");
        $r->bind_param("i", $idreview);
        $r->execute();
        $review = $r->get_result()->fetch_assoc();
        $r->close();

        if ($review) {

            /* AUTO REPLY */
            $reply = getAdminReplyByRating((int)$review['rating']);

            $up = $conn->prepare("
                UPDATE product_reviews
                SET is_approved = 1
                WHERE idreview = ?
            ");
            $up->bind_param("i", $idreview);
            $up->execute();
            $up->close();

            $title   = "Aurelius Watch đã phản hồi đánh giá của bạn";
$message = "Đánh giá của bạn đã được Aurelius Watch phản hồi tự động.";

$link = "/AureliusWatch/pages/product/detail.php?id={$review['idwatch']}#review";

$n = $conn->prepare("
    INSERT INTO user_notifications
        (iduser, type, title, message, link)
    VALUES
        (?, 'review_reply', ?, ?, ?)
");
$n->bind_param(
    "isss",
    $review['iduser'],
    $title,
    $message,
    $link
);
$n->execute();
$n->close();

        }
    }

    /* ===== ẨN REVIEW ===== */
    if ($action === 'hide') {
        $stmt = $conn->prepare("
            UPDATE product_reviews
            SET is_approved = 0
            WHERE idreview = ?
        ");
        $stmt->bind_param("i", $idreview);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: review.php");
    exit;
}

include __DIR__ . '/../includes_admin/header.php';

/* =========================
   SEARCH / FILTER
========================= */
$search = $_GET['search'] ?? '';
$filterStatus = $_GET['status'] ?? 'all';

$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(u.hoten LIKE ? OR w.namewatch LIKE ? OR r.comment LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if ($filterStatus === 'approved') {
    $where[] = "r.is_approved = 1";
}
if ($filterStatus === 'pending') {
    $where[] = "r.is_approved = 0";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

/* =========================
   PAGINATION
========================= */
$limit = 10;
$page  = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$countSql = "
    SELECT COUNT(*) AS total
    FROM product_reviews r
    JOIN user u ON r.iduser = u.iduser
    JOIN watches w ON r.idwatch = w.idwatch
    $whereSQL
";
$countStmt = $conn->prepare($countSql);
if ($params) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

/* =========================
   FETCH REVIEWS
========================= */
$sql = "
    SELECT 
        r.idreview,
        r.rating,
        r.comment,
        r.images,
        r.is_approved,
        r.created_at,
        u.hoten AS user_name,
        w.namewatch
    FROM product_reviews r
    JOIN user u ON r.iduser = u.iduser
    JOIN watches w ON r.idwatch = w.idwatch
    $whereSQL
    ORDER BY r.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <h1>Quản lý đánh giá sản phẩm</h1>

    <!-- SEARCH / FILTER -->
    <form method="GET" class="search-filter-form">
        <input type="text" name="search" placeholder="Tìm khách, sản phẩm, nội dung..."
               value="<?= htmlspecialchars($search) ?>">

        <select name="status">
            <option value="all">Tất cả</option>
            <option value="pending" <?= $filterStatus=='pending'?'selected':'' ?>>Chờ duyệt</option>
            <option value="approved" <?= $filterStatus=='approved'?'selected':'' ?>>Đã duyệt</option>
        </select>

        <button type="submit">Lọc</button>

        <?php if ($search || $filterStatus !== 'all'): ?>
        <a href="review.php" class="btn-reset">
            <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
        </a>
    <?php endif; ?>
    </form>

    <!-- TABLE -->
    <table class="feedback-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Sao</th>
                <th>Đánh giá</th>
                <th>Hình ảnh</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = $reviews->fetch_assoc()): ?>
            <tr>
                <td><?= $r['idreview'] ?></td>
                <td><?= htmlspecialchars($r['user_name']) ?></td>
                <td><?= htmlspecialchars($r['namewatch']) ?></td>
                <td style="color:#d4af37;">
                    <?= str_repeat('★', $r['rating']) ?>
                </td>
                <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                <td>
                    <?php if (!empty($r['images'])): ?>
                        <div class="admin-review-images">
                            <?php foreach (explode(',', $r['images']) as $img): ?>
                                <img src="/AureliusWatch/<?= htmlspecialchars($img) ?>"
                                     onclick="openReviewImage(this.src)">
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span style="color:#666;">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $r['is_approved']
                        ? '<span class="badge badge-done">Đã duyệt</span>'
                        : '<span class="badge badge-new">Chờ duyệt</span>' ?>
                </td>
                <td>
                    <?php if (!$r['is_approved']): ?>
                        <a href="?action=approve&id=<?= $r['idreview'] ?>" class="btn-admin btn-approve">
                            Duyệt
                        </a>
                    <?php else: ?>
                        <a href="?action=hide&id=<?= $r['idreview'] ?>" class="btn-admin btn-hide">
                            Ẩn
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- PAGINATION -->
     <?php $queryParams = $_GET; ?>

    <!-- PAGINATION -->
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php $queryParams['page'] = $i; ?>
        <a href="?<?= http_build_query($queryParams) ?>"
           class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

</div>

<script>
function openReviewImage(src) {
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.inset = 0;
    overlay.style.background = 'rgba(0,0,0,0.85)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = 9999;

    const img = document.createElement('img');
    img.src = src;
    img.style.maxWidth = '90%';
    img.style.maxHeight = '90%';
    img.style.borderRadius = '12px';

    overlay.appendChild(img);
    overlay.onclick = () => overlay.remove();
    document.body.appendChild(overlay);
}
</script>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
