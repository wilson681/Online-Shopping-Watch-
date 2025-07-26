<?php
require '../base.php'; // ✅ 只加载数据库连接

header("Content-Type: application/json"); // ✅ 确保返回 JSON 数据
$response = ["success" => false, "message" => ""];

// ✅ 确保用户已登录
if (!isset($_SESSION["user_id"])) {
    $response["message"] = "User not logged in!";
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION["user_id"];
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$dob = trim($_POST["dob"] ?? "");

// ✅ **检查哪些字段为空**
$missing_fields = [];
if (empty($name)) $missing_fields[] = "Name";
if (empty($email)) $missing_fields[] = "Email";
if (empty($phone)) $missing_fields[] = "Phone";

if (!empty($missing_fields)) {
    $response["message"] = "⚠️ Please fill in: " . implode(", ", $missing_fields);
    echo json_encode($response);
    exit();
}

// ✅ **验证 Email 格式**
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response["message"] = "❌ Invalid email format!";
    echo json_encode($response);
    exit();
}

// ✅ **验证手机号格式（仅允许 10-11 位数字）**
if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    $response["message"] = "❌ Invalid phone number! Must be 10-11 digits.";
    echo json_encode($response);
    exit();
}

// ✅ **检查 Email 是否已存在（防止重复 Email）**
$stmt = $db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->execute([$email, $user_id]);
if ($stmt->fetch()) {
    $response["message"] = "❌ Email is already in use!";
    echo json_encode($response);
    exit();
}

// ✅ **更新数据库**
$stmt = $db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, dob = ? WHERE user_id = ?");
if ($stmt->execute([$name, $email, $phone, $dob, $user_id])) {
    $response["success"] = true;
    $response["message"] = "✅ Profile updated successfully!";
} else {
    $response["message"] = "❌ Failed to update profile!";
}

echo json_encode($response);
exit();
?>
