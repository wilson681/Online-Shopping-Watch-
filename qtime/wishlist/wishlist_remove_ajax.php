<?php
require '../base.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = (int)($_SESSION['user_id'] ?? 0);
$product_id = (int)($data['product_id'] ?? 0);

if ($user_id <= 0 || $product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$success = $stmt->execute([$user_id, $product_id]);

echo json_encode(["success" => $success]);
