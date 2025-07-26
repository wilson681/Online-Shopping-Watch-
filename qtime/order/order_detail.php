<?php
require 'head.php';

// 获取 order_id 和 product_id
$order_id = $_GET['order_id'] ?? null;
$product_id = $_GET['product_id'] ?? null;

if (!$order_id || !$product_id) {
    header("Location: orders.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        o.order_id,
        o.created_at,
        u.address,
        u.postcode,
        u.state,
        oi.quantity,
        oi.price,
        oi.status AS item_status,
        p.name AS product_name,
        img.image_url
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    JOIN orderitems oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN productimages img ON p.product_id = img.product_id
    WHERE o.user_id = :user_id AND o.order_id = :order_id AND oi.product_id = :product_id
";

$stmt = $db->prepare($sql);
$stmt->execute([
    'user_id' => $user_id,
    'order_id' => $order_id,
    'product_id' => $product_id
]);

$item = $stmt->fetch(PDO::FETCH_OBJ);

if (!$item) {
    echo "<p style='padding: 50px;'>❌ Product not found in this order.</p>";
    exit();
}

// 整合信息
$created_at = $item->created_at;
$status = $item->item_status;
$address = "{$item->address}, {$item->postcode}, {$item->state}";
$subtotal = $item->quantity * $item->price;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= htmlspecialchars($order_id) ?> | QTime</title>
    <link rel="stylesheet" href="css/orders.css">
</head>
<body>
<div class="page-container">
    <div class="order-wrapper">
        <h2>Product Detail</h2>

        <!-- 🔍 顶部 Order 信息 -->
        <div class="order-top">
            <p><strong>Order ID:</strong> #<?= htmlspecialchars($order_id) ?></p>
            <p><strong>Date:</strong> <?= date("Y-m-d", strtotime($created_at)) ?></p>
            <p><strong>Status:</strong> <span class="status <?= htmlspecialchars($status) ?>"><?= ucfirst($status) ?></span></p>
            <p><strong>Shipping Address:</strong> <?= htmlspecialchars($address) ?></p>
        </div>

        <hr style="margin: 20px 0;">

        <!-- 📦 单一产品详情 -->
        <div class="order-row">
            <div class="product-info">
                <img src="<?= htmlspecialchars($item->image_url ?? 'default.jpg') ?>" alt="Product">
                <div class="product-details">
                    <p class="product-name"><?= htmlspecialchars($item->product_name) ?></p>
                    <p>Quantity: <?= $item->quantity ?></p>
                </div>
            </div>
            <div class="order-meta">
                <p class="price">RM <?= number_format($item->price, 2) ?></p>
                <p>Subtotal: RM <?= number_format($subtotal, 2) ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'foot.php'; ?>
</body>
</html>
