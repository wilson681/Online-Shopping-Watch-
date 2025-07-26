<?php
require '../head.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 获取收藏商品列表
$stmt = $db->prepare("
    SELECT p.product_id, p.name, p.price,
        (SELECT image_url FROM productimages WHERE product_id = p.product_id ORDER BY image_id ASC LIMIT 1) AS image
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Wishlist - QTIME</title>
  <link rel="stylesheet" href="../css/wishlist.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
</head>
<body>
<div class="page-container">

  <!-- ✅ 外层包装 -->
  <div class="wishlist-wrapper">
    <h2 class="wishlist-title">My Wishlist</h2>

    <?php if (empty($items)): ?>
      <p class="empty-msg">You have no items in your wishlist.</p>
    <?php else: ?>
      <div class="wishlist-grid">
        <?php foreach ($items as $item): ?>
          <div class="wishlist-item">
            <a href="../homepage/product_detail.php?product_id=<?= $item['product_id'] ?>">
              <img src="<?= htmlspecialchars($item['image'] ?? 'image/default.jpg') ?>" alt="Product" class="wishlist-img">
            </a>
            <div class="wishlist-details">
              <a href="../homepage/product_detail.php?product_id=<?= $item['product_id'] ?>" class="wishlist-name">
                <?= htmlspecialchars($item['name']) ?>
              </a>
              <p class="price">RM <?= number_format($item['price'], 2) ?></p>
              <button class="wishlist-remove-btn" data-product-id="<?= $item['product_id'] ?>">❤️</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../foot.php'; ?>


<!-- ✅ 專屬 wishlist 彈窗 HTML -->
<div class="wishlist-popup-overlay" id="wishlistPopupOverlay" style="display: none;"></div>
<div class="wishlist-popup" id="wishlistPopup" style="display: none;">
  <h3 id="wishlistPopupTitle">Title</h3>
  <p id="wishlistPopupMessage">Message goes here.</p>
  <button class="wishlist-popup-btn" id="wishlistPopupBtn">OK</button>
</div>

<script src="wishlist.js"></script>
</body>
</html>
