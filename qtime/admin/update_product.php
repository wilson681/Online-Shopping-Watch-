<?php
require("../auth.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$product_id = $_POST['product_id'] ?? null;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$colors = $_POST['colors'] ?? [];
$features = $_POST['features'] ?? [];

$errors = [];
if (!$product_id) $errors[] = "Product ID missing.";
if (empty($name)) $errors[] = "Product name is required.";
if (!is_numeric($price) || $price <= 0) $errors[] = "Price must be a positive number.";
if (!is_numeric($stock) || $stock < 0) $errors[] = "Stock must be a non-negative number.";

if ($errors) {
    echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
    exit;
}

$stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE product_id = ?");
$stmt->execute([$name, $description, $price, $stock, $product_id]);

$db->prepare("DELETE FROM product_colours WHERE product_id = ?")->execute([$product_id]);
$color_stmt = $db->prepare("INSERT INTO product_colours (product_id, color_id) VALUES (?, ?)");
foreach ($colors as $color_id) {
    $color_stmt->execute([$product_id, $color_id]);
}

$db->prepare("DELETE FROM productfeatures WHERE product_id = ?")->execute([$product_id]);
$feature_stmt = $db->prepare("INSERT INTO productfeatures (product_id, feature_id) VALUES (?, ?)");
foreach ($features as $feature_id) {
    $feature_stmt->execute([$product_id, $feature_id]);
}

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/qtime/image/';
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024;

$currentCountStmt = $db->prepare("SELECT COUNT(*) FROM productimages WHERE product_id = ?");
$currentCountStmt->execute([$product_id]);
$imageIndex = $currentCountStmt->fetchColumn();

if (!empty($_FILES['images']['name'][0])) {
    
    $img_stmt = $db->prepare("INSERT INTO productimages (product_id, image_url) VALUES (?, ?)");

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_path) {
        if (is_uploaded_file($tmp_path)) {
            $file_type = strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
            if (!in_array($file_type, $allowed_types)) continue;
            if ($_FILES['images']['size'][$key] > $maxFileSize) continue;

            $imageIndex++;
            $filename = $product_id . '_' . $imageIndex . '.' . $file_type;
            $target_path = $upload_dir . $filename;
            $relative_path = '../image/' . $filename;

            if (move_uploaded_file($tmp_path, $target_path)) {
                $img_stmt->execute([$product_id, $relative_path]);
            }
        }
    }
}

echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
exit;
