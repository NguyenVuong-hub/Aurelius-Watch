<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";

/* ===== CHECK LOGIN ===== */
if (!isset($_SESSION['user'])) {
    die("Vui lòng đăng nhập để đánh giá");
}

$iduser  = $_SESSION['user']['id'];
$idwatch = $_POST['idwatch'] ?? '';
$idorder = intval($_POST['idorder'] ?? 0);
$idorder_item = intval($_POST['idorder_item'] ?? 0);
$rating  = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

/* ===== VALIDATE ===== */
if (
    $idwatch === '' ||
    $idorder <= 0 ||
    $idorder_item <= 0 ||
    $rating < 1 || $rating > 5
) {
    die("Dữ liệu không hợp lệ");
}

/* ===== CHECK ĐÃ REVIEW CHƯA ===== */
$check = $conn->prepare("
    SELECT 1
    FROM product_reviews
    WHERE iduser = ?
      AND idorder = ?
      AND idwatch = ?
      AND idorder_item = ?
    LIMIT 1
");
$check->bind_param("iisi", $iduser, $idorder, $idwatch, $idorder_item);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    die("Bạn đã đánh giá sản phẩm này rồi");
}
$check->close();

/* ===== UPLOAD IMAGE REVIEW ===== */
$uploadedImages = [];
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/reviews/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!empty($_FILES['review_images']['name'][0])) {

    foreach ($_FILES['review_images']['tmp_name'] as $key => $tmp) {

        if ($key >= 3) break; // giới hạn 3 ảnh

        if ($_FILES['review_images']['error'][$key] !== 0) continue;

        $ext = strtolower(pathinfo($_FILES['review_images']['name'][$key], PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

        if ($_FILES['review_images']['size'][$key] > 2 * 1024 * 1024) continue; // 2MB

        $newName = 'review_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $uploadDir . $newName;

        if (move_uploaded_file($tmp, $dest)) {
            $uploadedImages[] = "uploads/reviews/$newName";
        }
    }
}

$imageString = !empty($uploadedImages)
    ? implode(',', $uploadedImages)
    : null;

/* ===== INSERT REVIEW ===== */
$insert = $conn->prepare("
    INSERT INTO product_reviews
    (iduser, idwatch, idorder, idorder_item, rating, comment, images, is_approved)
    VALUES (?, ?, ?, ?, ?, ?, ?, 0)
");

$insert->bind_param(
    "isiisss",
    $iduser,
    $idwatch,
    $idorder,
    $idorder_item,
    $rating,
    $comment,
    $imageString
);

try {
    $insert->execute();

    // 🔔 TẠO NOTIFICATION CHO ADMIN
    $reviewId = $conn->insert_id;

    $notiStmt = $conn->prepare("
        INSERT INTO admin_notifications (type, title, target_id)
        VALUES ('review', ?, ?)
    ");
    $title = "Đánh giá sản phẩm mới";
    $notiStmt->bind_param("si", $title, $reviewId);
    $notiStmt->execute();

} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        $insert->close();
        die("Bạn đã đánh giá sản phẩm này rồi");
    }
    throw $e;
}

$insert->close();

/* ===== REDIRECT ===== */
header("Location: /AureliusWatch/pages/order/order_detail.php?id=$idorder&reviewed=1");
exit;
