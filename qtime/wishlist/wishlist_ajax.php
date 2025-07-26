<?php
require '../base.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = (int)($data['user_id'] ?? 0);
    $product_id = (int)($data['product_id'] ?? 0);
    $action = $data['action'] ?? "add";

    if ($user_id <= 0 || $product_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
        exit;
    }

    if ($action === "add") {
        $stmt = $db->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(["success" => true]);
    } elseif ($action === "remove") {
        $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Unknown action"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error"]);
}
