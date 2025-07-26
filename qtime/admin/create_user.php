<?php
session_start();
require("../auth.php");
//i lazy to create a new file for post method so i just combine them into one file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $name              = trim($_POST['name'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $password          = trim($_POST['password'] ?? '');
    $phone             = trim($_POST['phone'] ?? '');
    $address           = trim($_POST['address'] ?? '');
    $postcode          = trim($_POST['postcode'] ?? '');
    $state             = trim($_POST['state'] ?? '');
    $dob               = trim($_POST['dob'] ?? '');
    $security_answer   = trim($_POST['security_answer'] ?? '');
    $security_question = trim($_POST['security_question'] ?? '');
    $role              = trim($_POST['role'] ?? '');
    $errors = [];
    foreach (compact('name', 'email', 'password', 'phone', 'security_question', 'security_answer', 'role') as $field => $value) {
        if (empty($value)) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Email is already in use.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if (!preg_match('/^\d{10,11}$/', $phone)) {
        $errors[] = "Phone must be 10 to 11 digits.";
    }
    $profile_picture_path = '../image/default_profile.jpg';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $tmpFilePath = $_FILES['profile_picture']['tmp_name'];
        $originalName = basename($_FILES['profile_picture']['name']);
        $uploadDir = '../image/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destination = $uploadDir . time() . '_' . $originalName;
        if (move_uploaded_file($tmpFilePath, $destination)) {
            $profile_picture_path = $destination;
        } else {
            $errors[] = "Error moving the uploaded file.";
        }
    }
    if ($errors) {
        echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        exit;
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password_hash, phone, address, postcode, state, dob, security_question, security_answer, role, profile_picture)
            VALUES (:name, :email, :password_hash, :phone, :address, :postcode, :state, :dob, :security_question, :security_answer, :role, :profile_picture)";
    $stmt = $db->prepare($sql);
    $params = compact('name', 'email', 'password_hash', 'phone', 'address', 'postcode', 'state', 'dob', 'security_question', 'security_answer', 'role');
    $params['profile_picture'] = $profile_picture_path;
    echo $stmt->execute($params) ?
        json_encode(['status' => 'success']) :
        json_encode(['status' => 'error', 'message' => 'Database insert failed.']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="../css/user_detail.css">
    <link rel="stylesheet" href="../css/create_user.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <script src="create_user.js" defer></script>
</head>

<body>
    <div class="edit-container">
        <div class="header-section">
            <h2>Create User</h2>
        </div>
        <div class="profile-section">
            <div class="profile-image-container">
                <img id="profileImage" src="../image/default_profile.jpg" alt="Profile Picture">
                <div class="edit-icon" onclick="triggerFileInput();">
                    <img src="../image/edit_icon.svg" alt="Edit Icon">
                </div>
            </div>
        </div>
        <form id="createUserForm" method="post" enctype="multipart/form-data">
            <input type="file" id="profileFileInput" name="profile_picture" style="display:none;" accept="image/*">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="postcode">Postcode:</label>
                    <input type="text" id="postcode" name="postcode" required>
                </div>
                <div class="form-group">
                    <label for="state">State:</label>
                    <select id="state" name="state" required>
                        <option value="" disabled>Select your state</option>
                        <option>Johor</option>
                        <option>Kedah</option>
                        <option>Kelantan</option>
                        <option>Malacca</option>
                        <option>Negeri Sembilan</option>
                        <option>Pahang</option>
                        <option>Perak</option>
                        <option>Perlis</option>
                        <option>Penang</option>
                        <option>Sabah</option>
                        <option>Sarawak</option>
                        <option>Selangor</option>
                        <option>Terengganu</option>
                        <option>Kuala Lumpur</option>
                        <option>Putrajaya</option>
                        <option>Labuan</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="security_question">Security Question:</label>
                    <select id="security_question" name="security_question" required>
                        <option value="What is your pet's name?">What is your pet's name?</option>
                        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                        <option value="What was the name of your first school?">What was the name of your first school?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="security_answer">Security Answer:</label>
                    <input type="text" id="security_answer" name="security_answer" required>
                </div>
            </div>

            <button type="submit" class="submitButton">Create User</button>
            <a href="admin.php?section=member" class="exit-btn">Exit</a>
        </form>

        <div id="successMsg" class="success-popup"></div>
        <div id="errorMsg" class="error-popup"></div>
    </div>
</body>

</html>