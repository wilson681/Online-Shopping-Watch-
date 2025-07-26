<?php
require '../head.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/login.php");
    exit;
}

// Get user info
$stmt = $db->prepare("SELECT name, address, postcode, state, phone FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get cart items
$stmt = $db->prepare("
    SELECT p.product_id, p.name, p.price, ci.quantity,
           (SELECT image_url FROM productimages WHERE product_id = p.product_id ORDER BY image_id ASC LIMIT 1) AS image
    FROM carts c
    JOIN cartitems ci ON c.cart_id = ci.cart_id
    JOIN products p ON ci.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - QTIME</title>
  <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
<div class="page-container">
  <div class="checkout-container">

    <!-- Left: Info & Payment -->
    <div class="checkout-left">
      <div class="checkout-card">
        <h2>Shipping Information</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?>, <?= htmlspecialchars($user['postcode']) ?>, <?= htmlspecialchars($user['state']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
      </div>

      <div class="checkout-card">
        <h2>Select Payment Method</h2>
        <div class="payment-methods">
          <label><input type="radio" name="payment_method" value="tng" checked> TNG eWallet</label>
          <label><input type="radio" name="payment_method" value="card"> Credit / Debit Card</label>
          <label><input type="radio" name="payment_method" value="cod"> Cash on Delivery</label>
        </div>

        <!-- ✅ Card Form Inputs -->
        <div class="card-form">
          <div class="card-field">
            <label>Card Number</label>
            <input type="text" id="card-number" placeholder="1234 5678 1234 5678">
          </div>
          <div class="card-field">
            <label>Name on Card</label>
            <input type="text" id="card-name" placeholder="John Doe">
          </div>
          <div class="card-row">
            <div class="card-field">
              <label>Expiry</label>
              <input type="text" id="card-expiry" placeholder="MM/YY">
            </div>
            <div class="card-field">
              <label>CVV</label>
              <input type="text" id="card-cvv" placeholder="123">
            </div>
          </div>
        </div>

        <!-- ✅ Credit Card Visual Widget -->
        <div class="card-widget">
          <div class="card-inner" id="card-inner">
            <!-- Front Side -->
            <div class="card-front">
              <div class="card-logo">VISA</div>
              <div class="card-number" id="display-card-number">#### #### #### ####</div>
              <div class="card-footer">
                <div class="card-name-label">Card Holder</div>
                <div class="card-expiry-label">Expires</div>
                <div class="card-name" id="display-card-name">FULL NAME</div>
                <div class="card-expiry" id="display-card-expiry">MM/YY</div>
              </div>
            </div>
            <!-- Back Side -->
            <div class="card-back">
              <div class="card-strip"></div>
              <div class="card-cvv-box">
                <span>CVV</span>
                <div id="display-card-cvv">###</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Order Summary -->
    <div class="checkout-right">
      <div class="checkout-card">
        <h2>Your Order</h2>
        <?php foreach ($items as $item): 
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
        ?>
        <div class="checkout-item">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="Product" class="summary-img">
          <div class="summary-details">
            <p class="checkout-item-name"><?= htmlspecialchars($item['name']) ?></p>
            <p>Qty: <?= $item['quantity'] ?></p>
            <p>RM <?= number_format($subtotal, 2) ?></p>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="checkout-total">
          <strong>Total: RM <?= number_format($total, 2) ?></strong>
        </div>

        <button class="place-order-btn">Place Order</button>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Loading 动画弹窗 -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
  <div class="loading-text">
    Placing your order<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
  </div>
</div>

<!-- ✅ 下单成功弹窗 -->
<div class="success-popup" id="orderSuccessPopup" style="display: none;">
  <div class="success-message">
    <p>Your order has been placed</p>
    <button onclick="window.location.href='../homepage/home.php'">Return to Homepage</button>
  </div>
</div>

<?php include '../foot.php'; ?>
<script src="checkout.js"></script>
</body>
</html>
