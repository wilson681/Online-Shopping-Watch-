<?php
require("../auth.php");

$product_id = $_GET['product_id'] ?? null;
if (!$product_id) {
  echo "No product selected.";
  exit;
}

// Fetch product, brands, categories
$stmt = $db->prepare("SELECT p.*, b.brand_name, c.category_name
  FROM products p
  JOIN brands b ON p.brand_id = b.brand_id
  JOIN categories c ON p.category_id = c.category_id
  WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch colors
$colors = $db->query("SELECT * FROM colors")->fetchAll(PDO::FETCH_ASSOC);
$product_colors = $db->query("SELECT color_id FROM product_colours WHERE product_id = $product_id")->fetchAll(PDO::FETCH_COLUMN);

// Fetch features
$features = $db->query("SELECT * FROM features")->fetchAll(PDO::FETCH_ASSOC);
$product_features = $db->query("SELECT feature_id FROM productfeatures WHERE product_id = $product_id")->fetchAll(PDO::FETCH_COLUMN);

// Fetch images
$images = $db->query("SELECT * FROM productimages WHERE product_id = $product_id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link rel="stylesheet" href="../css/prodmanagement.css">
  <script src="prodmanagement.js" defer></script>
  <link rel="stylesheet" href="../css/update_product.css">
  <script src="update_product.js" defer></script>

</head>

<body>
<div class="prod-wrapper">
    <h2>Edit Product (ID: <?= $product_id ?>)</h2>
    <div id="popupMessage" class="popup-message" style="display:none;"></div>
    <form action="update_product.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?= $product_id ?>">

      <div class="form-group">
        <label>Product ID:</label>
        <input type="text" name="ID" value="<?= htmlspecialchars($product['product_id']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>Description:</label>
        <textarea name="description" readonly><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="form-group">
        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" readonly>
      </div>

      <div class="form-group">
        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" readonly>
      </div>

      <div class="form-group">
        <label>Colors:</label>
        <div class="checkbox-group">
          <?php foreach ($colors as $color): ?>
            <label class="color-circle-wrapper">
              <input type="checkbox" name="colors[]" value="<?= $color['color_id'] ?>"
                <?= in_array($color['color_id'], $product_colors) ? 'checked' : '' ?> disabled>
              <span class="color-circle" style="background-color: <?= htmlspecialchars($color['color_name']) ?>"></span>
              <?= htmlspecialchars($color['color_name']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="form-group">
        <label>Features:</label>
        <div class="checkbox-group checkbox-default">
          <?php foreach ($features as $feat): ?>
            <label>
              <input type="checkbox" name="features[]" value="<?= $feat['feature_id'] ?>"
                <?= in_array($feat['feature_id'], $product_features) ? 'checked' : '' ?> disabled>
              <?= htmlspecialchars($feat['feature_name']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="form-group">
        <label>Images:</label>
        <div class="image-gallery" id="imageGallery">
          <?php foreach ($images as $img): ?>
            <div class="image-card" data-image-id="<?= $img['image_id'] ?>">
              <span class="delete-icon" style="display:none;">&times;</span>
              <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Product Image">
            </div>
          <?php endforeach; ?>

          <div class="image-card add-new-image" id="addImageBox" style="display:none;">
            <!-- 不要再用 label for -->
            <div class="plus-icon" id="triggerImageInput">+</div>
            <input type="file" name="images[]" id="newImageInput" multiple style="display:none;">

          </div>
        </div>

        <div class="btn-group">
          <button type="button" id="editButton" class="btn-update">Edit Product</button>
          <button type="submit" id="submitButton" class="submitButton" style="display:none;">Submit</button>
        </div>
        <div class="btn-group">
          <button type="button" id="cancelButton" class="cancelButton" style="display:none;">Cancel</button>
          <a href="admin.php?section=product" class="btn-exit">Exit</a>
        </div>

    </form>
  </div>

</body>

</html>