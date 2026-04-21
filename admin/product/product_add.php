<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

/* =========================
   LOAD DATA
========================= */
$brands   = $conn->query("SELECT * FROM brands ORDER BY namebrand");
$genders  = $conn->query("SELECT * FROM genders ORDER BY namegender");
$caseM    = $conn->query("SELECT * FROM materials WHERE material_type='case'");
$strapM   = $conn->query("SELECT * FROM materials WHERE material_type='strap'");
$glassM   = $conn->query("SELECT * FROM materials WHERE material_type='glass'");
$caseColors = $conn->query("SELECT * FROM case_colors WHERE status = 1 ORDER BY namecolor");

/* =========================
   HELPER
========================= */
function slugify($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* =========================
   SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AUTO IDWATCH
    $lastRes = $conn->query("SELECT idwatch FROM watches ORDER BY idwatch DESC LIMIT 1");
    $lastId  = $lastRes->fetch_assoc()['idwatch'] ?? 'AW000';
    $idwatch = 'AW' . str_pad((int)substr($lastId, 2) + 1, 3, '0', STR_PAD_LEFT);

    // BRAND
    $brandRow = $conn->query("
        SELECT namebrand FROM brands WHERE idbrand = " . (int)$_POST['idbrand']
    )->fetch_assoc();

    if (!$brandRow) {
        die('Hãng không hợp lệ');
    }

    $brandSlug = slugify($brandRow['namebrand']);

    /* =========================
       UPLOAD IMAGE
    ========================= */
    if (empty($_FILES['image']['name'])) {
        die('Vui lòng chọn ảnh sản phẩm');
    }

    $uploadDir  = "/uploads/$brandSlug/";
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch" . $uploadDir;

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        die('Định dạng ảnh không hợp lệ');
    }

    $fileName  = time() . '_' . uniqid() . '.' . $ext;
    $imagePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $fileName)) {
        die('Upload ảnh thất bại');
    }

    /* =========================
       INSERT PRODUCT
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO watches (
            idwatch, namewatch, idbrand, idgender,
            case_material_id, strap_material_id, glass_material_id,
            case_color_id,
            price, quantity, country, loaimay,
            kichcomatso, doday, mota, image
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssiiiiii ddsssddss",
        $idwatch,
        $_POST['namewatch'],
        $_POST['idbrand'],
        $_POST['idgender'],
        $_POST['case_material_id'],
        $_POST['strap_material_id'],
        $_POST['glass_material_id'],
        $_POST['case_color_id'],
        $_POST['price'],
        $_POST['quantity'],
        $_POST['country'],
        $_POST['loaimay'],
        $_POST['kichcomatso'],
        $_POST['doday'],
        $_POST['mota'],
        $imagePath
    );

    if (!$stmt->execute()) {
        die('Lỗi thêm sản phẩm: ' . $stmt->error);
    }

    // LOG
    admin_log(
        'CREATE_PRODUCT',
        'product',
        null,
        'Thêm sản phẩm: ' . $_POST['namewatch']
    );

    header("Location: product_manage.php");
    exit;
}

include __DIR__ . '/../includes_admin/header.php';
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <div class="page-header">
        <h1>Thêm sản phẩm mới</h1>
        <a href="product_manage.php" class="btn-add">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" class="product-form">
        <div class="form-grid">

            <div class="form-row full">
                <label>Ảnh sản phẩm</label>
                <img src="/AureliusWatch/uploads/no-image.png"
                     class="thumb-edit"
                     id="previewImage">
                <input type="file" name="image" accept="image/*" onchange="previewFile(this)" required>
            </div>

            <div class="form-row full">
                <label>Tên đồng hồ</label>
                <input type="text" name="namewatch" required>
                <small class="hint">
                    <a href="/AureliusWatch/admin/category/category.php?tab=brands" target="_blank">
                        Thêm danh mục
                    </a>
                </small>
            </div>

            <div class="form-row">
                <label>Hãng</label>
                <select name="idbrand" required>
                    <option value="">-- Chọn hãng --</option>
                    <?php while ($b = $brands->fetch_assoc()): ?>
                        <option value="<?= $b['idbrand'] ?>">
                            <?= htmlspecialchars($b['namebrand']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Giới tính</label>
                <select name="idgender" required>
                    <option value="">-- Chọn --</option>
                    <?php while ($g = $genders->fetch_assoc()): ?>
                        <option value="<?= $g['idgender'] ?>">
                            <?= htmlspecialchars($g['namegender']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu vỏ</label>
                <select name="case_material_id" required>
                    <option value="">-- Chọn --</option>
                    <?php while ($m = $caseM->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>">
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu dây</label>
                <select name="strap_material_id" required>
                    <option value="">-- Chọn --</option>
                    <?php while ($m = $strapM->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>">
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu kính</label>
                <select name="glass_material_id" required>
                    <option value="">-- Chọn --</option>
                    <?php while ($m = $glassM->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>">
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Màu vỏ</label>
                <select name="case_color_id" required>
                    <option value="">-- Chọn màu --</option>
                    <?php while ($c = $caseColors->fetch_assoc()): ?>
                        <option value="<?= $c['idcolor'] ?>">
                            <?= htmlspecialchars($c['namecolor']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row"><label>Giá</label><input type="number" name="price" required></div>
            <div class="form-row"><label>Số lượng</label><input type="number" name="quantity" required></div>
            <div class="form-row"><label>Xuất xứ</label><input name="country"></div>
            <div class="form-row"><label>Loại máy</label><input name="loaimay"></div>
            <div class="form-row"><label>Size mặt (mm)</label><input type="number" step="0.1" name="kichcomatso"></div>
            <div class="form-row"><label>Độ dày (mm)</label><input type="number" step="0.1" name="doday"></div>

            <div class="form-row full">
                <label>Mô tả</label>
                <textarea name="mota" rows="4"></textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-add">
                <i class="fa fa-save"></i> Thêm sản phẩm
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes_admin/footer.php'; ?>

<script>
function previewFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImage').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
