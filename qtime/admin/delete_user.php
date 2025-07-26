<?php
require("../auth.php");
if (!isset($_GET['user_id'])) {
    echo json_encode(["success" => false, "error" => "User ID not provided"]);
    exit;
}
$user_id = intval($_GET['user_id']);

try {
    $db->beginTransaction();
    //get user orders
    $stmt = $db->prepare("SELECT order_id FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $orderIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($orderIds)) {
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        // delete order items
        $deleteOrderItems = $db->prepare("DELETE FROM orderitems WHERE order_id IN ($placeholders)");
        $deleteOrderItems->execute($orderIds);

        // delete order_status_logs
        $deleteLogs = $db->prepare("DELETE FROM order_status_logs WHERE order_id IN ($placeholders)");
        $deleteLogs->execute($orderIds);

        // delete orders
        $deleteOrders = $db->prepare("DELETE FROM orders WHERE order_id IN ($placeholders)");
        $deleteOrders->execute($orderIds);
    }

    // delete carts
    $deleteCarts = $db->prepare("DELETE FROM carts WHERE user_id = ?");
    $deleteCarts->execute([$user_id]);

    // delete wishlist
    $deleteWishlist = $db->prepare("DELETE FROM wishlist WHERE user_id = ?");
    $deleteWishlist->execute([$user_id]);

    // delete user
    $deleteUser = $db->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteUser->execute([$user_id]);

    if ($deleteUser->rowCount() > 0) {
        $db->commit();
        echo json_encode(["success" => true]);
    } else {
        $db->rollBack();
        echo json_encode(["success" => false, "error" => "User not found or already deleted"]);
    }
} catch (PDOException $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
