<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
include __DIR__ . '/../includes_admin/header.php';

/* ====== CẤU HÌNH ====== */
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$keyword = trim($_GET['keyword'] ?? '');
$isSearching = ($keyword !== '');

$where = "WHERE 1=1";

if ($isSearching) {
    $safeKeyword = $conn->real_escape_string($keyword);
    $phoneKeyword = preg_replace('/\D/', '', $safeKeyword);
    $isPhoneSearch = ($phoneKeyword !== '' && strlen($phoneKeyword) >= 5);

    $where .= "
        AND (
            u.hoten LIKE '%$safeKeyword%'
            OR u.email LIKE '%$safeKeyword%'
            OR u.phone LIKE '%$safeKeyword%'
    ";

    if ($isPhoneSearch) {
        $where .= "
            OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(u.phone,' ',''),'-',''),'(',''),')',''),'+','')
               LIKE '%$phoneKeyword%'
        ";
    }

    $where .= ")";
}

/* ====== ĐẾM TỔNG ====== */
$totalQuery = "SELECT COUNT(*) AS total FROM user u $where";
$total = $conn->query($totalQuery)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

/* ====== LẤY DANH SÁCH USER ====== */
$sql = "
    SELECT 
        u.iduser,
        u.hoten,
        u.email,
        u.phone,
        u.ngaysinh,
        u.gioitinh,
        u.ngaydangky,
        u.status
    FROM user u
    $where
    ORDER BY u.iduser DESC
    LIMIT $limit OFFSET $offset
";

$users = $conn->query($sql);

function highlight($text, $kw) {
    if (!$kw) return htmlspecialchars($text);
    return preg_replace(
        "/(" . preg_quote($kw, '/') . ")/i",
        '<span class="hl">$1</span>',
        htmlspecialchars($text)
    );
}

function isBirthdayToday($date) {
    if (!$date || $date === '0000-00-00') return false;
    return date('m-d') === date('m-d', strtotime($date));
}
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <h1>Quản lý người dùng</h1>

    <!-- FILTER & SEARCH -->
    <div class="filter-box">
        <form method="get" class="search-filter-form" style="width: 100%;">
            <input type="text"
                   name="keyword"
                   placeholder="Tìm theo tên, email hoặc số điện thoại..."
                   value="<?= htmlspecialchars($keyword) ?>"
                   style="flex: 1; min-width: 300px;">

            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>

            <?php if ($isSearching): ?>
                <a href="user_manage.php" class="btn-reset">
                    <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($users && $users->num_rows > 0):
                $stt = $offset + 1;
                while ($row = $users->fetch_assoc()):
            ?>
                <tr <?= isBirthdayToday($row['ngaysinh']) ? 'class="birthday-row"' : '' ?>>
                    <td><?= $stt++ ?></td>
                    <td><strong>#<?= $row['iduser'] ?></strong></td>
                    <td>
                        <?= highlight($row['hoten'], $keyword) ?>
                        <?php if (isBirthdayToday($row['ngaysinh'])): ?>
                            <span class="birthday-badge">🎂</span>
                        <?php endif; ?>
                    </td>
                    <td><?= highlight($row['email'] ?: '—', $keyword) ?></td>
                    <td><?= highlight($row['phone'] ?: '—', $keyword) ?></td>
                    <td>
                        <?php if ($row['ngaysinh'] && $row['ngaysinh'] !== '0000-00-00'): ?>
                            <?= date('d/m/Y', strtotime($row['ngaysinh'])) ?>
                        <?php else: ?>
                            <span style="color:#888;">Chưa cập nhật</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['gioitinh'] == 1 ? 'Nam' : ($row['gioitinh'] == 0 ? 'Nữ' : '—') ?></td>
                    <td><?= date('d/m/Y', strtotime($row['ngaydangky'])) ?></td>
                    <td>
                        <?php if ($row['status'] == 1): ?>
                            <span class="status active">Hoạt động</span>
                        <?php else: ?>
                            <span class="status blocked">Bị khóa</span>
                        <?php endif; ?>
                    </td>
                    <td class="action" style="text-align:center;">
                        <?php if ($row['status'] == 1): ?>
                            <button class="btn-danger btn-action"
                                    data-id="<?= $row['iduser'] ?>"
                                    data-status="0"
                                    data-name="<?= htmlspecialchars($row['hoten']) ?>">
                                <i class="fa-solid fa-lock"></i> Khóa
                            </button>
                        <?php else: ?>
                            <button class="btn-primary btn-action"
                                    data-id="<?= $row['iduser'] ?>"
                                    data-status="1"
                                    data-name="<?= htmlspecialchars($row['hoten']) ?>">
                                <i class="fa-solid fa-lock-open"></i> Mở
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <p style="color:#888; font-style:italic;">
                            <?= $isSearching ? 'Không tìm thấy người dùng nào phù hợp.' : 'Chưa có người dùng nào.' ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION & FOOTER -->
     <?php $queryParams = $_GET; ?>

    <!-- PAGINATION & FOOTER -->
<div class="order-footer">
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
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
    <?php endif; ?>
</div>

<!-- MODAL XÁC NHẬN -->
<div id="confirmModal" class="modal">
    <div class="modal-content" style="max-width:420px;">
        <h3 id="modalTitle" style="color:var(--black);">Xác nhận hành động</h3>
        <p id="modalText" style="margin:15px 0; color:#444;"></p>
        <div class="form-actions">
            <button id="modalCancel" class="btn-reset">Hủy</button>
            <button id="modalConfirm" class="btn-danger">Xác nhận</button>
        </div>
    </div>
</div>

<script>
let selectedId = null;
let selectedStatus = null;

document.querySelectorAll('.btn-action').forEach(btn => {
    btn.addEventListener('click', () => {
        selectedId = btn.dataset.id;
        selectedStatus = btn.dataset.status;
        const name = btn.dataset.name;

        const actionText = selectedStatus == "0" ? 'khóa' : 'mở khóa';

        document.getElementById('modalText').innerText = 
            `Bạn có chắc chắn muốn ${actionText} tài khoản "${name}" không?`;

        document.getElementById('confirmModal').style.display = 'flex';
    });
});

document.getElementById('modalCancel').onclick = () => {
    document.getElementById('confirmModal').style.display = 'none';
};

document.getElementById('modalConfirm').onclick = () => {
    if (selectedId && selectedStatus !== null) {
        window.location.href = `user_toggle_status.php?id=${selectedId}&status=${selectedStatus}`;
    }
};
</script>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>