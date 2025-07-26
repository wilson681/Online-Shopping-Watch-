<?php
require '../base.php';
header('Content-Type: text/html');

if (!isset($_SESSION['user_id'])) {
    echo "<p>You are not logged in.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

$stmt = $db->prepare("
    SELECT p.product_id, p.name, p.price, p.stock, ci.quantity, pi.image_url
    FROM cartitems ci
    JOIN products p ON ci.product_id = p.product_id
    LEFT JOIN (
        SELECT product_id, MIN(image_url) AS image_url
        FROM productimages
        GROUP BY product_id
    ) pi ON p.product_id = pi.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

$total = 0;

foreach ($items as $item):
    $productId = $item['product_id'];
    $img = htmlspecialchars($item['image_url'] ?? 'image/default.jpg');
    $name = htmlspecialchars($item['name']);
    $price = $item['price'];
    $qty = (int)$item['quantity'];
    $stock = (int)$item['stock'];
    $subtotal = $price * $qty;
    $total += $subtotal;
?>
    <div class="cart-item" data-product-id="<?= $productId ?>">
        <img src="<?= $img ?>" alt="<?= $name ?>" class="cart-item-img">
        <div class="cart-item-details">
            <p class="cart-item-name"><?= $name ?></p>
            <p class="cart-item-price">Unit: RM <?= number_format($price, 2) ?></p>
            <label>Qty:
                <input type="number"
                       class="cart-qty-input"
                       value="<?= $qty ?>"
                       min="1"
                       max="<?= $stock ?>"
                       data-stock="<?= $stock ?>"> <!-- ✅ 用于 JS 验证 -->
            </label>
            <p class="cart-item-subtotal">Subtotal: RM <?= number_format($subtotal, 2) ?></p>
            <button class="remove-btn" data-product-id="<?= $productId ?>">Remove</button>
        </div>
    </div>
<?php endforeach; ?>

<!-- ✅ 总价 -->
<div class="cart-total">
    <strong>Total: RM <?= number_format($total, 2) ?></strong>
</div>

<!-- ✅ 底部按钮 -->
<div class="cart-footer-buttons">
    <button onclick="validateCartBeforeRedirect('cart.php')" class="view-cart-btn">View Cart Page</button>
    <button onclick="validateCartBeforeRedirect('checkout.php')" class="checkout-btn">Checkout</button>
</div>

