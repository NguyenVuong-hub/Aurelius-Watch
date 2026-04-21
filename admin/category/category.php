<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

$tab = $_GET['tab'] ?? 'brands';
if (!in_array($tab, ['brands', 'materials', 'colors'])) {
    $tab = 'brands';
}

/* ================= ADD ================= */
if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        header("Location: category.php?tab=$tab");
        exit;
    }

    if ($tab === 'brands') {
        $stmt = $conn->prepare("INSERT INTO brands (namebrand) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        admin_log('CREATE_CATEGORY', 'brand', $stmt->insert_id, "Thêm hãng: $name");
    }

    if ($tab === 'materials') {
        $type = $_POST['type'] ?? '';
        if (in_array($type, ['case','strap','glass'])) {
            $stmt = $conn->prepare("
                INSERT INTO materials (namematerial, material_type)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ss", $name, $type);
            $stmt->execute();
            admin_log('CREATE_CATEGORY', 'material', $stmt->insert_id, "Thêm chất liệu ($type): $name");
        }
    }

    if ($tab === 'colors') {
        $stmt = $conn->prepare("INSERT INTO case_colors (namecolor) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        admin_log('CREATE_CATEGORY', 'case_color', $stmt->insert_id, "Thêm màu vỏ: $name");
    }

    header("Location: category.php?tab=$tab");
    exit;
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {
    $id   = (int)$_POST['id'];
    $name = trim($_POST['name'] ?? '');

    if ($id <= 0 || $name === '') {
        header("Location: category.php?tab=$tab");
        exit;
    }

    if ($tab === 'brands') {
        $stmt = $conn->prepare("UPDATE brands SET namebrand=? WHERE idbrand=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        admin_log('UPDATE_CATEGORY','brand',$id,"Cập nhật hãng: $name");
    }

    if ($tab === 'materials') {
        $stmt = $conn->prepare("UPDATE materials SET namematerial=? WHERE idmaterial=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        admin_log('UPDATE_CATEGORY','material',$id,"Cập nhật chất liệu: $name");
    }

    if ($tab === 'colors') {
        $stmt = $conn->prepare("UPDATE case_colors SET namecolor=? WHERE idcolor=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        admin_log('UPDATE_CATEGORY','case_color',$id,"Cập nhật màu vỏ: $name");
    }

    header("Location: category.php?tab=$tab");
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id > 0) {
        if ($tab === 'brands') {
            $conn->query("DELETE FROM brands WHERE idbrand=$id");
        }
        if ($tab === 'materials') {
            $conn->query("DELETE FROM materials WHERE idmaterial=$id");
        }
        if ($tab === 'colors') {
            $conn->query("DELETE FROM case_colors WHERE idcolor=$id");
        }
    }

    header("Location: category.php?tab=$tab");
    exit;
}

/* ================= FILTER ================= */
$keyword = trim($_GET['keyword'] ?? '');
$where = "1=1";
if ($keyword !== '') {
    $k = $conn->real_escape_string($keyword);
    if ($tab === 'brands')     $where = "namebrand LIKE '%$k%'";
    if ($tab === 'materials')  $where = "namematerial LIKE '%$k%'";
    if ($tab === 'colors')     $where = "namecolor LIKE '%$k%'";
}

/* ================= FETCH ================= */
if ($tab === 'brands') {
    $list = $conn->query("SELECT * FROM brands WHERE $where ORDER BY namebrand");
}
if ($tab === 'materials') {
    $list = $conn->query("
        SELECT * FROM materials
        WHERE $where
        ORDER BY material_type, namematerial
    ");
}
if ($tab === 'colors') {
    $list = $conn->query("SELECT * FROM case_colors WHERE $where ORDER BY namecolor");
}

include __DIR__ . '/../includes_admin/header.php';
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <h1>Quản lý danh mục</h1>

    <div class="tab-bar">
        <a href="?tab=brands" class="<?= $tab==='brands'?'active':'' ?>">Hãng</a>
        <a href="?tab=materials" class="<?= $tab==='materials'?'active':'' ?>">Chất liệu</a>
        <a href="?tab=colors" class="<?= $tab==='colors'?'active':'' ?>">Màu vỏ</a>
    </div>

    <div class="filter-box">
        <form method="get" class="search-filter-form">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            <input type="text" name="keyword" placeholder="Tìm theo tên..."
                   value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn-filter">
                <i class="fa-solid fa-magnifying-glass"></i> Lọc
            </button>
        </form>
    </div>

    <div class="form-box">
        <h3>Thêm mới</h3>
        <form method="post" style="display:flex; gap:12px;">
            <input type="text" name="name" placeholder="Tên danh mục" required>

            <?php if ($tab === 'materials'): ?>
                <select name="type" required>
                    <option value="">Loại</option>
                    <option value="case">Vỏ</option>
                    <option value="strap">Dây</option>
                    <option value="glass">Kính</option>
                </select>
            <?php endif; ?>

            <button name="add" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Thêm
            </button>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <?php if ($tab==='materials'): ?><th>Loại</th><?php endif; ?>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($list && $list->num_rows): ?>
                <?php while ($row = $list->fetch_assoc()): ?>

                <?php
                    if ($tab === 'brands') {
                        $id=$row['idbrand']; $name=$row['namebrand'];
                    } elseif ($tab === 'materials') {
                        $id=$row['idmaterial']; $name=$row['namematerial'];
                    } else {
                        $id=$row['idcolor']; $name=$row['namecolor'];
                    }
                ?>

                <tr>
                    <form method="post">
                        <td><?= $id ?></td>
                        <td><input type="text" name="name" value="<?= htmlspecialchars($name) ?>"></td>

                        <?php if ($tab==='materials'): ?>
                            <td><?= ucfirst($row['material_type']) ?></td>
                        <?php endif; ?>

                        <td class="action">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button name="update" class="btn-edit">
                                <i class="fa fa-save"></i> Lưu
                            </button>
                            <a href="?tab=<?= $tab ?>&delete=<?= $id ?>"
                               class="btn-delete"
                               onclick="return confirm('Xóa mục này?')">
                                <i class="fa fa-trash"></i> Xóa
                            </a>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;color:#888;">Chưa có dữ liệu</td></tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>
