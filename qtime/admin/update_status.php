<?php
include '../base.php'; 

header('Content-Type: application/json'); // set response type to json

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_status') {
    $order_id = $_GET['order_id'] ?? null;
    $status_updates = $_POST['status'] ?? [];
    $admin_id = $_SESSION['user_id'] ?? null;

    if (!$admin_id) {
        echo json_encode(['status' => 'error', 'message' => 'Admin session expired, please re-login.']);
        exit;
    }
    if (!$order_id) {
        echo json_encode(['status' => 'error', 'message' => 'Missing order ID.']);
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE orderitems SET status = ? WHERE order_id = ? AND product_id = ?");
        $oldStmt = $db->prepare("SELECT status FROM orderitems WHERE order_id = ? AND product_id = ?");
        $logStmt = $db->prepare("INSERT INTO order_status_logs (admin_id, order_id, product_id, old_status, new_status, changed_at) VALUES (?, ?, ?, ?, ?, NOW())");

        foreach ($status_updates as $product_id => $new_status) {
            $oldStmt->execute([$order_id, $product_id]);
            $old_status = $oldStmt->fetchColumn();

            if ($old_status !== $new_status) {
                $stmt->execute([$new_status, $order_id, $product_id]);
                $logStmt->execute([$admin_id, $order_id, $product_id, $old_status, $new_status]);
            }
        }

        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'Order status updated successfully.']);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
