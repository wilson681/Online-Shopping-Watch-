<?php
session_start();
require '../head.php'; // 确保已连接数据库 $db

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 获取数据库中的哈希密码
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (!$stmt->num_rows) {
        $message = "⚠️ 用户不存在";
    } elseif (!password_verify($old_password, $hashed_password)) {
        $message = "❌ 旧密码错误";
    } elseif ($new_password !== $confirm_password) {
        $message = "⚠️ 两次输入的新密码不匹配";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $message = "❌ 密码需至少8字符，包含大小写、数字和特殊字符";
    } else {
        // 更新数据库中的密码
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            $message = "✅ 密码更改成功！";
            
            // **确保同步更新 phpMyAdmin 数据库**
            if ($db->affected_rows > 0) {
                $message .= " 数据已同步至数据库.";
            } else {
                $message .= " ⚠️ 但数据库更新失败，请重试.";
            }
        } else {
            $message = "❌ 更新失败，请重试";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更改密码</title>
    <link rel="stylesheet" href="../css/password.css">
</head>
<body>
    <div class="container">
        <h2>更改密码</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        
        <form method="POST" action="password.php">
            <label for="old_password">旧密码:</label>
            <div class="password-box">
                <input type="password" id="old_password" name="old_password" required>
                <span onclick="togglePassword('old_password', this)">👁️</span>
            </div>

            <label for="new_password">新密码:</label>
            <div class="password-box">
                <input type="password" id="new_password" name="new_password" required onkeyup="validatePasswordStrength()">
                <span onclick="togglePassword('new_password', this)">👁️</span>
            </div>
            <p id="password-strength"></p>

            <label for="confirm_password">确认新密码:</label>
            <div class="password-box">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span onclick="togglePassword('confirm_password', this)">👁️</span>
            </div>

            <button type="submit">更改密码</button>
        </form>
    </div>

    <script src="password.js"></script>
</body>
</html>
