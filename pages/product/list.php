<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

/* ===== SEARCH HELPERS (REUSE CHATBOT LOGIC) ===== */

function findBrandByText($msg)
{
    global $conn;
    return $conn->query("
        SELECT idbrand 
        FROM brands 
        WHERE LOWER('$msg') LIKE CONCAT('%', LOWER(namebrand), '%')
        LIMIT 1
    ")->fetch_assoc();
}

function findGenderByText($msg)
{
    $msg = mb_strtolower($msg, 'UTF-8');

    if (preg_match('/\b(unisex|đôi|nam\s*nữ)\b/u', $msg)) return 3;
    if (preg_match('/\b(nam|men|male)\b/u', $msg)) return 1;
    if (preg_match('/\b(nữ|nu|women|female)\b/u', $msg)) return 2;

    return null;
}

function findColorByText($msg)
{
    global $conn;
    return $conn->query("
        SELECT idcolor 
        FROM case_colors 
        WHERE status = 1
        AND LOWER('$msg') LIKE CONCAT('%', LOWER(namecolor), '%')
        LIMIT 1
    ")->fetch_assoc();
}

function findStrapByText($msg)
{
    global $conn;
    return $conn->query("
        SELECT idmaterial 
        FROM materials
        WHERE material_type = 'strap'
        AND LOWER('$msg') LIKE CONCAT('%', LOWER(namematerial), '%')
        LIMIT 1
    ")->fetch_assoc();
}

$isFromChatbot = ($_GET['from'] ?? '') === 'chatbot';
/* =========================
   LOAD CATEGORIES (DYNAMIC)
========================= */

// Brands
$brands = $conn->query("
    SELECT idbrand, namebrand 
    FROM brands 
    ORDER BY namebrand
");

// Genders
$genders = $conn->query("
    SELECT idgender, namegender 
    FROM genders 
    ORDER BY idgender
");

// Case colors
$colors = $conn->query("
    SELECT idcolor, namecolor 
    FROM case_colors 
    WHERE status = 1
    ORDER BY namecolor
");

// Strap materials
$straps = $conn->query("
    SELECT idmaterial, namematerial 
    FROM materials 
    WHERE material_type = 'strap'
    ORDER BY namematerial
");

/* =========================
   SAFE SLUGIFY (NO ICONV BUG)
========================= */
function slugify($string)
{
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $string);
    return trim($string, '-');
}

/* =========================
   PAGINATION
========================= */
$limit  = 8;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$q = trim($_GET['q'] ?? '');

/* =========================
   FILTER CONDITIONS
========================= */
$where = [];
$params = [];
$types  = "";

if (!empty($_GET['brand'])) {
    $where[] = "w.idbrand = ?";
    $params[] = (int)$_GET['brand'];
    $types   .= "i";
}

if (!empty($_GET['gender'])) {

    $genderId = (int)$_GET['gender'];

    // Nam hoặc Nữ → cho phép cả Unisex (id = 3)
    if (in_array($genderId, [1, 2])) {
        $where[] = "w.idgender IN (?, 3)";
        $params[] = $genderId;
        $types   .= "i";
    } 
    // Unisex → chỉ Unisex
    else {
        $where[] = "w.idgender = ?";
        $params[] = $genderId;
        $types   .= "i";
    }
}
if ($q !== '') {
    $brand  = findBrandByText($q);
    $gender = findGenderByText($q);
    $color  = findColorByText($q);
    $strap  = findStrapByText($q);

    if ($brand) {
        $where[] = "w.idbrand = ?";
        $params[] = (int)$brand['idbrand'];
        $types   .= "i";
    }

    if ($gender) {
        if (in_array($gender, [1,2])) {
            $where[] = "w.idgender IN (?,3)";
            $params[] = $gender;
            $types   .= "i";
        } else {
            $where[] = "w.idgender = ?";
            $params[] = $gender;
            $types   .= "i";
        }
    }

    if ($color) {
        $where[] = "w.case_color_id = ?";
        $params[] = (int)$color['idcolor'];
        $types   .= "i";
    }

    if ($strap) {
        $where[] = "w.strap_material_id = ?";
        $params[] = (int)$strap['idmaterial'];
        $types   .= "i";
    }

    /* ===== fallback search text (nếu không match gì) ===== */
    if (!$brand && !$gender && !$color && !$strap) {
        $where[] = "(
            w.namewatch LIKE ?
            OR b.namebrand LIKE ?
        )";

        $like = "%$q%";
        $params[] = $like;
        $params[] = $like;
        $types   .= "ss";
    }
}

/* =========================
   PRICE FILTER (ADD ONLY)
========================= */
if (!empty($_GET['price'])) {
    if ($_GET['price'] === 'under500') {
        $where[] = "w.price < 500000000";
    }

    if ($_GET['price'] === 'under1000') {
        $where[] = "w.price < 1000000000";
    }

    if ($_GET['price'] === 'over1000') {
        $where[] = "w.price >= 1000000000";
    }
}

if (!empty($_GET['color'])) {
    $where[] = "w.case_color_id = ?";
    $params[] = (int)$_GET['color'];
    $types   .= "i";
}

if (!empty($_GET['strap'])) {
    $where[] = "w.strap_material_id = ?";
    $params[] = (int)$_GET['strap'];
    $types   .= "i";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

/* =========================
   GET WATCHES - THÊM CỘT IMAGE
========================= */
$sql = "
SELECT DISTINCT
    w.idwatch,
    w.namewatch,
    w.price,
    w.image,
    b.namebrand,
    w.idgender,
    g.namegender,
    cc.namecolor
FROM watches w
JOIN brands b ON w.idbrand = b.idbrand
JOIN genders g ON w.idgender = g.idgender
LEFT JOIN case_colors cc ON w.case_color_id = cc.idcolor
LEFT JOIN materials m ON w.strap_material_id = m.idmaterial
$whereSQL
ORDER BY w.idwatch DESC
LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultWatches = $stmt->get_result();

/* =========================
   TOTAL PAGES
========================= */
$sqlTotal = "
SELECT COUNT(DISTINCT w.idwatch) AS total
FROM watches w
JOIN brands b ON w.idbrand = b.idbrand
JOIN genders g ON w.idgender = g.idgender
LEFT JOIN case_colors cc ON w.case_color_id = cc.idcolor
LEFT JOIN materials m ON w.strap_material_id = m.idmaterial
$whereSQL
";

$stmtTotal = $conn->prepare($sqlTotal);
if ($params) {
    $stmtTotal->bind_param($types, ...$params);
}
$stmtTotal->execute();
$totalRow   = $stmtTotal->get_result()->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/product.css">

<div class="product-container">

    <!-- FILTER BAR -->
    <div class="filter-bar">
    <div class="filter-item">
        <span class="filter-label">☰ Bộ lọc</span>
    </div>

    <div class="filter-item has-dropdown">
    <span class="filter-label">Thương hiệu</span>
    <ul class="filter-dropdown">
        <?php while ($b = $brands->fetch_assoc()): ?>
            <li data-key="brand" data-value="<?= $b['idbrand'] ?>">
                <?= htmlspecialchars($b['namebrand']) ?>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

    <div class="filter-item has-dropdown">
        <span class="filter-label">Giá thành</span>
        <ul class="filter-dropdown">
            <li data-key="price" data-value="under500">Dưới 500 triệu</li>
            <li data-key="price" data-value="under1000">Dưới 1 tỷ</li>
            <li data-key="price" data-value="over1000">Trên 1 tỷ</li>
        </ul>
    </div>

    <div class="filter-item has-dropdown">
        <span class="filter-label">Giới tính</span>
        <ul class="filter-dropdown">
            <?php while ($g = $genders->fetch_assoc()): ?>
                <li data-key="gender" data-value="<?= $g['idgender'] ?>">
                    <?= htmlspecialchars($g['namegender']) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="filter-item has-dropdown">
        <span class="filter-label">Màu vỏ</span>
        <ul class="filter-dropdown">
            <?php while ($c = $colors->fetch_assoc()): ?>
                <li data-key="color" data-value="<?= $c['idcolor'] ?>">
                    <?= htmlspecialchars($c['namecolor']) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="filter-item has-dropdown">
        <span class="filter-label">Dây đeo</span>
        <ul class="filter-dropdown">
            <?php while ($s = $straps->fetch_assoc()): ?>
                <li data-key="strap" data-value="<?= $s['idmaterial'] ?>">
                    <?= htmlspecialchars($s['namematerial']) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<?php if (!empty($_GET)): ?>
<div class="active-filters">

    <!-- BRAND -->
    <?php if (!empty($_GET['brand'])): ?>
        <?php
        $id = (int)$_GET['brand'];
        $row = $conn->query("
            SELECT namebrand 
            FROM brands 
            WHERE idbrand = $id
        ")->fetch_assoc();
        ?>
        <span class="active-filter" data-key="brand">
            <?= htmlspecialchars($row['namebrand'] ?? '') ?> 
        </span>
    <?php endif; ?>

    <?php if (!empty($_GET['price'])): ?>
    <?php
        $priceMap = [
            'under500'  => 'Dưới 500 triệu',
            'under1000' => 'Dưới 1 tỷ',
            'over1000'  => 'Trên 1 tỷ'
        ];

        $priceKey   = $_GET['price'];
        $priceLabel = $priceMap[$priceKey] ?? '';
    ?>

    <?php if ($priceLabel): ?>
        <span class="active-filter" data-key="price">
            <?= htmlspecialchars($priceLabel) ?>
        </span>
    <?php endif; ?>
<?php endif; ?>

    <!-- GENDER -->
    <?php if (!empty($_GET['gender'])): ?>
        <?php
        $id = (int)$_GET['gender'];
        $row = $conn->query("
            SELECT namegender 
            FROM genders 
            WHERE idgender = $id
        ")->fetch_assoc();
        ?>
        <span class="active-filter" data-key="gender">
            <?= htmlspecialchars($row['namegender'] ?? '') ?> 
        </span>
    <?php endif; ?>

    <!-- COLOR -->
    <?php if (!empty($_GET['color'])): ?>
        <?php
        $id = (int)$_GET['color'];
        $row = $conn->query("
            SELECT namecolor 
            FROM case_colors 
            WHERE idcolor = $id
        ")->fetch_assoc();
        ?>
        <span class="active-filter" data-key="color">
            <?= htmlspecialchars($row['namecolor'] ?? '') ?> 
        </span>
    <?php endif; ?>

    <!-- STRAP -->
    <?php if (!empty($_GET['strap'])): ?>
        <?php
        $id = (int)$_GET['strap'];
        $row = $conn->query("
            SELECT namematerial 
            FROM materials 
            WHERE idmaterial = $id
        ")->fetch_assoc();
        ?>
        <span class="active-filter" data-key="strap">
            <?= htmlspecialchars($row['namematerial'] ?? '') ?> 
        </span>
    <?php endif; ?>

    <span class="clear-all">Xóa tất cả</span>
</div>
<?php endif; ?>

<?php if ($q !== ''): ?>
    <div style="margin-bottom:20px;color:#777">
        Kết quả tìm kiếm cho:
        <strong><?= htmlspecialchars($q) ?></strong>
    </div>
<?php endif; ?>

    <!-- PRODUCT GRID -->
    <div class="product-grid">

    <?php while ($w = $resultWatches->fetch_assoc()): ?>

        <?php
        // Ưu tiên ảnh từ cột image trong database (giống trang admin)
        $imagePath = "/AureliusWatch/uploads/no-image.png";

        if (!empty($w['image'])) {
            // Nếu có đường dẫn trong DB → dùng nó
            $imagePath = "/AureliusWatch/" . htmlspecialchars($w['image']);
        } else {
            // Fallback: tự tìm file trong thư mục (logic cũ)
            $brandSlug = slugify($w['namebrand']);
            $nameSlug  = slugify($w['namewatch']);
            $dir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/$brandSlug/";

            if (is_dir($dir)) {
                foreach (glob($dir."*.{jpg,jpeg,png,webp}", GLOB_BRACE) as $file) {
                    $fileName = slugify(pathinfo($file, PATHINFO_FILENAME));
                    if (strpos($fileName, $nameSlug) !== false) {
                        $imagePath = "/AureliusWatch/uploads/$brandSlug/" . basename($file);
                        break;
                    }
                }
            }
        }
        ?>

        <a href="detail.php?id=<?= $w['idwatch'] ?>" class="product-card">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($w['namewatch']) ?>">
            <h3><?= htmlspecialchars($w['namewatch']) ?></h3>
            <p><?= number_format($w['price']) ?> ₫</p>
        </a>

    <?php endwhile; ?>

    </div>

    <?php
    $queryParams = $_GET;
	unset($queryParams['page']);

	$baseQuery = http_build_query($queryParams);
	$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>
    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">

        <?php if ($page > 1): ?>
            <a href="?<?= $baseQuery ?>page=<?= $page - 1 ?>">&laquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= $baseQuery ?>page=<?= $i ?>"
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= $baseQuery ?>page=<?= $page + 1 ?>">&raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>

<script>
// Toggle dropdown
document.querySelectorAll('.filter-label').forEach(label => {
    label.addEventListener('click', e => {
        e.stopPropagation();
        const item = label.closest('.filter-item');

        document.querySelectorAll('.filter-item').forEach(i => {
            if (i !== item) i.classList.remove('active');
        });

        item.classList.toggle('active');
    });
});

// Click ngoài thì đóng
document.addEventListener('click', () => {
    document.querySelectorAll('.filter-item')
        .forEach(i => i.classList.remove('active'));
});

// Build query string
document.querySelectorAll('.filter-dropdown li').forEach(item => {
    item.addEventListener('click', e => {
        e.stopPropagation();

        const key   = item.dataset.key;
        const value = item.dataset.value;

        const url = new URL(window.location.href);

        url.searchParams.set(key, value);
        url.searchParams.set('page', 1); // reset page

        window.location.href = url.toString();
    });
});

// Remove single filter
document.querySelectorAll('.active-filter').forEach(tag => {
    tag.addEventListener('click', () => {
        const key = tag.dataset.key;
        const url = new URL(window.location.href);

        url.searchParams.delete(key);
        url.searchParams.set('page', 1);

        window.location.href = url.toString();
    });
});

// Clear all filters
const clearAll = document.querySelector('.clear-all');
if (clearAll) {
    clearAll.addEventListener('click', () => {
        window.location.href = window.location.pathname;
    });
}
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>