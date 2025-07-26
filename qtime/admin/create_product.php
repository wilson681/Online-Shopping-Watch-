<?php
require("../auth.php");

// Fetch brands and categories for dropdown
$brands = $db->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);
$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$colors = $db->query("SELECT * FROM colors")->fetchAll(PDO::FETCH_ASSOC);
$features = $db->query("SELECT * FROM features")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Product</title>
  <link rel="stylesheet" href="../css/create_product.css">
  <script src="create_product_process.js" defer></script>

</head>
<body>
<div class="prod-wrapper">
  <h2>Create New Product</h2>
  <div id="popupMessage" class="popup-message" style="display:none;"></div>

  <form action="create_product_process.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Product ID:</label>
      <input type="text" name="ID" required>
    </div>

    <div class="form-group">
      <label>Product Name:</label>
      <input type="text" name="name" required>
    </div>

    <div class="form-group">
      <label>Description:</label>
      <textarea name="description" required></textarea>
    </div>

    <div class="form-group">
      <label>Price:</label>
      <input type="number" step="0.01" name="price" required>
    </div>

    <div class="form-group">
      <label>Stock:</label>
      <input type="number" name="stock" required>
    </div>

    <div class="form-group">
      <label>Brand:</label>
      <select name="brand_id" required>
        <?php foreach ($brands as $b): ?>
          <option value="<?= $b['brand_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Category:</label>
      <select name="category_id" required>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Colors:</label>
      <div class="checkbox-group">
        <?php foreach ($colors as $color): ?>
          <label class="color-circle-wrapper">
            <input type="checkbox" name="colors[]" value="<?= $color['color_id'] ?>">
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
            <input type="checkbox" name="features[]" value="<?= $feat['feature_id'] ?>">
            <?= htmlspecialchars($feat['feature_name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="form-group">
      <label>Images:</label>
      <div class="image-gallery" id="imageGallery">
        <div class="image-card add-new-image" id="addImageBox">
          <div class="plus-icon" id="triggerImageInput">+</div>
        </div>
        <input type="file" name="images[]" id="newImageInput" multiple style="display:none;"> <!-- image[] is a array -->
      </div>
    </div>

    <div class="btn-group">
      <button type="submit" class="submitButton">Create Product</button>
        </div>
    <div class="btn-group">
      <a href="admin.php?section=product" class="btn-exit">Exit</a>
    </div>
  </form>
</div>
</body>
</html>
