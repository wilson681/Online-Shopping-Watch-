<?php
require '../base.php';
header('Content-Type: application/json');

// ✅ 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must log in first."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 获取传来的 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? 0;

if ($product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid product ID."]);
    exit;
}

// ✅ 获取当前用户的 cart_id
$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    echo json_encode(["success" => false, "message" => "Cart not found."]);
    exit;
}

// ✅ 从 cartitems 中删除该商品
$stmt = $db->prepare("DELETE FROM cartitems WHERE cart_id = ? AND product_id = ?");
$success = $stmt->execute([$cart_id, $product_id]);

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove item."]);
}
exit;
