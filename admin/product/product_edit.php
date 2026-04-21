<?php
require_once __DIR__ . '/../config_admin/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../includes_admin/admin_log.php';

/* ===== KIỂM TRA ID ===== */
if (!isset($_GET['id']) || $_GET['id'] === '') {
    die('ID không hợp lệ');
}

$idwatch = $_GET['id'];

/* ===== LẤY SẢN PHẨM ===== */
$stmt = $conn->prepare("SELECT * FROM watches WHERE idwatch = ? LIMIT 1");
$stmt->bind_param("s", $idwatch);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die('Không tìm thấy sản phẩm');
}
$product = $res->fetch_assoc();

/* ===== DỮ LIỆU DANH MỤC ===== */
$brands  = $conn->query("SELECT * FROM brands ORDER BY namebrand");
$genders = $conn->query("SELECT * FROM genders ORDER BY namegender");
$caseMaterials  = $conn->query("SELECT * FROM materials WHERE material_type='case'");
$strapMaterials = $conn->query("SELECT * FROM materials WHERE material_type='strap'");
$glassMaterials = $conn->query("SELECT * FROM materials WHERE material_type='glass'");
$caseColors = $conn->query("SELECT * FROM case_colors WHERE status = 1 ORDER BY namecolor");

/* ===== HELPER ===== */
function slugify($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* ===== SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namewatch = $conn->real_escape_string($_POST['namewatch']);
    $idbrand   = (int)$_POST['idbrand'];
    $idgender  = (int)$_POST['idgender'];
    $case_material_id  = (int)$_POST['case_material_id'];
    $strap_material_id = (int)$_POST['strap_material_id'];
    $glass_material_id = (int)$_POST['glass_material_id'];
    $case_color_id = (int)$_POST['case_color_id'];

    $price    = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $country  = $conn->real_escape_string($_POST['country']);
    $loaimay  = $conn->real_escape_string($_POST['loaimay']);
    $kichcomatso = (float)$_POST['kichcomatso'];
    $doday    = (float)$_POST['doday'];

    /* ===== ẢNH ===== */
    $imagePath = $product['image'];

    if (!empty($_FILES['image']['name'])) {

        $brandName = $conn->query("
            SELECT namebrand FROM brands WHERE idbrand = $idbrand
        ")->fetch_assoc()['namebrand'];

        $brandSlug = slugify($brandName);

        $uploadDir  = "/uploads/$brandSlug/";
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch" . $uploadDir;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $fileName = time() . '_' . uniqid() . '.' . $ext;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $fileName)) {

            if (!empty($product['image'])) {
                $old = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch" . $product['image'];
                if (file_exists($old)) unlink($old);
            }

            $imagePath = $uploadDir . $fileName;
        }
    }

    /* ===== UPDATE ===== */
    $sql = "
        UPDATE watches SET
            namewatch = '$namewatch',
            idbrand = $idbrand,
            idgender = $idgender,
            case_material_id = $case_material_id,
            strap_material_id = $strap_material_id,
            glass_material_id = $glass_material_id,
            case_color_id = $case_color_id,
            price = $price,
            quantity = $quantity,
            country = '$country',
            loaimay = '$loaimay',
            kichcomatso = $kichcomatso,
            doday = $doday,
            image = '$imagePath'
        WHERE idwatch = '$idwatch'
    ";

    if ($conn->query($sql)) {

        admin_log(
            'UPDATE_PRODUCT',
            'product',
            null,
            'Cập nhật sản phẩm: ' . $namewatch
        );

        header("Location: product_manage.php");
        exit;
    } else {
        echo "<div class='error'>Lỗi: {$conn->error}</div>";
    }
}

include __DIR__ . '/../includes_admin/header.php';

$imageShow = $product['image']
    ? '/AureliusWatch/' . $product['image']
    : '/AureliusWatch/uploads/no-image.png';
?>

<link rel="stylesheet" href="/AureliusWatch/admin/assets/css/admin.css">

<div class="dashboard">
    <div class="page-header">
        <h1>Chỉnh sửa sản phẩm</h1>
        <a href="product_manage.php" class="btn-add">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" class="product-form">
        <div class="form-grid">

            <div class="form-row full">
                <label>Ảnh sản phẩm</label>
                <img src="<?= $imageShow ?>" class="thumb-edit" id="previewImage">
                <input type="file" name="image" accept="image/*" onchange="previewFile(this)">
            </div>

            <div class="form-row">
                <label>Tên đồng hồ</label>
                <input type="text" name="namewatch" value="<?= htmlspecialchars($product['namewatch']) ?>" required>
            </div>

            <div class="form-row">
                <label>Hãng</label>
                <select name="idbrand" required>
                    <?php while ($b = $brands->fetch_assoc()): ?>
                        <option value="<?= $b['idbrand'] ?>" <?= $product['idbrand'] == $b['idbrand'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['namebrand']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Giới tính</label>
                <select name="idgender" required>
                    <?php while ($g = $genders->fetch_assoc()): ?>
                        <option value="<?= $g['idgender'] ?>" <?= $product['idgender'] == $g['idgender'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['namegender']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu vỏ</label>
                <select name="case_material_id" required>
                    <?php while ($m = $caseMaterials->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>" <?= $product['case_material_id'] == $m['idmaterial'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu dây</label>
                <select name="strap_material_id" required>
                    <?php while ($m = $strapMaterials->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>" <?= $product['strap_material_id'] == $m['idmaterial'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Chất liệu kính</label>
                <select name="glass_material_id" required>
                    <?php while ($m = $glassMaterials->fetch_assoc()): ?>
                        <option value="<?= $m['idmaterial'] ?>" <?= $product['glass_material_id'] == $m['idmaterial'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['namematerial']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Màu vỏ</label>
                <select name="case_color_id" required>
                    <?php while ($c = $caseColors->fetch_assoc()): ?>
                        <option value="<?= $c['idcolor'] ?>" <?= $product['case_color_id'] == $c['idcolor'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['namecolor']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row"><label>Giá</label><input type="number" name="price" value="<?= $product['price'] ?>" required></div>
            <div class="form-row"><label>Số lượng</label><input type="number" name="quantity" value="<?= $product['quantity'] ?>" required></div>
            <div class="form-row"><label>Xuất xứ</label><input name="country" value="<?= htmlspecialchars($product['country']) ?>"></div>
            <div class="form-row"><label>Loại máy</label><input name="loaimay" value="<?= htmlspecialchars($product['loaimay']) ?>"></div>
            <div class="form-row"><label>Size mặt (mm)</label><input type="number" step="0.1" name="kichcomatso" value="<?= $product['kichcomatso'] ?>"></div>
            <div class="form-row"><label>Độ dày (mm)</label><input type="number" step="0.1" name="doday" value="<?= $product['doday'] ?>"></div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-add">
                <i class="fa fa-save"></i> Lưu thay đổi
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
