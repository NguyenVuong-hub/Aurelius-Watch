<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/config/db.php";
include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/header.php";

$user_id = $_SESSION['user']['id'] ?? null;
if (!isset($_SESSION['cart_session'])) {
    $_SESSION['cart_session'] = session_id();
}
$session_id = $_SESSION['cart_session'];

/* ===== GET CART ACTIVE ===== */
if ($user_id) {
    $stmt = $conn->prepare("SELECT idcart FROM carts WHERE user_id=? AND status='active' LIMIT 1");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("SELECT idcart FROM carts WHERE session_id=? AND status='active' LIMIT 1");
    $stmt->bind_param("s", $session_id);
}
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cart) {
    echo "<p style='text-align:center;margin:80px'>Giỏ hàng trống</p>";
    include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php";
    exit;
}

$idcart = $cart['idcart'];

/* ===== GET ITEMS ===== */
$stmt = $conn->prepare("
    SELECT 
        ci.iditem,
        ci.quantity,
        ci.price,
        w.namewatch,
        b.namebrand
    FROM cart_items ci
    JOIN watches w ON ci.idwatch = w.idwatch
    JOIN brands b ON w.idbrand = b.idbrand
    WHERE ci.idcart = ?
");
$stmt->bind_param("i", $idcart);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();

?>

<link rel="stylesheet" href="/AureliusWatch/assets/css/cart.css">

<div class="cart-container">
<h1>GIỎ HÀNG</h1>

<form method="post" action="/AureliusWatch/pages/checkout/checkout.php">

<table class="cart-table">
<tr>
    <th class="col-check">
        <input type="checkbox" id="check-all" checked>
    </th>
    <th>Sản phẩm</th>
    <th>Giá</th>
    <th>Số lượng</th>
    <th>Tạm tính</th>
    <th></th>
</tr>

<?php
function slugify($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $string);
    return trim($string, '-');
}
?>

<?php while ($item = $items->fetch_assoc()):
    $sub = $item['price'] * $item['quantity'];

    $brandSlug = slugify($item['namebrand']);
$nameSlug  = slugify($item['namewatch']);

$img = "/AureliusWatch/uploads/no-image.png";
$dir = $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/uploads/$brandSlug/";

if (is_dir($dir)) {
    foreach (glob($dir."*.{jpg,jpeg,png,webp}", GLOB_BRACE) as $f) {
        if (strpos(slugify(pathinfo($f, PATHINFO_FILENAME)), $nameSlug) !== false) {
            $img = "/AureliusWatch/uploads/$brandSlug/" . basename($f);
            break;
        }
    }
}
?>

<tr class="cart-row active"
    data-price="<?= $item['price'] ?>"
    data-qty="<?= $item['quantity'] ?>">

    <td class="col-check">
        <input type="checkbox"
               class="item-check"
               name="cart_items[]"
               value="<?= $item['iditem'] ?>"
               checked>
    </td>

    <td class="cart-product">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['namewatch']) ?>">
        <span><?= htmlspecialchars($item['namewatch']) ?></span>
    </td>

    <td class="cart-price"><?= number_format($item['price']) ?> ₫</td>

    <td>
        <div class="qty-box">
            <button type="button" class="qty-btn" onclick="changeQty(this,-1)">−</button>
            <input type="text"
                   class="qty-input"
                   data-id="<?= $item['iditem'] ?>"
                   value="<?= $item['quantity'] ?>"
                   readonly>
            <button type="button" class="qty-btn" onclick="changeQty(this,1)">+</button>
        </div>
    </td>

    <td class="cart-subtotal">
        <span class="sub-money"><?= number_format($sub) ?> ₫</span>
    </td>

    <td>
        <a href="remove_cart.php?iditem=<?= $item['iditem'] ?>" class="remove">✕</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<div class="cart-summary">
    <h2>
        TỔNG TIỀN:
        <span id="cart-total">0 ₫</span>
    </h2>
    <button type="submit" class="btn-checkout">
        TIẾN HÀNH THANH TOÁN
    </button>
</div>

</form>
</div>

<script>
/* =========================
   FORMAT MONEY
========================= */
function formatMoney(n) {
    return n.toLocaleString('vi-VN') + ' ₫';
}

/* =========================
   ANIMATE NUMBER
========================= */
function animateNumber(el, from, to) {
    const duration = 350;
    const start = performance.now();

    function run(now) {
        const progress = Math.min((now - start) / duration, 1);
        const value = Math.floor(from + (to - from) * progress);
        el.innerText = formatMoney(value);
        if (progress < 1) requestAnimationFrame(run);
    }
    requestAnimationFrame(run);
}

/* =========================
   UPDATE TOTAL
========================= */
function updateTotal(animated = true) {
    let total = 0;

    document.querySelectorAll('.item-check').forEach(cb => {
        if (cb.checked) {
            const row = cb.closest('.cart-row');
            total += Number(row.dataset.price) * Number(row.dataset.qty);
        }
    });

    const totalEl = document.getElementById('cart-total');
    const current = parseInt(totalEl.innerText.replace(/\D/g,'')) || 0;

    animated
        ? animateNumber(totalEl, current, total)
        : totalEl.innerText = formatMoney(total);
}

/* =========================
   CHECKBOX LOGIC (2 CHIỀU)
========================= */
const checkAll = document.getElementById('check-all');
const itemChecks = document.querySelectorAll('.item-check');

/* Check all → check hết item */
checkAll.addEventListener('change', function () {
    itemChecks.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('.cart-row').classList.toggle('active', cb.checked);
    });
    updateTotal();
});

/* Item → sync lại check-all */
itemChecks.forEach(cb => {
    cb.addEventListener('change', function () {
        this.closest('.cart-row').classList.toggle('active', this.checked);

        // 🔥 nếu TẤT CẢ item đều checked → check-all checked
        // 🔥 nếu CÓ ÍT NHẤT 1 item unchecked → check-all unchecked
        checkAll.checked = [...itemChecks].every(c => c.checked);

        updateTotal();
    });
});

/* =========================
   CHANGE QTY REALTIME
========================= */
function changeQty(btn, diff) {
    const row = btn.closest('.cart-row');
    const input = row.querySelector('.qty-input');
    let qty = parseInt(input.value) + diff;
    if (qty < 1) return;

    input.value = qty;
    row.dataset.qty = qty;

    const price = Number(row.dataset.price);
    row.querySelector('.sub-money').innerText = formatMoney(price * qty);

    updateTotal();

    // sync server (optional)
    fetch('update_cart.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'iditem=' + input.dataset.id + '&quantity=' + qty
    });
}

/* =========================
   INIT
========================= */
updateTotal(false);
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/AureliusWatch/includes/footer.php"; ?>
