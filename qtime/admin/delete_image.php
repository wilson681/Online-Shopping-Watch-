<?php
require("../auth.php");

$image_id = $_GET['image_id'] ?? null;
$product_id = $_GET['product_id'] ?? null;

if (!$image_id || !$product_id) {
    echo "Invalid image or product.";
    exit;
}

// get image url
$stmt = $db->prepare("SELECT image_url FROM productimages WHERE image_id = ?");
$stmt->execute([$image_id]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    echo "Image not found.";
    exit;
}

//delete image file
$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/qtime/' . $image['image_url'];
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// delete image from database
$stmt = $db->prepare("DELETE FROM productimages WHERE image_id = ?");
$stmt->execute([$image_id]);

// location back to product management page
header("Location: prodmanagement.php?product_id=" . urlencode($product_id));
exit;
