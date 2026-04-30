<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/admin/review/review_reply.php";

/* =========================
   VALIDATE ID
========================= */
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: /AureliusWatch/pages/product/list.php");
    exit;
}

/* =========================
   SLUGIFY FUNCTION
========================= */
function slugify($string)
{
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $string);
    return trim($string, '-');
}

/* =========================
   GET PRODUCT DETAIL
========================= */
$sql = "
SELECT 
    w.*,
    b.namebrand,
    g.namegender,
    m1.namematerial AS case_material,
    m2.namematerial AS strap_material,
    m3.namematerial AS glass_material,
    cc.namecolor AS case_color
FROM watches w
JOIN brands b ON w.idbrand = b.idbrand
JOIN genders g ON w.idgender = g.idgender
LEFT JOIN materials m1 ON w.case_material_id = m1.idmaterial
LEFT JOIN materials m2 ON w.strap_material_id = m2.idmaterial
LEFT JOIN materials m3 ON w.glass_material_id = m3.idmaterial
LEFT JOIN case_colors cc ON w.case_color_id = cc.idcolor
WHERE w.idwatch = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<p style='text-align:center'>Sản phẩm không tồn tại</p>";
    include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php";
    exit;
}

/* =========================
   IMAGE
========================= */
$imagePath = "/AureliusWatch/uploads/no-image.png";

if (!empty($product['image'])) {
    $imagePath = "/AureliusWatch/" . htmlspecialchars($product['image']);
} else {
    $brandSlug = slugify($product['namebrand']);
    $nameSlug  = slugify($product['namewatch']);
    $dir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/$brandSlug/";

    if (is_dir($dir)) {
        foreach (glob($dir . "*.{jpg,jpeg,png,webp}", GLOB_BRACE) as $file) {
            if (strpos(slugify(pathinfo($file, PATHINFO_FILENAME)), $nameSlug) !== false) {
                $imagePath = "/AureliusWatch/uploads/$brandSlug/" . basename($file);
                break;
            }
        }
    }
}

