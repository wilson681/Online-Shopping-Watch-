<?php
require '../head.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login/login.php");
    exit();
}

$selected_order_id = $_GET['order_id'] ?? null;
$search_term = $_GET['search'] ?? '';
$active_status_filter = $_GET['status'] ?? 'all'; // Get the active status filter

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_item'])) {
    $order_id_to_cancel = $_POST['order_id'];
    $product_id_to_cancel = $_POST['product_id'];
    $cancel_reason = $_POST['cancel_reason'];
    $quantity_to_cancel = 0;

    if (empty($cancel_reason)) {
        $error_message = "Please select a cancellation reason.";
    } else {
        // 获取要取消的订单项信息
        $stmt_select = $db->prepare("SELECT quantity FROM orderitems WHERE order_id = :order_id AND product_id = :product_id AND status IN ('paid', 'shipping')");
        $stmt_select->execute([':order_id' => $order_id_to_cancel, ':product_id' => $product_id_to_cancel]);
        $order_item_to_cancel = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($order_item_to_cancel) {
            $quantity_to_cancel = $order_item_to_cancel['quantity'];

            // 更新 orderitems 表的状态为 'refund'
            $stmt_update_orderitem = $db->prepare("UPDATE orderitems SET status = 'refund' WHERE order_id = :order_id AND product_id = :product_id");
            $stmt_update_orderitem->execute([':order_id' => $order_id_to_cancel, ':product_id' => $product_id_to_cancel]);

            // 更新 products 表的库存
            $stmt_update_product = $db->prepare("UPDATE products SET stock = stock + :quantity WHERE product_id = :product_id");
            $stmt_update_product->execute([':quantity' => $quantity_to_cancel, ':product_id' => $product_id_to_cancel]);

            $success_message = "Item cancelled successfully.";
        } else {
            $error_message = "Could not find the order item to cancel or it's not in a cancellable status.";
        }
    }
}

if ($selected_order_id) {
    // ============== 详情页逻辑 ==============
    $stmt = $db->prepare("
        SELECT
            o.order_id,
            o.created_at,
            oi.product_id,
            oi.quantity,
            oi.price,
            oi.status AS item_status,
            p.name AS product_name,
            img.image_url,
            u.name AS user_name,
            u.phone,
            u.address,
            u.postcode,
            u.state
        FROM orders o
        JOIN orderitems oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        LEFT JOIN (
            SELECT product_id, MIN(image_url) AS image_url
            FROM productimages
            GROUP BY product_id
        ) img ON p.product_id = img.product_id
        JOIN users u ON o.user_id = u.user_id
        WHERE o.user_id = :user_id
        AND o.order_id = :order_id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'order_id' => $selected_order_id
    ]);
    $items = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (empty($items)) {
        header("Location: orders.php");
        exit();
    }

    // 按状态分组
    $status_groups = [
        'paid' => [],
        'shipping' => [],
        'refund' => []
    ];

    foreach ($items as $item) {
        $status = strtolower($item->item_status);
        if (isset($status_groups[$status])) {
            $status_groups[$status][] = $item;
        }
    }

} else {
    // ============== 列表页逻辑 ==============
    $stmt = $db->prepare("
        SELECT
            o.order_id,
            o.created_at,
            SUM(oi.quantity * oi.price) AS total_amount,
            COUNT(oi.product_id) AS item_count
        FROM orders o
        JOIN orderitems oi ON o.order_id = oi.order_id
        WHERE o.user_id = :user_id
        AND o.order_id LIKE :search
        GROUP BY o.order_id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'search' => "%$search_term%"
    ]);
    $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $selected_order_id ? "Order #$selected_order_id" : 'Orders' ?> | QTime</title>
    <link rel="stylesheet" href="../css/orders.css">
