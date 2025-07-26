<?php
error_reporting(0);
require '../head.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $db->prepare("SELECT profile_picture, password_hash, security_question FROM users WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../login/login.php");
    exit();
}

$current_question = $user['security_question'];
$currentPage = 'security';  // ✅ 加在此处


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
    <link rel="stylesheet" href="../css/security.css">
    <script src="security.js" defer></script>

</head>

<body>
    <div class="page-container">
        <div class="account-container">

            <!-- 左侧导航栏 -->
            <aside class="account-sidebar">
                <div class="profile-section">
                    <img id="profileImage" src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
                    <input type="file" id="profileUpload" hidden>
                    <button onclick="document.getElementById('profileUpload').click()" class="edit-profile-btn">Change</button>
                </div>

                <ul>
                    <li><a href="account.php">Account Details</a></li>
                    <li><a href="address.php">Delivery Address</a></li>
                    <li class="<?= $currentPage == 'security' ? 'active' : '' ?>"><a href="security.php">Security</a></li>
                </ul>
            </aside>
            <section class="account-content">
                <h2>Account Security</h2>

                <div class="security-section">
                    <h3>Password</h3>
                    <div class="input-group">
                        <input type="password" value="********" disabled>
                        <span class="edit-text" onclick="showPasswordModal()">Edit</span>
                    </div>
                </div>

                <div class="security-section">
                    <h3>Security Question & Answer</h3>

                    <div class="input-group">
                        <label>Security Question:</label>
                        <input type="text" value="<?= htmlspecialchars($current_question) ?>" disabled>
                        <span class="edit-text" onclick="showSecurityModal()">Edit</span>
                    </div>

                    <div class="input-group">
                        <label>Answer:</label>
                        <input type="password" value="********" disabled>
                    </div>
                </div>
            </section>


            <!-- 🔐 Change Password Modal -->
            <div id="password-modal" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePasswordModal()">✖️</span>
                    <h3>Edit Password</h3>
                    <!-- 当前密码 -->
                    <div class="password-wrapper">
                        <input type="password" id="current-password" placeholder="Current Password">
                        <span class="toggle-password" onclick="togglePassword('current-password', this)">👁</span>
                    </div>

                    <!-- 新密码 -->
                    <div class="password-wrapper">
                        <input type="password" id="new-password" placeholder="New Password">
                        <span class="toggle-password" onclick="togglePassword('new-password', this)">👁</span>
                    </div>

                    <!-- 确认新密码 -->
                    <div class="password-wrapper">
                        <input type="password" id="confirm-password" placeholder="Confirm New Password">
                        <span class="toggle-password" onclick="togglePassword('confirm-password', this)">👁</span>
                    </div>



                    <li id="length-req">❌ Minimum of 8 characters</li>
                    <li id="complexity-req">❌ Uppercase, lowercase letters, one number & one special character</li>


                    <button id="save-password-btn">Save</button>

                </div>
            </div>

            <!-- 🛡️ Security Question Modal -->
            <div id="security-modal" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closeSecurityModal()">✖️</span>
                    <h3>Change Security Question</h3>

                    <!-- 验证密码部分 -->
                    <div id="password-verify-section">
                        <div class="password-wrapper">
                            <input type="password" id="verify-password" placeholder="Enter your password to verify">
                            <span class="toggle-password" onclick="togglePassword('verify-password', this)">👁</span>
                        </div>

                        <button id="verify-password-btn">Verify</button>
                    </div>

                    <!-- 修改问题和答案部分，默认隐藏 -->
                    <div id="security-change-section" style="display:none;">
                        <select id="new-security-question">
                            <option value="">Select a new security question</option>
                            <option value="What's your pet's name?">What's your pet's name?</option>
                            <option value="Your mother's maiden name?">Your mother's maiden name?</option>
                            <option value="Your first school's name?">Your first school's name?</option>
                        </select>

                        <input type="text" id="new-security-answer" placeholder="Enter your new answer">
                        <button id="save-security-btn">Save</button>
                    </div>
                </div>
            </div>



            <!-- 自定义弹出框 -->
            <!-- 🔔 Custom Popup -->
            <div id="custom-popup" class="popup">
                <div class="popup-content">
                    <p id="popup-message"></p>
                    <button id="popup-close-btn">OK</button>
                </div>
            </div>

        </div>
        <?php include '../foot.php'; ?>
    </div>
</body>
</html>