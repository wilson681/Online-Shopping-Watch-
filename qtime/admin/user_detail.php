<?php
require("../auth.php");
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0; //if no user_id, set to 0
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
  echo "User not found.";
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Profile</title>
  <link rel="stylesheet" href="../css/user_detail.css">
  <link rel="stylesheet" href="../css/update_user.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <script defer src="edit_uprofile.js"></script>
  <script defer src="upuservalid.js"></script>
</head>
<body>
  <div class="edit-container">
    <div class="header-section">
      <h2>User Profile</h2>
    </div>
    <form id="userForm" action="update_user.php" method="post" enctype="multipart/form-data">
      <input type="file" id="profileFileInput" name="profile_picture" style="display:none;" accept="image/*">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']); ?>">
      <input type="hidden" name="current_profile_picture" value="<?= htmlspecialchars($user['profile_picture']) ?>">
      <div class="profile-section">
        <div class="profile-image-container">
          <img id="profileImage" src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
          <div class="edit-icon" onclick="triggerFileInput();">
            <img src="../image/edit_icon.svg" alt="Edit Icon">
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Name:</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required readonly> <!--readonly cannot be edit-->
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required readonly>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password:</label>
          <!--placeholder let admin see ####only not show the password after hashing -->
          <input type="text" id="password" name="password_hash" placeholder="#########" require readonly>
        </div>
        <div class="form-group">
          <label>Role:</label>
          <select name="role" disabled>
            <option value="customer" <?= $user['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Phone:</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" readonly>
        </div>
        <div class="form-group">
          <label>Address:</label>
          <input type="text" name="address" value="<?= htmlspecialchars($user['address']); ?>" readonly>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Postcode:</label>
          <input type="text" name="postcode" value="<?= htmlspecialchars($user['postcode']); ?>" readonly>
        </div>
        <div class="form-group">
          <label>State:</label>
          <select name="state" disabled> <!--same with readonly but for select-->
            <option <?= $user['state'] == 'Johor' ? 'selected' : ''; ?>>Johor</option>
            <option <?= $user['state'] == 'Kedah' ? 'selected' : ''; ?>>Kedah</option>
            <option <?= $user['state'] == 'Kelantan' ? 'selected' : ''; ?>>Kelantan</option>
            <option <?= $user['state'] == 'Malacca' ? 'selected' : ''; ?>>Malacca</option>
            <option <?= $user['state'] == 'Negeri Sembilan' ? 'selected' : ''; ?>>Negeri Sembilan</option>
            <option <?= $user['state'] == 'Pahang' ? 'selected' : ''; ?>>Pahang</option>
            <option <?= $user['state'] == 'Perak' ? 'selected' : ''; ?>>Perak</option>
            <option <?= $user['state'] == 'Perlis' ? 'selected' : ''; ?>>Perlis</option>
            <option <?= $user['state'] == 'Penang' ? 'selected' : ''; ?>>Penang</option>
            <option <?= $user['state'] == 'Sabah' ? 'selected' : ''; ?>>Sabah</option>
            <option <?= $user['state'] == 'Sarawak' ? 'selected' : ''; ?>>Sarawak</option>
            <option <?= $user['state'] == 'Selangor' ? 'selected' : ''; ?>>Selangor</option>
            <option <?= $user['state'] == 'Terengganu' ? 'selected' : ''; ?>>Terengganu</option>
            <option <?= $user['state'] == 'Kuala Lumpur' ? 'selected' : ''; ?>>Kuala Lumpur</option>
            <option <?= $user['state'] == 'Putrajaya' ? 'selected' : ''; ?>>Putrajaya</option>
            <option <?= $user['state'] == 'Labuan' ? 'selected' : ''; ?>>Labuan</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Date of Birth:</label>
          <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']); ?>" readonly>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Security Question:</label>
          <select name="security_question" disabled>
            <option value="What is your pet's name?"
              <?php if ($user['security_question'] === "What is your pet's name?") echo "selected"; ?>>
              What is your pet's name?
            </option>
            <option value="What is your mother's maiden name?"
              <?php if ($user['security_question'] === "What is your mother's maiden name?") echo "selected"; ?>>
              What is your mother's maiden name?
            </option>
            <option value="What was the name of your first school?"
              <?php if ($user['security_question'] === "What was the name of your first school?") echo "selected"; ?>>
              What was the name of your first school?
            </option>
          </select>
        </div>
        <div class="form-group">
          <label>Security Answer:</label>
          <input type="text" name="security_answer" value="<?= htmlspecialchars($user['security_answer']); ?>" readonly>
        </div>
      </div>
      <button type="button" id="editButton" class="edit-btn">Edit Profile</button>
      <button type="submit" id="submitButton" class="submitButton" style="display:none;">Submit</button>
      <button type="button" id="cancelButton" class="cancelButton" style="display:none;">Cancel</button>
      <a href="admin.php?section=member" class="exit-btn">Exit</a>
    </form>
    <!--for the javscript prompt message -->
    <div id="successMsg" class="success-popup"></div>
    <div id="errorMsg" class="error-popup"></div>
  </div>
</body>

</html>