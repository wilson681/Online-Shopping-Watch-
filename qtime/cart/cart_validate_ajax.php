<?php
require '../base.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["valid" => false, "message" => "You must log in first."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// 获取该用户的购物车 ID
$stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    echo json_encode(["valid" => false, "message" => "Your cart is empty."]);
    exit;
}

// 获取购物车中的商品数量和产品库存
$stmt = $db->prepare("
    SELECT p.name, ci.quantity, p.stock
    FROM cartitems ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 检查库存是否充足
foreach ($items as $item) {
    if ($item['quantity'] > $item['stock']) {
        $name = htmlspecialchars($item['name']);
        $stock = (int)$item['stock'];
        $message = "$name only has $stock item(s) left. Please reduce quantity or remove it.";
        echo json_encode(["valid" => false, "message" => $message]);
        exit;
    }
}

// 全部库存都足够
echo json_encode(["valid" => true]);
exit;
