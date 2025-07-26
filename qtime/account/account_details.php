<?php

// ✅ 获取用户信息
$user_id = $_SESSION["user_id"];
$stmt = $db->prepare("SELECT name, email, phone, dob FROM users WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Account Details</h2>
<form class="account-info" id="accountForm">
    <div class="input-group">
        <label>Name:</label>
        <input type="text" id="name" value="<?= htmlspecialchars($user['name']) ?>" disabled>
        <span class="edit-text" onclick="enableEdit('name')">Edit</span>
    </div>

    <div class="input-group">
        <label>Email:</label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
        <span class="edit-text" onclick="enableEdit('email')">Edit</span>
    </div>

    <div class="input-group">
        <label>Phone:</label>
        <input type="text" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" disabled>
        <span class="edit-text" onclick="enableEdit('phone')">Edit</span>
    </div>

    <div class="input-group">
        <label>Date of Birth:</label>
        <input type="date" id="dob" value="<?= htmlspecialchars($user['dob']) ?>" disabled>
        <span class="edit-text" onclick="enableEdit('dob')">Edit</span>
    </div>

    <button type="submit" class="save-btn">Save</button>
</form>
