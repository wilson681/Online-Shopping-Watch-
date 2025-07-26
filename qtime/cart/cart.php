<?php
require '../head.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    $cartItems = [];
} else {
    $stmt = $db->prepare("
        SELECT ci.cart_id, ci.product_id, ci.quantity,
               p.name, p.price, p.stock,
               (SELECT image_url 
                FROM productimages pi 
                WHERE pi.product_id = p.product_id 
                ORDER BY pi.image_id ASC 
                LIMIT 1) AS image
        FROM cartitems ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - QTIME</title>
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
<div class="page-container">
  <div class="cart-page">
    <div class="cart-section">
      <h1>Your Shopping Cart</h1>

      <?php if (count($cartItems) === 0): ?>
        <p>Your cart is empty.</p>
      <?php else:
        $total = 0;
        foreach ($cartItems as $item):
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
      ?>
        <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
          <img src="<?= htmlspecialchars($item['image'] ?? 'image/default.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img">
          <div class="cart-item-details">
            <p class="cart-item-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="cart-item-price">Unit: RM <?= number_format($item['price'], 2) ?></p>
            <p class="cart-item-stock">Stock: <?= (int)$item['stock'] ?></p>
            <label>Qty:
              <input type="number"
                     class="cart-qty-input"
                     value="<?= $item['quantity'] ?>"
                     min="1"
                     max="<?= $item['stock'] ?>"
                     data-stock="<?= $item['stock'] ?>">
            </label>
            <p class="cart-item-subtotal">Subtotal: RM <?= number_format($subtotal, 2) ?></p>
            <button class="remove-btn" data-product-id="<?= $item['product_id'] ?>">Remove</button>
          </div>
        </div>
      <?php endforeach; ?>

        <div class="cart-total">
          Total: RM <?= number_format($total, 2) ?>
        </div>

        <div class="cart-footer-buttons">
          <button class="checkout-btn">Checkout</button>
        </div>
      <?php endif; ?>
    </div> <!-- .cart-section -->
  </div> <!-- .cart-page -->
</div> <!-- .page-container -->

<?php include '../foot.php'; ?>
</body>
</html>
