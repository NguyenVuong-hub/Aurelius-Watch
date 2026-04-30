<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/includes/header.php";

/* =====================
   CHECK LOGIN
===================== */
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    die("Vui lòng đăng nhập.");
}

/* =====================
   GET PARAM
===================== */
$idorder      = (int)($_GET['order'] ?? 0);
$idorder_item = (int)($_GET['item'] ?? 0);
$idwatch      = $_GET['watch'] ?? '';

if (!$idorder || !$idwatch) {
    die("Thiếu thông tin đánh giá.");
}

/* =====================
   CHECK ĐƠN + SP HỢP LỆ
===================== */
$stmt = $conn->prepare("
    SELECT oi.idorder_item, w.namewatch
    FROM orders o
    JOIN order_items oi ON o.idorder = oi.order_id
    JOIN watches w ON oi.watch_id = w.idwatch
    WHERE o.idorder = ?
      AND o.iduser = ?
      AND o.status = 'Hoàn thành'
      AND oi.watch_id = ?
    LIMIT 1
");
$stmt->bind_param("iis", $idorder, $userId, $idwatch);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    die("Bạn không thể đánh giá sản phẩm này.");
}

/* =====================
   CHECK ĐÃ REVIEW CHƯA
===================== */
$stmt = $conn->prepare("
    SELECT idreview
    FROM product_reviews
    WHERE iduser=? AND idorder=? AND idwatch=?
    LIMIT 1
");
$stmt->bind_param("iis", $userId, $idorder, $idwatch);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    die("Bạn đã đánh giá sản phẩm này rồi.");
}

/* =====================
   HANDLE POST
===================== */
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int)($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $error = "Rating không hợp lệ.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO product_reviews
            (iduser, idwatch, idorder, idorder_item, rating, comment)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isiiss",
            $userId,
            $idwatch,
            $idorder,
            $idorder_item,
            $rating,
            $comment
        );
        $stmt->execute();

        $success = "Đánh giá đã gửi, chờ duyệt.";
    }
}
?>

<div class="review-box">
    <h2>Đánh giá: <?= htmlspecialchars($item['namewatch']) ?></h2>

    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Số sao:</label>
        <select name="rating">
            <?php for ($i=5;$i>=1;$i--): ?>
                <option value="<?= $i ?>"><?= $i ?> ⭐</option>
            <?php endfor; ?>
        </select>

        <textarea name="comment" placeholder="Nhận xét của bạn..."></textarea>

        <button type="submit">Gửi đánh giá</button>
    </form>
</div>

<?php include $_SERVER['DOCUMENT_ROOT']."/AureliusWatch/includes/footer.php"; ?>
