<?php
include '../head.php';


// ✅ 用户已登录就跳转（放最上面）
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: ../admin/admin.php");
    } else {
        header("Location: ../account/account.php");
    }
    exit();
}


// 统一错误消息，避免信息泄露
$error_message = "";

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // 查询用户信息
    $stmt = $db->prepare("SELECT user_id, password_hash, role FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(); // ✅ 使用 FETCH_OBJ

    // 🔥 统一错误信息（防止撞库）
    if ($user && password_verify($password, $user->password_hash)) {
        $_SESSION["user_id"] = $user->user_id;
        $_SESSION["role"] = $user->role;

        // ✅ 登录成功后跳转
        if ($user->role === "admin") {
            redirect("../admin/admin.php");
        } else { 
            redirect("../homepage/home.php");
        }
        exit(); // 🔥 确保 PHP 停止执行，防止意外输出
    } else {
        // ❌ 登录失败（密码错误或 email 不存在）
        $error_message = "Invalid email or password!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | QTime</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="login.js" defer></script>
</head>

<body>
<div class="page-container">
    <div class="login-wrapper">
        <!-- 📸 左侧图片 -->
        <div class="login-image">
            <img src="../image/login.jpg" alt="Brand Ambassador">
        </div>

        <!-- 🔐 右侧登录表单 -->
        <div class="login-container">
            <h2>Welcome Back to QTime</h2>

            <!-- 显示错误信息 -->
            <?php if (!empty($error_message)) : ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label>Password <span>*</span></label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                        <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
                    </div>
                </div>

                <!-- 🔗 忘记密码 -->
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot your password?</a>
                </div>

                <!-- 🔘 登录按钮 -->
                <button type="submit" class="login-btn">LOGIN</button>

                <!-- 🔄 注册新账号 -->
                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
    </div>

    <!-- ✅ 传递 PHP 错误信息给 JS -->
    <script>
        const errorMessage = "<?php echo $error_message; ?>";
    </script>


<?php include '../foot.php'; ?>
</body>

</html>