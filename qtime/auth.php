<?php
require 'base.php'; // confirm database connection

// ✅ default status
$user_logged_in = false;
$user_email = "";
$user_role = "";

//if session have user id, then user is logged in
if (isset($_SESSION["user_id"])) {
    $stmt = $db->prepare("SELECT email, role FROM users WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $user_logged_in = true;
        $user_email = $user->email;
        $user_role = $user->role; //get user role
    }
}

// let other pages know user logged in status
?>
