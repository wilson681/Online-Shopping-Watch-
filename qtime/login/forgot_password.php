<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../head.php';

// Initialize variables
$error = '';
$success = false;
$step = 1; // 1: email input, 2: security question, 3: password reset
$email = '';
$security_question = '';
$user_id = '';
$role = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step1'])) {
        // Step 1: Verify email
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error = 'Please enter your email ';
        } else {
            // Check if user exists and get role
            $query = "SELECT user_id, security_question, role, password_hash FROM users WHERE email = :email";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch();

                if ($user) {
                    $user_id = $user->user_id;
                    $security_question = $user->security_question;
                    $role = $user->role;
                    $_SESSION['current_password_hash'] = $user->password_hash;
                    
                    // Check role and handle accordingly
                    if ($role === 'admin') {
                        $error = 'User not found with the provided email';
                        $step = 1;
                    } elseif ($role === 'customer') {
                        $step = 2;
                    } else {
                        $error = 'Invalid user role';
                        $step = 1;
                    }
                } else {
                    $error = 'User not found with the provided email';
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = 'A database error occurred. Please try again.';
            }
        }
    } elseif (isset($_POST['step2'])) {
        // Step 2: Verify security answer
        $user_id = $_POST['user_id'];
        $security_answer = trim($_POST['security_answer'] ?? '');
        
        if (empty($security_answer)) {
            $error = 'Please enter your security answer';
            $step = 2;
            
            // Get security question again for display
            try {
                $stmt = $db->prepare("SELECT security_question, password_hash FROM users WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $user = $stmt->fetch();
                
                if ($user) {
                    $security_question = $user->security_question;
                    $_SESSION['current_password_hash'] = $user->password_hash;
                } else {
                    $error = 'User not found';
                    $step = 1;
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = 'A database error occurred. Please try again.';
                $step = 1;
            }
        } else {
            // Verify security answer
            try {
                $stmt = $db->prepare("SELECT user_id, password_hash FROM users WHERE user_id = :user_id AND security_answer = :security_answer");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':security_answer', $security_answer);
                $stmt->execute();
                $user = $stmt->fetch();
                
                if ($user) {
                    $step = 3;
                    $_SESSION['current_password_hash'] = $user->password_hash;
                } else {
                    $error = 'Incorrect security answer';
                    $step = 2;
                    
                    // Get security question again for display
                    $stmt = $db->prepare("SELECT security_question, password_hash FROM users WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->execute();
                    $user = $stmt->fetch();
                    
                    if ($user) {
                        $security_question = $user->security_question;
                        $_SESSION['current_password_hash'] = $user->password_hash;
                    } else {
                        $error = 'User not found';
                        $step = 1;
                    }
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = 'A database error occurred. Please try again.';
                $step = 1;
            }
        }
    } elseif (isset($_POST['step3'])) {
        // Step 3: Reset password
        $user_id = $_POST['user_id'];
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Validate password match first
        if ($new_password !== $confirm_password) {
            $error = 'Passwords do not match!';
            $step = 3;
        } else {
            // Validate password strength
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
                $error = 'Password must be at least 8 characters long, contain uppercase, lowercase, number and special character';
                $step = 3;
            } else {
                // Check if password is same as previous password
                if (isset($_SESSION['current_password_hash'])) {
                    if (password_verify($new_password, $_SESSION['current_password_hash'])) {
                        $error = 'New password cannot be the same as your previous password';
                        $step = 3;
                    } else {
                        // Hash and update password
                        try {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $db->prepare("UPDATE users SET password_hash = :password WHERE user_id = :user_id");
                            $stmt->bindParam(':password', $hashed_password);
                            $stmt->bindParam(':user_id', $user_id);
                            
                            if ($stmt->execute()) {
                                // Clear the session variable after successful update
                                unset($_SESSION['current_password_hash']);
                                error_log("Password reset success for user ID: $user_id");
                                $success = true;
                            } else {
                                $errorInfo = $stmt->errorInfo();
                                error_log("Password reset failed for user ID: $user_id - " . print_r($errorInfo, true));
                                $error = 'Failed to update password: ' . $errorInfo[2];
                                $step = 3;
                            }
                        } catch (PDOException $e) {
                            error_log("Database error: " . $e->getMessage());
                            $error = 'A database error occurred. Please try again.';
                            $step = 3;
                        }
                    }
                } else {
                    $error = 'Session expired. Please start the password reset process again.';
                    $step = 1;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/forgot_password.css">
<script src="forgot_password.js" defer></script>
</head>
<body>
    <div class="forgot-container">
        <h2>Forgot Password</h2>
        
        <?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

        
<?php if ($success): ?>
    <div id="success-message" class="success">Password reset successfully! You can now <a href="login.php">login</a> with your new password.</div>
<?php endif; ?>
        
        <?php if ($step === 1): ?>
            <!-- Step 1: Email Input -->
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email"
                           value="<?php echo htmlspecialchars($email); ?>" required>
                    <p class="info-text">Enter the email associated with your account</p>
                </div>
                <button type="submit" name="step1">Continue</button>
                <div class="return-login">
                    <a href="login.php">Return to Login</a>
                </div>
            </form>
            
        <?php elseif ($step === 2): ?>
            <!-- Step 2: Security Question -->
            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <div class="form-group">
                    <label>Security Question</label>
                    <input type="text" value="<?php echo htmlspecialchars($security_question); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="security_answer">Your Answer</label>
                    <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer" required>
                </div>
                <button type="submit" name="step2">Verify Answer</button>
                <div class="return-login">
                    <a href="login.php">Return to Login</a>
                </div>
            </form>
            
        <?php elseif ($step === 3): ?>
            <!-- Step 3: Password Reset -->
            <form method="POST" action="forgot_password.php" onsubmit="return validatePasswordForm()">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="new-password" name="new_password" 
                               placeholder="New Password" required
                               oninput="validatePassword()">
                        <span class="toggle-password" onclick="togglePassword('new-password', this)">👁</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm-password" name="confirm_password" 
                               placeholder="Confirm New Password" required
                               oninput="validatePassword()">
                        <span class="toggle-password" onclick="togglePassword('confirm-password', this)">👁</span>
                    </div>
                    <div class="password-error" id="password-error"></div>
                </div>
                
                <ul class="password-requirements">
                    <li id="length-req">❌ Minimum of 8 characters</li>
                    <li id="complexity-req">❌ Uppercase, lowercase letters, one number and special character</li>
                </ul>
                
                <button type="submit" name="step3" id="savePasswordBtn" disabled>Reset Password</button>
                <div class="return-login">
                    <a href="login.php">Return to Login</a>
                </div>
            </form>

          
        <?php endif; ?>
    </div>
</body>
</html>