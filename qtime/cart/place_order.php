<?php
require '../base.php';
header('Content-Type: application/json');

// ✅ 显示错误方便调试
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 1️⃣ 获取用户购物车 ID
    $stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_id = $stmt->fetchColumn();

    if (!$cart_id) {
        echo json_encode(["success" => false, "message" => "Cart not found."]);
        exit;
    }

    // 2️⃣ 获取购物车商品
    $stmt = $db->prepare("
        SELECT ci.product_id, ci.quantity, p.price, p.stock
        FROM cartitems ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        echo json_encode(["success" => false, "message" => "Your cart is empty."]);
        exit;
    }

    // 3️⃣ 检查库存
    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock']) {
            echo json_encode([
                "success" => false,
                "message" => "Not enough stock for product ID " . $item['product_id']
            ]);
            exit;
        }
    }

    // ✅ 先计算总价
    $total_price = 0;
    foreach ($items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // 4️⃣ 开始事务
    $db->beginTransaction();

    // 5️⃣ 创建订单并写入总价
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $total_price]);
    $order_id = $db->lastInsertId();

    // 6️⃣ 写入 orderitems 并更新库存
    $insertItem = $db->prepare("
        INSERT INTO orderitems (order_id, product_id, quantity, price, status)
        VALUES (?, ?, ?, ?, 'paid')
    ");
    $updateStock = $db->prepare("
        UPDATE products SET stock = stock - ? WHERE product_id = ?
    ");

    foreach ($items as $item) {
        $insertItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        $updateStock->execute([$item['quantity'], $item['product_id']]);
    }

    // 7️⃣ 清空购物车
    $stmt = $db->prepare("DELETE FROM cartitems WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    // ✅ 提交事务
    $db->commit();

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
