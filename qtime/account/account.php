<?php
require '../head.php'; // ✅ 确保加载数据库 & 认证

// ✅ 确保用户已登录
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// ✅ 获取用户信息
$stmt = $db->prepare("SELECT name, email, phone, dob, profile_picture FROM users WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ 如果用户信息为空，重定向到登录页
if (!$user) {
    header("Location: ../login.php");
    exit();
}

// ✅ 头像路径（默认头像）
$profile_picture = !empty($user["profile_picture"]) ? $user["profile_picture"] : "images/default_profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="../css/account.css">
    <script src="account.js" defer></script>
</head>

<body>
    <div class="page-container">
        <div class="account-container">

            <!-- 🌟 左侧导航栏 -->
            <aside class="account-sidebar">
                <!-- 🌟 头像部分 -->
                <!-- 🌟 用户头像 -->
                <div class="profile-section">
                    <img id="profileImage" src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
                    <input type="file" id="profileUpload" hidden>
                    <button onclick="document.getElementById('profileUpload').click()" class="edit-profile-btn">Change</button>
                </div>


                <ul>
                    <li class="active"><a href="#">Account Details</a></li>
                    <li><a href="address.php">Delivery Address</a></li>
                    <li><a href="security.php">Security</a></li>
                </ul>
            </aside>

            <!-- 🌟 右侧内容区 -->
            <section class="account-content">
                <h2>Account Details</h2>
                <form class="account-info" id="accountForm">

                    <!-- Name -->
                    <div class="input-group">
                        <label>Name:</label>
                        <input type="text" id="name" value="<?= htmlspecialchars($user['name']) ?>" disabled>
                        <span class="edit-text" onclick="enableEdit('name')">Edit</span>
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <label>Email:</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        <span class="edit-text" onclick="enableEdit('email')">Edit</span>
                    </div>

                    <!-- Phone -->
                    <div class="input-group">
                        <label>Phone:</label>
                        <input type="text" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" disabled>
                        <span class="edit-text" onclick="enableEdit('phone')">Edit</span>
                    </div>

                    <!-- Date of Birth -->
                    <div class="input-group">
                        <label>Date of Birth:</label>
                        <input type="date" id="dob" value="<?= htmlspecialchars($user['dob']) ?>" disabled>
                        <span class="edit-text" onclick="enableEdit('dob')">Edit</span>
                    </div>

                    <button type="submit" class="save-btn">Save</button>
                </form>
            </section>
        </div>
    </div>

    <?php include '../foot.php'; ?>

    <!-- 🌟 自定义弹出框 -->
    <div id="custom-popup" class="popup">
        <div class="popup-content">
            <p id="popup-message"></p>
            <button onclick="closePopup()">OK</button>
        </div>
    </div>
</body>

</html>