<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

/* =========================
   DETECT MODE
========================= */
$mode = $_POST['mode'] ?? $_GET['mode'] ?? 'cart';
$total = 0;
$items = null;

/* =========================
   MODE: BUY NOW
========================= */
if ($mode === 'buynow') {

    $idwatch = $_GET['idwatch'] ?? null;
    if (!$idwatch) {
        die("Sản phẩm mua ngay không hợp lệ");
    }

    $stmt = $conn->prepare("
        SELECT idwatch, namewatch, price, 1 AS quantity
        FROM watches
        WHERE idwatch = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $idwatch);
    $stmt->execute();
    $items = $stmt->get_result();

    if ($items->num_rows === 0) {
        die("Sản phẩm không tồn tại");
    }
}

/* =========================
   MODE: CART
========================= */
else {

    $selectedItems = $_POST['cart_items'] ?? [];

    if (empty($selectedItems)) {
        die("Vui lòng chọn sản phẩm để thanh toán");
    }

    $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
    $types = str_repeat('i', count($selectedItems));

    $sql = "
        SELECT ci.iditem, ci.quantity, ci.price, w.namewatch
        FROM cart_items ci
        JOIN watches w ON ci.idwatch = w.idwatch
        WHERE ci.iditem IN ($placeholders)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$selectedItems);
    $stmt->execute();
    $items = $stmt->get_result();
}
?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/checkout.css">

<div class="checkout-container">
<form action="process.php" method="post" class="checkout-form">

<input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">

<?php if ($mode === 'cart'): ?>
    <?php foreach ($selectedItems as $id): ?>
        <input type="hidden" name="cart_items[]" value="<?= $id ?>">
    <?php endforeach; ?>
<?php else: ?>
    <input type="hidden" name="idwatch" value="<?= htmlspecialchars($idwatch) ?>">
<?php endif; ?>

<!-- ================= LEFT ================= -->
<div class="checkout-left">
    <h2>THÔNG TIN THANH TOÁN</h2>
    <input type="text" name="fullname" required placeholder="Họ và tên">
    <input type="text" name="phone" required placeholder="Số điện thoại">
    <input type="email" name="email" required placeholder="Email">
    <input type="text" name="address" required placeholder="Địa chỉ">
    <textarea name="note" placeholder="Ghi chú"></textarea>
</div>

<!-- ================= RIGHT ================= -->
<div class="checkout-right">
    <h2>ĐƠN HÀNG</h2>

<?php while ($i = $items->fetch_assoc()):
    $sub = $i['price'] * $i['quantity'];
    $total += $sub;
?>
    <div class="order-item">
        <span><?= htmlspecialchars($i['namewatch']) ?> × <?= $i['quantity'] ?></span>
        <strong><?= number_format($sub) ?> ₫</strong>
    </div>
<?php endwhile; ?>

    <div class="order-total">
        <strong>Tổng cộng: <?= number_format($total) ?> ₫</strong>
    </div>

    <div class="payment-method">
    <h3>PHƯƠNG THỨC THANH TOÁN</h3>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="vnpay">
        <img src="/AureliusWatch/assets/images/vnpay.png" alt="VNPay">
        <span>VNPay (QR)</span>
    </label>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="visa">
        <img src="/AureliusWatch/assets/images/visa.png" alt="Visa">
        <span>Thẻ Visa</span>
    </label>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="cod">
        <img src="/AureliusWatch/assets/images/cod.png" alt="COD">
        <span>Thanh toán khi nhận hàng</span>
    </label>
</div>

    <button type="submit" class="btn-order">ĐẶT HÀNG</button>
</div>

</form>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
