<?php
require '../head.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// 获取产品主信息
$stmt = $db->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

// 获取产品图片列表
$imgStmt = $db->prepare("SELECT image_url FROM productimages WHERE product_id = ? ORDER BY image_id ASC");
$imgStmt->execute([$product_id]);
$images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

// 获取颜色（多对多）
$colorStmt = $db->prepare("
    SELECT c.color_name
    FROM product_colours pc
    JOIN colors c ON pc.color_id = c.color_id
    WHERE pc.product_id = ?
");
$colorStmt->execute([$product_id]);
$colors = $colorStmt->fetchAll(PDO::FETCH_COLUMN);

// ✅ 检查是否已加入 wishlist（新增部分）
$isInWishlist = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $isInWishlist = $stmt->fetchColumn() ? true : false;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - QTIME</title>
    <link rel="stylesheet" href="../css/product_detail.css">
</head>
<body>
<div class="page-container">

    <div class="product-detail-container">
        <!-- ✅ 图片区域 -->
      <!-- ✅ 图片区域：左右按钮居中对齐在图像两边 -->
<div class="image-section">
    <button class="slider-btn prev-btn" id="prev-btn">‹</button>

    <img id="product-image" src="<?= htmlspecialchars($images[0] ?? 'image/default.jpg') ?>" alt="Product Image">

    <button class="slider-btn next-btn" id="next-btn">›</button>
</div>


        <!-- ✅ 产品详情 -->
        <div class="info-section">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="price">RM <?= number_format($product['price'], 2) ?></p>
            <p class="stock">Stock: <?= $product['stock'] ?></p>
            <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <!-- ✅ 颜色选择展示 -->
            <div class="color-options">
                <?php foreach ($colors as $color): ?>
                    <label class="color-option">
                        <input type="checkbox" name="color" value="<?= htmlspecialchars($color) ?>" disabled>
                        <span style="background: <?= htmlspecialchars($color) ?>;"></span> <?= htmlspecialchars($color) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- ✅ 数量选择 -->
            <div class="quantity-select">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" min="1" max="<?= $product['stock'] ?>" value="1">
            </div>

            <!-- ✅ 操作按钮 -->
            <div class="action-buttons">
                <button id="add-to-cart">Add to Cart</button>
                <button id="wishlist-btn" class="wishlist-icon <?= $isInWishlist ? 'active' : '' ?>">❤</button>
            </div>
        </div>
    </div>


    </div> 
<?php include '../foot.php'; ?>

<!-- ✅ 专属 product 页面弹窗 -->
<div class="popup-overlay" id="productPopupOverlay" style="display: none;"></div>
<div class="custom-popup" id="productPopup" style="display: none;">
    <h3 id="productPopupTitle">Message</h3>
    <p id="productPopupMessage">Something went wrong!</p>
    <button class="popup-btn" id="productPopupBtn">OK</button>
</div>



<!-- ✅ 放在所有 HTML 元素之后！ -->
<script>
    window.productImages = <?= json_encode($images) ?>;
    window.userId = <?= isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0 ?>;
</script>
<script src="product_detail.js"></script>
</body>
</html>
