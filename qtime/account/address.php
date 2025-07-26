<?php
require '../head.php'; // ✅ 确保数据库连接

// ✅ 确保用户已登录
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// ✅ 获取用户头像 & 地址信息
$stmt = $db->prepare("SELECT profile_picture, address, postcode, state FROM users WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ 处理数据库数据，防止 NULL 值导致问题
$current_profile = $user['profile_picture'] ?? 'images/default_profile.jpg';
$current_address = $user['address'] ?? 'No address set';
$current_postcode = $user['postcode'] ?? '';
$current_state = $user['state'] ?? '';

$currentPage = 'address';  // ✅ 确保左侧导航栏高亮
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Address</title>
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/address.css"> <!-- ✅ 专属 Delivery Address CSS -->
    <script src="address.js" defer></script>
</head>

<body>
    <div class="page-container">
        <div class="account-container">

            <!-- 🌟 左侧导航栏 -->
            <aside class="account-sidebar">
                <div class="profile-section">
                    <img id="profileImage" src="<?= htmlspecialchars($current_profile) ?>" alt="Profile Picture">
                    <input type="file" id="profileUpload" hidden>
                    <button onclick="document.getElementById('profileUpload').click()" class="edit-profile-btn">Change</button>
                </div>

                <ul>
                    <li><a href="account.php">Account Details</a></li>
                    <li class="<?= $currentPage == 'address' ? 'active' : '' ?>"><a href="address.php">Delivery Address</a></li>
                    <li><a href="security.php">Security</a></li>
                </ul>
            </aside>

            <!-- 🌟 右侧内容区 -->
            <section class="account-content">
                <h2>Delivery Address</h2>

                <div class="address-section">
                    <!-- Address -->
                    <div class="address-row">
                        <label for="display-address">Address:</label>
                        <input type="text" id="display-address" value="<?= htmlspecialchars($current_address) ?>" disabled>
                        <span class="edit-text" onclick="enableEditing()">Edit</span>
                    </div>

                    <!-- Post Code -->
                    <div class="address-row">
                        <label for="display-postcode">Post Code:</label>
                        <input type="text" id="display-postcode" value="<?= htmlspecialchars($current_postcode) ?>" disabled>
                        <span class="edit-text" onclick="enableEditing()">Edit</span>
                    </div>

                    <!-- State -->
                    <div class="address-row">
                        <label for="state">State:</label>
                        <select id="state" disabled>
                            <option value="" disabled>Select your state</option>
                            <option value="Johor" <?= $current_state == "Johor" ? "selected" : "" ?>>Johor</option>
                            <option value="Kedah" <?= $current_state == "Kedah" ? "selected" : "" ?>>Kedah</option>
                            <option value="Kelantan" <?= $current_state == "Kelantan" ? "selected" : "" ?>>Kelantan</option>
                            <option value="Malacca" <?= $current_state == "Malacca" ? "selected" : "" ?>>Malacca</option>
                            <option value="Negeri Sembilan" <?= $current_state == "Negeri Sembilan" ? "selected" : "" ?>>Negeri Sembilan</option>
                            <option value="Pahang" <?= $current_state == "Pahang" ? "selected" : "" ?>>Pahang</option>
                            <option value="Perak" <?= $current_state == "Perak" ? "selected" : "" ?>>Perak</option>
                            <option value="Perlis" <?= $current_state == "Perlis" ? "selected" : "" ?>>Perlis</option>
                            <option value="Penang" <?= $current_state == "Penang" ? "selected" : "" ?>>Penang</option>
                            <option value="Sabah" <?= $current_state == "Sabah" ? "selected" : "" ?>>Sabah</option>
                            <option value="Sarawak" <?= $current_state == "Sarawak" ? "selected" : "" ?>>Sarawak</option>
                            <option value="Selangor" <?= $current_state == "Selangor" ? "selected" : "" ?>>Selangor</option>
                            <option value="Terengganu" <?= $current_state == "Terengganu" ? "selected" : "" ?>>Terengganu</option>
                            <option value="Kuala Lumpur" <?= $current_state == "Kuala Lumpur" ? "selected" : "" ?>>Kuala Lumpur</option>
                            <option value="Putrajaya" <?= $current_state == "Putrajaya" ? "selected" : "" ?>>Putrajaya</option>
                            <option value="Labuan" <?= $current_state == "Labuan" ? "selected" : "" ?>>Labuan</option>
                        </select>
                        <span class="edit-text" onclick="enableEditing()">Edit</span>
                    </div>

                    <!-- Save 按钮 -->
                    <!-- Save 按钮 -->
                    <div class="button-group">
                        <button id="save-address-btn" class="save-btn" style="display: none;">Save</button>
                    </div>

                </div>
            </section>


            <!-- 🔔 Custom Popup -->
            <div id="custom-popup" class="popup" style="display: none;">
                <div class="popup-content">
                    <p id="popup-message"></p> <!-- ✅ 确保这个 `<p>` 存在 -->
                    <button id="popup-close-btn" onclick="closePopup()">OK</button>
                </div>
            </div>


        </div>
    </div>
</body>
<?php include '../foot.php'; ?>

</html>