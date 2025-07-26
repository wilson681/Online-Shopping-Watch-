<?php
require("../auth.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if ($user_id === 0) {
    echo json_encode(['status'=>'error', 'message'=>'No user ID provided.']);
    exit;
}

$upload_dir = dirname(__DIR__) . '/image/';
$allowed_types = ["jpg", "jpeg", "png", "gif"];
$maxFileSize = 2 * 1024 * 1024;

$name              = trim($_POST['name'] ?? '');
$email             = trim($_POST['email'] ?? '');
$password_hash     = trim($_POST['password_hash'] ?? '');
$phone             = trim($_POST['phone'] ?? '');
$address           = trim($_POST['address'] ?? '');
$postcode          = trim($_POST['postcode'] ?? '');
$state             = trim($_POST['state'] ?? '');
$dob               = trim($_POST['dob'] ?? '');
$security_answer   = trim($_POST['security_answer'] ?? '');
$security_question = trim($_POST['security_question'] ?? '');
$role              = trim($_POST['role'] ?? '');

$errors = [];

foreach (compact('name', 'email', 'phone', 'address', 'postcode', 'state', 'security_question', 'security_answer', 'role') as $field => $value) {
    if (empty($value)) $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";

$stmt = $db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->execute([$email, $user_id]);
if ($stmt->fetch()) $errors[] = "Email is already in use.";

if (!empty($password_hash) && strlen($password_hash) < 8) $errors[] = "Password must be at least 8 characters.";

if (!preg_match('/^\d{10,11}$/', $phone)) $errors[] = "Phone must be 10 to 11 digits.";

if ($errors) {
    echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
    exit;
}    

$password_hash = $password_hash ? password_hash($password_hash, PASSWORD_DEFAULT) : null;
$profile_picture_url = $_POST["current_profile_picture"] ?? null;

if (!empty($_FILES["profile_picture"]["name"])) {
    if (!file_exists($upload_dir) || !is_writable($upload_dir)) {
        echo json_encode(['status' => 'error', 'message' => 'Upload directory issue.']);
        exit;
    }

    $safe_name = preg_replace("/[^a-zA-Z0-9-_]/", "", $name);
    $file_type = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
        exit;
    }

    if ($_FILES['profile_picture']['size'] > $maxFileSize) {
        echo json_encode(['status' => 'error', 'message' => 'Image exceeds 2MB.']);
        exit;
    }

    $filename = $safe_name . uniqid() . "_profile_picture." . $file_type;
    $upload_file = $upload_dir . $filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_file)) {
        $profile_picture_url = '../image/' . $filename;
        if (!empty($_POST['current_profile_picture'])) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $_POST['current_profile_picture']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
        exit;
    }
}

$sql = "UPDATE users SET name=:name,email=:email,phone=:phone,address=:address,postcode=:postcode,state=:state,dob=:dob,security_answer=:security_answer,security_question=:security_question,role=:role";
if ($password_hash) $sql .= ",password_hash=:password_hash";
if ($profile_picture_url) $sql .= ",profile_picture=:profile_picture";
$sql .= " WHERE user_id=:user_id";

$stmt = $db->prepare($sql);

$params = compact('name', 'email', 'phone', 'address', 'postcode', 'state', 'dob', 'security_answer', 'security_question', 'role', 'user_id');
if ($password_hash) $params['password_hash'] = $password_hash;
if ($profile_picture_url) $params['profile_picture'] = $profile_picture_url;

echo $stmt->execute($params) ? 
     json_encode(['status'=>'success']) :
     json_encode(['status'=>'error', 'message'=>'Database update failed.']);
exit;
