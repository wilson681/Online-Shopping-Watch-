<?php
require("../auth.php");
//set rersponse type to json
header('Content-Type: application/json');
//only accept post method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
/// Get product details from POST request and remove any empty spaces or set to null if not set and set to 0 if not set
$product_id = trim($_POST['ID'] ?? '');
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$brand_id = $_POST['brand_id'] ?? null;
$category_id = $_POST['category_id'] ?? null;
$colors = $_POST['colors'] ?? [];
$features = $_POST['features'] ?? [];
$errors = [];
if (empty($product_id)) $errors[] = "Product ID is required.";
if (empty($name)) $errors[] = "Product name is required.";
if (!is_numeric($price) || $price <= 0) $errors[] = "Price must be a positive number.";
if (!is_numeric($stock) || $stock < 0) $errors[] = "Stock must be non-negative.";
if (!$brand_id) $errors[] = "Brand is required.";
if (!$category_id) $errors[] = "Category is required.";
// database is int type avoid auto increment insert to database when this product id is a text
if (!ctype_digit($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID must be a number.']);
    exit; //here avoid
}

if ($errors) {
    echo json_encode([
        'status' => 'error',
        'message' => $errors[0] 
    ]);
    exit;
}
//check is product id already exists?
$check_stmt = $db->prepare("SELECT product_id FROM products WHERE product_id = ?");
$check_stmt->execute([$product_id]);
if ($check_stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID already exists.']);
    exit;
}

$stmt = $db->prepare("INSERT INTO products (product_id, name, description, price, stock, brand_id, category_id)
                      VALUES (?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$product_id, $name, $description, $price, $stock, $brand_id, $category_id]);
//execute to the ?
//for debug
if (!$success) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to insert product.']);
    exit;
}
//  Insert product features and colors into their respective tables
if (!empty($colors)) {
    $color_stmt = $db->prepare("INSERT INTO product_colours (product_id, color_id) VALUES (?, ?)");
    foreach ($colors as $color_id) {
        $color_stmt->execute([$product_id, $color_id]);
    }
}

if (!empty($features)) {
    $feature_stmt = $db->prepare("INSERT INTO productfeatures (product_id, feature_id) VALUES (?, ?)");
    foreach ($features as $feature_id) {
        $feature_stmt->execute([$product_id, $feature_id]);
    }
}

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/qtime/image/';
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024;

if (!empty($_FILES['images']['name'][0])) {
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true); // if no directory, create it

    $img_stmt = $db->prepare("INSERT INTO productimages (product_id, image_url) VALUES (?, ?)");

    $imageIndex = 0; // set the image index to 0 for first image
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_path) {
        if (is_uploaded_file($tmp_path)) {
            $file_type = strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
            if (!in_array($file_type, $allowed_types)) continue;
            if ($_FILES['images']['size'][$key] > $maxFileSize) continue;

            $imageIndex++; //image index +1 for next image
            $filename = $product_id . '_' . $imageIndex . '.' . $file_type;
            $target_path = $upload_dir . $filename;
            $relative_path = '../image/' . $filename;

            if (move_uploaded_file($tmp_path, $target_path)) {
                $img_stmt->execute([$product_id, $relative_path]);
            }
        }
    }
}

echo json_encode(['status' => 'success', 'message' => 'Product created successfully.']);
exit;
