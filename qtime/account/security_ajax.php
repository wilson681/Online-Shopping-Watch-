<?php
require '../auth.php';

header("Content-Type: application/json");

// 确保用户已登录
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION["user_id"];

// 获取用户当前密码哈希
$stmt = $db->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$current_password_hash = $stmt->fetchColumn();

$response = ["success" => false, "message" => ""];
$action = $_POST["action"] ?? "";

if ($action == "verify_password") {
    $password = $_POST["password"] ?? "";
    $response["success"] = password_verify($password, $current_password_hash);
}

else if ($action == "update_password") {
    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";

    // 检查当前密码是否正确
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $stored_hash = $stmt->fetchColumn();

    if (!password_verify($current_password, $stored_hash)) {
        $response["message"] = "❌ Current password is incorrect!";
    } elseif ($current_password == $new_password) {
        $response["message"] = "❌ New password cannot be the same as current password!";
    } else {
        // 更新新密码
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        if ($updateStmt->execute([$new_hash, $user_id])) {
            $response["success"] = true;
        } else {
            $response["message"] = "❌ Failed to update password!";
        }
    }

    echo json_encode($response);
    exit();
}


else if ($action == "update_security_question") {
    $new_question = $_POST["security_question"] ?? "";
    $new_answer = $_POST["security_answer"] ?? "";

    if (empty($new_question) || empty($new_answer)) {
        $response["message"] = "⚠️ Security question & answer can't be empty!";
    } else {
        $stmt = $db->prepare("UPDATE users SET security_question = ?, security_answer = ? WHERE user_id = ?");
        if ($stmt->execute([$new_question, $new_answer, $user_id])) {
            $response["success"] = true;
        } else {
            $response["message"] = "❌ Unable to update security question!";
        }
    }
}

else {
    $response["message"] = "Invalid action!";
}

echo json_encode($response);
exit();