</head>
<body>
    <div class="page-container">
        <div class="order-wrapper">
            <?php if($selected_order_id): ?>
                <div class="detail-header">
                    <a href="orders.php" class="back-btn">← Back to Orders</a>
                    <h2>Order #<?= $selected_order_id ?></h2>
                    <div class="order-meta">
                        <p>Order Date: <?= date("Y-m-d H:i", strtotime($items[0]->created_at)) ?></p>
                        <p>Name: <?= htmlspecialchars($items[0]->user_name) ?></p>
                        <p>Contact: <?= htmlspecialchars($items[0]->phone) ?></p>
                        <p>Address: <?= htmlspecialchars($items[0]->address) ?>,
                            <?= htmlspecialchars($items[0]->postcode) ?>
                            <?= htmlspecialchars($items[0]->state) ?></p>
                    </div>
                </div>

                <div class="status-filter">
                    <a href="?order_id=<?= $selected_order_id ?>&status=all" class="status-btn <?= $active_status_filter === 'all' ? 'active' : '' ?>" data-status="all">All</a>
                    <a href="?order_id=<?= $selected_order_id ?>&status=paid" class="status-btn <?= $active_status_filter === 'paid' ? 'active' : '' ?>" data-status="paid">Paid</a>
                    <a href="?order_id=<?= $selected_order_id ?>&status=shipping" class="status-btn <?= $active_status_filter === 'shipping' ? 'active' : '' ?>" data-status="shipping">Shipping</a>
                    <a href="?order_id=<?= $selected_order_id ?>&status=refund" class="status-btn <?= $active_status_filter === 'refund' ? 'active' : '' ?>" data-status="refund">Refund</a>
                </div>

                <div class="item-list">
                    <?php foreach ($status_groups as $status => $items): ?>
                        <?php foreach ($items as $item): ?>
                            <?php if ($active_status_filter === 'all' || $active_status_filter === strtolower($item->item_status)): ?>
                                <div class="order-item" data-status="<?= strtolower($item->item_status) ?>">
                                    <div class="product-info">
                                        <img src="<?= htmlspecialchars($item->image_url ?? 'default.jpg') ?>"
                                            alt="<?= htmlspecialchars($item->product_name) ?>">
                                        <div class="product-details">
                                            <h4><?= htmlspecialchars($item->product_name) ?></h4>
                                            <div class="meta-info">
                                                <span>Quantity: <?= $item->quantity ?></span>
                                                <span>Price: RM <?= number_format($item->price, 2) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if(in_array(strtolower($item->item_status), ['shipping', 'paid'])): ?>
                                        <div class="action-section">
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?= $selected_order_id ?>">
                                                <input type="hidden" name="product_id" value="<?= $item->product_id ?>">
                                                <select class="cancel-reason" name="cancel_reason">
                                                    <option value="">Select reason</option>
                                                    <option value="change_mind">Changed mind</option>
                                                    <option value="wrong_item">Wrong item</option>
                                                    <option value="delay">Shipping delay</option>
                                                </select>
                                                <button type="button" class="cancel-btn"
                                                    data-order-id="<?= $selected_order_id ?>"
                                                    data-product-id="<?= $item->product_id ?>">
                                                    Cancel
                                                </button>
                                                <button type="submit" id="confirm-cancel-btn-<?= $item->product_id ?>"
                                                    name="cancel_order_item" style="display: none;">Confirm Cancel</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="list-header">
                    <h2>Order History</h2>
                    <form class="search-form" method="GET">
                        <input type="text" name="search"
                            placeholder="Search Order ID..."
                            value="<?= htmlspecialchars($search_term) ?>">
                        <button type="submit">🔍 Search</button>
                    </form>
                </div>

                <div class="order-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-id">#<?= $order->order_id ?></div>
                            <div class="order-info">
                                <p><?= date("Y-m-d H:i", strtotime($order->created_at)) ?></p>
                                <p><?= $order->item_count ?> items</p>
                            </div>
                            <div class="order-actions">
                                <p class="total">RM <?= number_format($order->total_amount, 2) ?></p>
                                <a href="orders.php?order_id=<?= $order->order_id ?>"
                                    class="view-btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="notification-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3 id="notification-title"></h3>
            <p id="notification-message"></p>
            <button id="notification-close-btn">OK</button>
        </div>
    </div>

    <div id="confirmation-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Confirm Cancellation</h3>
            <p>Are you sure you want to cancel this item?</p>
            <div class="modal-buttons">
                <button id="confirm-cancel-btn" class="confirm-btn">Confirm</button>
                <button id="cancel-cancel-btn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>


     <script src="orders.js"></script>
</body>
</html>