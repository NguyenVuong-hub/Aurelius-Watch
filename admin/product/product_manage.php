<?php
session_start();
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

/* ===== FILTER & PAGINATION ===== */
$keyword = trim($_GET['keyword'] ?? '');
$brand   = $_GET['brand'] ?? '';
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset  = ($page - 1) * $perPage;

$where = "WHERE 1=1";

if ($keyword !== '') {
    $k = $conn->real_escape_string($keyword);
    $where .= " AND w.namewatch LIKE '%$k%'";
}
if ($brand !== '' && is_numeric($brand)) {
    $b = (int)$brand;
    $where .= " AND b.idbrand = $b";
}

/* ===== COUNT ===== */
$countSql = "
SELECT COUNT(*) AS total
FROM watches w
JOIN brands b ON w.idbrand = b.idbrand
JOIN genders g ON w.idgender = g.idgender
LEFT JOIN case_colors cc ON w.case_color_id = cc.idcolor
$where
";
$totalResult = $conn->query($countSql);
$totalItems  = $totalResult->fetch_assoc()['total'];
$totalPages  = ceil($totalItems / $perPage);

/* ===== PRODUCTS ===== */
$sql = "
SELECT 
    w.idwatch,
    w.namewatch,
    b.namebrand,
    g.namegender,
    mc.namematerial AS case_material,
    ms.namematerial AS strap_material,
    mg.namematerial AS glass_material,
    cc.namecolor    AS case_color,
    w.price,
    w.quantity,
    w.country,
    w.loaimay,
    w.kichcomatso,
    w.doday,
    w.image
FROM watches w
JOIN brands b ON w.idbrand = b.idbrand
JOIN genders g ON w.idgender = g.idgender
LEFT JOIN materials mc ON w.case_material_id = mc.idmaterial
LEFT JOIN materials ms ON w.strap_material_id = ms.idmaterial
LEFT JOIN materials mg ON w.glass_material_id = mg.idmaterial
LEFT JOIN case_colors cc ON w.case_color_id = cc.idcolor
$where
ORDER BY w.idwatch DESC
LIMIT $offset, $perPage
";
$products = $conn->query($sql);

/* ===== BRAND LIST ===== */
$brands = $conn->query("SELECT idbrand, namebrand FROM brands ORDER BY namebrand ASC");
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <div class="page-header">
        <h1>Quản lý sản phẩm</h1>
        <a href="product_add.php" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
        </a>
    </div>

    <!-- FILTER -->
    <div class="filter-box">
        <form method="get" class="search-filter-form">
            <input type="text"
                   name="keyword"
                   placeholder="Tìm theo tên sản phẩm..."
                   value="<?= htmlspecialchars($keyword) ?>"
                   style="flex: 1; min-width: 280px;">

            <select name="brand">
                <option value="">-- Tất cả hãng --</option>
                <?php while ($b = $brands->fetch_assoc()): ?>
                    <option value="<?= $b['idbrand'] ?>" <?= ($brand == $b['idbrand']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['namebrand']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Lọc
            </button>

            <?php if ($keyword !== '' || $brand !== ''): ?>
                <a href="product_manage.php" class="btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper-product">
        <table class="admin-table-product">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Hãng</th>
                    <th>Giới tính</th>
                    <th>Vỏ</th>
                    <th>Dây</th>
                    <th>Kính</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Xuất xứ</th>
                    <th>Loại máy</th>
                    <th>Màu vỏ</th>
                    <th>Size mặt</th>
                    <th>Độ dày</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['idwatch'] ?></td>
                        <td>
                            <img src="/AureliusWatch/<?= htmlspecialchars($row['image'] ?: 'uploads/no-image.png') ?>"
                                 class="thumb"
                                 alt="<?= htmlspecialchars($row['namewatch']) ?>">
                        </td>
                        <td><?= htmlspecialchars($row['namewatch']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['namebrand']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['namegender']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['case_material'] ?? '—') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['strap_material'] ?? '—') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['glass_material'] ?? '—') ?></td>
                        <td class="text-right">
                            <strong><?= number_format($row['price']) ?> VNĐ</strong>
                        </td>
                        <td class="text-center <?= $row['quantity'] <= 5 ? 'text-danger' : '' ?>">
                            <?= $row['quantity'] ?>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($row['country'] ?: '—') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['loaimay'] ?: '—') ?></td>
                        <td class="text-center">
                            <?= htmlspecialchars($row['case_color'] ?? '—') ?>
                        </td>
                        <td class="text-center"><?= $row['kichcomatso'] ? $row['kichcomatso'] . ' mm' : '—' ?></td>
                        <td class="text-center"><?= $row['doday'] ? $row['doday'] . ' mm' : '—' ?></td>
                        <td class="action" style="white-space: nowrap;">
                            <a href="product_edit.php?id=<?= $row['idwatch'] ?>"
                               class="btn-edit"
                               title="Chỉnh sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="product_delete.php?id=<?= $row['idwatch'] ?>"
                               onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')"
                               class="btn-delete"
                               title="Xóa">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="16" class="text-center py-5">
                        <p style="color:#888; font-style:italic; font-size:16px;">
                            <?= $keyword || $brand
                                ? 'Không tìm thấy sản phẩm nào phù hợp.'
                                : 'Chưa có sản phẩm nào trong hệ thống.' ?>
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
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                        <i class="fa-solid fa-chevron-left"></i> Trước
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);
                for ($p = $start; $p <= $end; $p++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"
                       class="<?= $p == $page ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                        Sau <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