/* =========================
   RATING SUMMARY
========================= */
$stmt = $conn->prepare("
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
    FROM product_reviews
    WHERE idwatch = ? AND is_approved = 1
");
$stmt->bind_param("s", $id);
$stmt->execute();
$ratingData = $stmt->get_result()->fetch_assoc();

$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$totalReviews = $ratingData['total_reviews'] ?? 0;

/* =========================
   RATING DISTRIBUTION
========================= */
$stmt = $conn->prepare("
    SELECT 
        rating,
        COUNT(*) AS total
    FROM product_reviews
    WHERE idwatch = ? AND is_approved = 1
    GROUP BY rating
");
$stmt->bind_param("s", $id);
$stmt->execute();
$rsStars = $stmt->get_result();

$starCount = [1=>0,2=>0,3=>0,4=>0,5=>0];
$totalReview = 0;

while ($row = $rsStars->fetch_assoc()) {
    $starCount[$row['rating']] = $row['total'];
    $totalReview += $row['total'];
}
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/product-detail.css">

<div class="product-detail-container">

    <div class="breadcrumb">
    <a href="/AureliusWatch/index.php">Aurelius Watch</a>
    /
    <a href="/AureliusWatch/pages/product/list.php?gender=<?= $product['idgender'] ?>">
        <?= htmlspecialchars($product['namegender']) ?>
    </a>
    /
<a href="/AureliusWatch/pages/product/list.php?brand=<?= $product['idbrand'] ?>">
    <?= htmlspecialchars($product['namebrand']) ?>
</a>

</div>


    <div class="product-detail-main">

        <div class="product-image-zoom">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['namewatch']) ?>">
        </div>

        <div class="product-info sticky-info">
            <h1><?= htmlspecialchars($product['namewatch']) ?></h1>
            <p class="product-code">Mã sản phẩm: <?= $product['idwatch'] ?></p>

            <div class="rating-summary" id="scrollToReviews">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= round($avgRating) ? '★' : '☆' ?>
                <?php endfor; ?>
                <span>(<?= $avgRating ?>/5 - <?= $totalReviews ?> đánh giá)</span>
            </div>

            <div class="price">
                <?= number_format($product['price'], 0, ',', '.') ?> ₫
            </div>

            <p class="description">
                <?= nl2br(htmlspecialchars($product['mota'])) ?>
            </p>

            <div class="actions">
                <form action="/AureliusWatch/pages/cart/add.php" method="POST">
                    <input type="hidden" name="idwatch" value="<?= $product['idwatch'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-add-cart">THÊM VÀO GIỎ</button>
                </form>

                <form action="/AureliusWatch/pages/checkout/checkout.php" method="get">
                    <input type="hidden" name="mode" value="buynow">
                    <input type="hidden" name="idwatch" value="<?= $product['idwatch'] ?>">
                    <button type="submit" class="btn-black">MUA NGAY</button>
                </form>
            </div>
        </div>
    </div>

    <!-- THÔNG TIN SẢN PHẨM -->
    <div class="product-specs">
        <h3>THÔNG TIN SẢN PHẨM</h3>
        <ul>
            <li><strong>Hãng:</strong> <?= $product['namebrand'] ?></li>
            <li><strong>Giới tính:</strong> <?= $product['namegender'] ?></li>
            <li><strong>Xuất xứ:</strong> <?= $product['country'] ?: '—' ?></li>
            <li><strong>Loại máy:</strong> <?= $product['loaimay'] ?: '—' ?></li>
            <li><strong>Màu vỏ:</strong> <?= $product['case_color'] ?: '—' ?></li>
            <li><strong>Kích cỡ mặt:</strong> <?= $product['kichcomatso'] ? $product['kichcomatso'] . ' mm' : '—' ?></li>
            <li><strong>Độ dày:</strong> <?= $product['doday'] ? $product['doday'] . ' mm' : '—' ?></li>
            <li><strong>Chất liệu vỏ:</strong> <?= $product['case_material'] ?: '—' ?></li>
            <li><strong>Dây đeo:</strong> <?= $product['strap_material'] ?: '—' ?></li>
            <li><strong>Kính:</strong> <?= $product['glass_material'] ?: '—' ?></li>
        </ul>
    </div>

    <!-- ĐÁNH GIÁ (CHỈ HIỂN THỊ) -->
    <div class="product-reviews" id="reviews">
        <h3>ĐÁNH GIÁ SẢN PHẨM</h3>
        
        <div class="rating-overview">

    <!-- TRUNG BÌNH SAO -->
    <div class="rating-average">
        <div class="avg-score"><?= number_format($avgRating, 1) ?></div>
        <div class="avg-stars">
            <?php for ($i=1; $i<=5; $i++): ?>
                <?= $i <= round($avgRating) ? '★' : '☆' ?>
            <?php endfor; ?>
        </div>
        <div class="total-review">
            <?= $totalReview ?> đánh giá
        </div>
    </div>

    <!-- BIỂU ĐỒ SAO -->
    <div class="rating-bars">
        <?php for ($i = 5; $i >= 1; $i--):
            $percent = $totalReview > 0
                ? round(($starCount[$i] / $totalReview) * 100)
                : 0;
        ?>
        <div class="rating-row">
            <span class="star-label"><?= $i ?> ★</span>
            <div class="bar">
                <div class="fill" style="width: <?= $percent ?>%"></div>
            </div>
            <span class="percent"><?= $percent ?>%</span>
        </div>
        <?php endfor; ?>
    </div>

</div>

        <?php
        $stmt = $conn->prepare("
            SELECT pr.rating, pr.comment, pr.created_at, pr.images, u.hoten, u.username
                FROM product_reviews pr
                JOIN user u ON pr.iduser = u.iduser
                WHERE pr.idwatch = ? AND pr.is_approved = 1
            ORDER BY pr.created_at DESC
        ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $reviews = $stmt->get_result();

        if ($reviews->num_rows == 0) {
            echo "<p style='text-align:center;color:#666'>Chưa có đánh giá nào.</p>";
        }

        while ($r = $reviews->fetch_assoc()): ?>
    <div class="review-item">

        <!-- USER INFO -->
        <strong><?= htmlspecialchars($r['hoten'] ?: $r['username']) ?></strong>

        <div class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?= $i <= $r['rating'] ? '★' : '☆' ?>
            <?php endfor; ?>
        </div>

        <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
        <small><?= date('d/m/Y', strtotime($r['created_at'])) ?></small>

        <!-- REVIEW IMAGES -->
        <?php if (!empty($r['images'])): ?>
            <div class="review-images">
                <?php foreach (explode(',', $r['images']) as $img): ?>
                    <img
                        src="/AureliusWatch/<?= htmlspecialchars($img) ?>"
                        alt="Review image"
                        onclick="openReviewImage(this.src)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ADMIN REPLY -->
        <div class="admin-reply">
            <strong>Aurelius Watch</strong>
            <p><?= nl2br(htmlspecialchars(getAdminReplyByRating($r['rating']))) ?></p>
        </div>

    </div>
<?php endwhile; ?>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
<?php
$sqlRelated = "
    SELECT idwatch, namewatch, price, image
    FROM watches
    WHERE idbrand = ?
      AND idwatch != ?
    ORDER BY idwatch DESC
    LIMIT 4
";

$stmt = $conn->prepare($sqlRelated);
$stmt->bind_param("is", $product['idbrand'], $product['idwatch']);
$stmt->execute();
$related = $stmt->get_result();

if ($related->num_rows > 0):
?>
    <div class="related-products">
        <h2>SẢN PHẨM CÙNG HÃNG</h2>
        <div class="related-grid">
            <?php while ($r = $related->fetch_assoc()): ?>
                <?php
                $rImg = "/AureliusWatch/uploads/no-image.png";

                if (!empty($r['image'])) {
                    $rImg = "/AureliusWatch/" . htmlspecialchars($r['image']);
                } else {
                    $rBrandSlug = slugify($product['namebrand']);
                    $rNameSlug  = slugify($r['namewatch']);
                    $rDir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/$rBrandSlug/";

                    if (is_dir($rDir)) {
                        foreach (glob($rDir."*.{jpg,jpeg,png,webp}", GLOB_BRACE) as $f) {
                            if (strpos(slugify(pathinfo($f, PATHINFO_FILENAME)), $rNameSlug) !== false) {
                                $rImg = "/AureliusWatch/uploads/$rBrandSlug/" . basename($f);
                                break;
                            }
                        }
                    }
                }
                ?>
                <a href="detail.php?id=<?= $r['idwatch'] ?>" class="related-card">
                    <img src="<?= $rImg ?>" alt="<?= htmlspecialchars($r['namewatch']) ?>">
                    <h4><?= htmlspecialchars($r['namewatch']) ?></h4>
                    <p><?= number_format($r['price'], 0, ',', '.') ?> ₫</p>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>
</div>

<script>
function openReviewImage(src) {
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = 0;
    overlay.style.left = 0;
    overlay.style.width = '100%';
    overlay.style.height = '100%';
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
    img.style.boxShadow = '0 0 30px rgba(0,0,0,0.5)';

    overlay.appendChild(img);
    overlay.onclick = () => overlay.remove();
    document.body.appendChild(overlay);
}
</script>

<script>
document.getElementById('scrollToReviews')?.addEventListener('click', function () {
    const reviews = document.getElementById('reviews');
    if (!reviews) return;

    const headerOffset = 90; // chỉnh theo chiều cao header của bạn
    const elementPosition = reviews.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
});
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
