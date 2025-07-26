<?php
require '../base.php';
header('Content-Type: application/json');

// ✅ 验证登录状态
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must log in first."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 获取 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? 0;
$quantity = $data['quantity'] ?? 0;

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// ✅ 获取 cart_id
$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    echo json_encode(["success" => false, "message" => "Cart not found."]);
    exit;
}

// ✅ 检查库存是否足够（可选）
$stmt = $db->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$stock = $stmt->fetchColumn();

if ($quantity > $stock) {
    echo json_encode(["success" => false, "message" => "Exceeds available stock."]);
    exit;
}

// ✅ 更新数量
$stmt = $db->prepare("UPDATE cartitems SET quantity = ? WHERE cart_id = ? AND product_id = ?");
$success = $stmt->execute([$quantity, $cart_id, $product_id]);

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed."]);
}
exit;
