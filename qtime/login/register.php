<?php
require '../head.php'; // 确保数据库连接

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $dob = !empty($_POST["dob"]) ? $_POST["dob"] : NULL;
    $address = trim($_POST["address"]);
    $postcode = trim($_POST["postcode"]);
    $state = trim($_POST["state"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $security_question = isset($_POST["security_question"]) ? trim($_POST["security_question"]) : "";
    $security_answer = isset($_POST["security_answer"]) ? trim($_POST["security_answer"]) : "";

    // ✅ 后端最终检查（防止绕过前端）
    if (
        empty($first_name) || empty($last_name) || empty($email) || empty($phone) ||
        empty($address) || empty($postcode) || empty($state) || empty($password) ||
        empty($confirm_password) || empty($security_question) || empty($security_answer) ||
        $password !== $confirm_password
    ) {
        redirect("register.php");
    }

    // ✅ 检查 Email 是否已存在
    $stmt = $db->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // ❌ Email 已被使用，传递错误变量
        echo "<script>window.location.href = 'register.php?error=email_exists';</script>";
        exit();
    }

    // ✅ 加密密码
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $full_name = $first_name . " " . $last_name;

    // ✅ 存储用户数据
    $sql = "INSERT INTO users (name, email, password_hash, role, profile_picture, address, postcode, state, phone, dob, security_question, security_answer, created_at) 
            VALUES (:name, :email, :password, 'customer', '../image/default_profile.jpg', :address, :postcode, :state, :phone, :dob, :security_question, :security_answer, NOW())";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(":name", $full_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":address", $address);
    $stmt->bindParam(":postcode", $postcode);
    $stmt->bindParam(":state", $state);
    $stmt->bindParam(":phone", $phone);

    // ✅ 处理 NULL 值
    if ($dob === NULL) {
        $stmt->bindValue(":dob", NULL, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(":dob", $dob);
    }

    $stmt->bindParam(":security_question", $security_question);
    $stmt->bindParam(":security_answer", $security_answer);

    // ✅ 检查 SQL 是否执行成功
    if ($stmt->execute()) {
        $_SESSION["success"] = "Account successfully created! Please login.";
        redirect("login.php");
    } else {
        // ⚠️ 直接显示数据库错误（用于调试，完成后请删除）
        die("Database error: " . implode(" | ", $stmt->errorInfo()));
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/register.css">
    <script src="register.js" defer></script> <!-- ✅ 让 JavaScript 正确加载 -->
</head>

<body>
    <div class="page-container">
        <div class="register-wrapper">
            <div class="register-image">
                <img src="../image/register.jpg" alt="Brand Ambassador">
            </div>

            <div class="register-container">
                <h2>Create Your Account</h2>

                <form action="register.php" method="POST" onsubmit="return validateForm()">
                    <div class="section-box">
                        <h3 class="section-title">Your Personal Details</h3>
                        <div class="form-group">
                            <label>First Name <span>*</span></label>
                            <input type="text" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name <span>*</span></label>
                            <input type="text" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span>*</span></label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number <span>*</span></label>
                            <input type="text" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth (Optional)</label>
                            <input type="date" name="dob">
                        </div>
                        <div class="form-group">
                            <label>Address <span>*</span></label>
                            <input type="text" name="address" required>
                        </div>
                        <div class="form-group">
                            <label>Postcode <span>*</span></label>
                            <input type="text" name="postcode" required>
                        </div>
                        <div class="form-group">
                            <label>State <span>*</span></label>
                            <select name="state" required>
                            <option value="" disabled>Select your state</option>
                            <option value="Johor" >Johor</option>
                            <option value="Kedah">Kedah</option>
                            <option value="Kelantan" >Kelantan</option>
                            <option value="Malacca" >Malacca</option>
                            <option value="Negeri Sembilan">Negeri Sembilan</option>
                            <option value="Pahang" >Pahang</option>
                            <option value="Perak" >Perak</option>
                            <option value="Perlis" >Perlis</option>
                            <option value="Penang">Penang</option>
                            <option value="Sabah" >Sabah</option>
                            <option value="Sarawak" >Sarawak</option>
                            <option value="Selangor">Selangor</option>
                            <option value="Terengganu" >Terengganu</option>
                            <option value="Kuala Lumpur">Kuala Lumpur</option>
                            <option value="Putrajaya">Putrajaya</option>
                            <option value="Labuan">Labuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-box">
                        <h3 class="section-title">Your Password</h3>
                        <div class="form-group">
                            <label>Password <span>*</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" required>
                                <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span>*</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" required onkeyup="checkPasswordMatch()">
                                <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁</span>
                            </div>
                            <p id="password-error" class="error-message"></p>
                        </div>
                    </div>

                    <div class="section-box">
                        <h3 class="section-title">Security Question</h3>
                        <div class="form-group">
                            <label>Security Question <span>*</span></label>
                            <select id="security_question" name="security_question" required onchange="toggleSecurityAnswer()">
                                <option value="">Please Select</option>
                                <option value="What is your pet's name?">What is your pet's name?</option>
                                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                                <option value="What was the name of your first school?">What was the name of your first school?</option>
                            </select>
                        </div>

                        <div class="form-group" id="security_answer_group" style="display: none;">
                            <label>Security Answer <span>*</span></label>
                            <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer">
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="agree" required>
                        <label for="agree">I have read and agree to the <a href="../terms/privacy.php">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="submit-btn">Create Account</button>
                </form>
            </div>
        </div>
        <!-- 🔥 弹出框 -->
        <div class="popup-overlay" id="popupOverlay"></div>
        <div class="custom-popup" id="customPopup">
            <h3 id="popupTitle">Error</h3>
            <p id="popupMessage">Something went wrong!</p>
            <button class="popup-btn" onclick="closePopup()">OK</button>
        </div>
        <?php include '../foot.php'; ?>
    </div>
</body>

</html>