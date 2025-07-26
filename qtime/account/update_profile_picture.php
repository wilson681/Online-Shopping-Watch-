<?php
require '../auth.php'; // ✅ 确保数据库连接

$response = ["success" => false];

if (!isset($_SESSION["user_id"])) {
    $response["message"] = "You must be logged in!";
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION["user_id"];

if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
    $file = $_FILES["profile_picture"];
    $file_ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $allowed_exts = ["jpg", "jpeg", "png"];

    if (!in_array(strtolower($file_ext), $allowed_exts)) {
        $response["message"] = "❌ Only JPG, JPEG, and PNG files are allowed!";
        echo json_encode($response);
        exit();
    }

    $target_dir = "../image/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $new_filename = "profile_" . $user_id . "." . $file_ext;
    $target_file = $target_dir . $new_filename;

  // ✅ **删除旧头像**
  $stmt = $db->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $old_picture = $stmt->fetchColumn();

  if ($old_picture && file_exists($old_picture) && strpos($old_picture, 'default_profile.jpg') === false) {
      unlink($old_picture);
  }

  if (move_uploaded_file($file["tmp_name"], $target_file)) {
      // ✅ **更新数据库**
      $stmt = $db->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
      $stmt->execute([$target_file, $user_id]);

      $response["success"] = true;
      $response["new_image_url"] = $target_file;
  } else {
      $response["message"] = "❌ Upload failed!";
  }
} else {
  $response["message"] = "❌ No file uploaded!";
}

echo json_encode($response);
?>