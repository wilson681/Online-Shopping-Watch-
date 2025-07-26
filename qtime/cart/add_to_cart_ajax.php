<?php
require '../base.php';

// 设置返回类型为 JSON
header('Content-Type: application/json');

// ✅ 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must log in first."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 获取前端传来的 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? 0;
$quantity = $data['quantity'] ?? 0;

// ✅ 验证数据合法性
if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid product or quantity."]);
    exit;
}

// ✅ 获取产品库存
$stmt = $db->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(["success" => false, "message" => "Product not found."]);
    exit;
}

if ($quantity > $product['stock']) {
    echo json_encode(["success" => false, "message" => "Stock not available."]);
    exit;
}

// ✅ 确保用户有购物车（1 个用户只能有 1 个购物车）
$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    // 如果没有，就创建一个
    $stmt = $db->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
    $stmt->execute([$user_id]);
    $cart_id = $db->lastInsertId();
}

// ✅ 检查该产品是否已经在购物车中
$stmt = $db->prepare("SELECT quantity FROM cartitems WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$cart_id, $product_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // ✅ 如果已有，就更新数量
    $newQty = $existing['quantity'] + $quantity;
    $stmt = $db->prepare("UPDATE cartitems SET quantity = ? WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$newQty, $cart_id, $product_id]);
} else {
    // ✅ 否则插入新的项目
    $stmt = $db->prepare("INSERT INTO cartitems (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity]);
}

echo json_encode(["success" => true, "message" => "Item added to cart."]);
exit;
