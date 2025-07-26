<?php
require '../auth.php'; // ✅ 确保数据库连接正确

// ✅ 检查用户是否登录
if (!isset($_SESSION["user_id"])) {
    exit(json_encode(["success" => false, "message" => "❌ User not logged in!"]));
}

$user_id = $_SESSION["user_id"];
$new_address = trim($_POST["address"] ?? "");
$new_postcode = trim($_POST["postcode"] ?? "");
$new_state = trim($_POST["state"] ?? "");

// ✅ 验证必填字段
if (!$new_address || !$new_postcode || !$new_state) {
    exit(json_encode(["success" => false, "message" => "⚠️ Please fill in all fields!"]));
}

// ✅ 执行数据库更新
$stmt = $db->prepare("UPDATE users SET address = :address, postcode = :postcode, state = :state WHERE user_id = :user_id");
$stmt->bindParam(":address", $new_address);
$stmt->bindParam(":postcode", $new_postcode);
$stmt->bindParam(":state", $new_state);
$stmt->bindParam(":user_id", $user_id);
$success = $stmt->execute();

// ✅ 返回 JSON 结果
exit(json_encode([
    "success" => $success,
    "message" => $success ? "✅ Address updated successfully!" : "❌ Failed to update address!"
]));
