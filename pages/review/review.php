<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    die("Vui lòng đăng nhập");
}

$userId  = $_SESSION['user']['id'];
$idorder = intval($_GET['idorder'] ?? 0);
$idwatch = $_GET['idwatch'] ?? '';
$idorder_item  = intval($_GET['idorder_item'] ?? 0);

if ($idorder <= 0 || $idwatch === '') {
    die("Dữ liệu không hợp lệ");
}

/* ===== LẤY THÔNG TIN SẢN PHẨM ===== */
$stmt = $conn->prepare("
    SELECT namewatch, image 
    FROM watches 
    WHERE idwatch = ?
");
$stmt->bind_param("s", $idwatch);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại");
}

/* ===== KIỂM TRA ĐÃ REVIEW CHƯA ===== */
$check = $conn->prepare("
    SELECT idreview 
    FROM product_reviews
    WHERE idorder = ?
      AND idwatch = ?
      AND iduser = ?
      AND idorder_item = ?
    LIMIT 1
");
$check->bind_param("isii", $idorder, $idwatch, $idorder_item, $userId);
$check->execute();
$reviewed = $check->get_result()->num_rows > 0;

// ===== IMAGE HANDLING (GIỐNG PRODUCT DETAIL) =====
$img = "/AureliusWatch/uploads/no-image.png";

// Ưu tiên ảnh trong DB
if (!empty($product['image'])) {

    $img = "/AureliusWatch/" . ltrim($product['image'], '/');

} else {

    $brandSlug = slugify($product['namebrand']);
    $nameSlug  = slugify($product['namewatch']);

    $dir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/$brandSlug/";

    if (is_dir($dir)) {

        $files = glob($dir . "*.{jpg,jpeg,png,webp}", GLOB_BRACE);

        foreach ($files as $f) {
            $fileSlug = slugify(pathinfo($f, PATHINFO_FILENAME));
            if (strpos($fileSlug, $nameSlug) !== false) {
                $img = "/AureliusWatch/uploads/$brandSlug/" . basename($f);
                break;
            }
        }
    }
}

$img = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/review.css">

<div class="review-container">
    <!-- PRODUCT INFO -->
    <div class="review-product">
    <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['namewatch']) ?>">
    <div class="review-product-info">
        <h3><?= htmlspecialchars($product['namewatch']) ?></h3>
    </div>
</div>

    <?php if ($reviewed): ?>
        <div class="reviewed-box">
            ✔ Bạn đã đánh giá sản phẩm này.  
            <span>Cảm ơn bạn đã tin tưởng Aurelius Watch</span>
        </div>
    <?php else: ?>

        <form method="post" action="/AureliusWatch/pages/review/review_insert.php" class="review-form" enctype="multipart/form-data">
            <input type="hidden" name="idorder" value="<?= $idorder ?>">
            <input type="hidden" name="idwatch" value="<?= $idwatch ?>">
            <input type="hidden" name="idorder_item" value="<?= $idorder_item ?>">

            <!-- STAR RATING -->
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5">★</label>

                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">★</label>

                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>

                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">★</label>

                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">★</label>
            </div>

            <div class="rating-error" id="ratingError">
                Vui lòng chọn số sao đánh giá
            </div>

            <!-- COMMENT -->
            <textarea name="comment" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>

            <!-- UPLOAD IMAGE -->
            <div class="review-upload" id="uploadArea">
                <label for="review-images" class="upload-btn">
                    ＋ Thêm hình ảnh
                </label>

                <input type="file"
                    id="review-images"
                    name="review_images[]"
                    accept="image/*"
                    multiple>

                <small id="uploadCounter">0 / 3 ảnh</small>
            </div>

            <!-- PREVIEW -->
            <div class="review-preview" id="reviewPreview"></div>

            <button type="submit"
                class="btn-submit-review"
                id="submitReviewBtn"
                disabled>
                GỬI ĐÁNH GIÁ
            </button>

        </form>

    <?php endif; ?>
</div>

<!-- TOAST -->
<?php if (isset($_GET['success'])): ?>
<div id="toast" class="toast-success">
    ★ Gửi đánh giá thành công
</div>
<script>
setTimeout(() => document.getElementById('toast').classList.add('show'), 100);
setTimeout(() => document.getElementById('toast').classList.remove('show'), 3000);
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================
       STAR RATING + SUBMIT
    ========================= */
    const ratingInputs = document.querySelectorAll('.star-rating input');
    const submitBtn = document.getElementById('submitReviewBtn');
    const ratingError = document.getElementById('ratingError');

    if (submitBtn) submitBtn.disabled = true;

    ratingInputs.forEach(input => {
        input.addEventListener('change', () => {
            submitBtn.disabled = false;
            ratingError.style.display = 'none';
        });
    });

    /* =========================
       IMAGE UPLOAD + PREVIEW
    ========================= */
    const input = document.getElementById('review-images');
    const preview = document.getElementById('reviewPreview');
    const counter = document.getElementById('uploadCounter');
    const uploadArea = document.getElementById('uploadArea');

    let filesArray = [];
    const MAX_FILES = 3;

    if (!input) return;

    input.addEventListener('change', e => {
        addFiles(e.target.files);
    });

    ['dragenter','dragover'].forEach(evt => {
        uploadArea.addEventListener(evt, e => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
    });

    ['dragleave','drop'].forEach(evt => {
        uploadArea.addEventListener(evt, e => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });
    });

    uploadArea.addEventListener('drop', e => {
        addFiles(e.dataTransfer.files);
    });

    function addFiles(files) {
        for (let file of files) {
            if (!file.type.startsWith('image/')) continue;
            if (filesArray.length >= MAX_FILES) {
                alert('Chỉ được tối đa 3 ảnh');
                break;
            }
            filesArray.push(file);
        }
        renderPreview();
        syncInput();
    }

    function renderPreview() {
        preview.innerHTML = '';
        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}">
                    <span class="preview-remove" data-index="${index}">×</span>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
        counter.textContent = `${filesArray.length} / ${MAX_FILES} ảnh`;
    }

    preview.addEventListener('click', e => {
        if (e.target.classList.contains('preview-remove')) {
            const index = e.target.dataset.index;
            filesArray.splice(index, 1);
            renderPreview();
            syncInput();
        }
    });

    function syncInput() {
        const dt = new DataTransfer();
        filesArray.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
