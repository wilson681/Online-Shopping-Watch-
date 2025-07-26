const newPasswordInput = document.getElementById('new-password');
const confirmPasswordInput = document.getElementById('confirm-password');
const lengthReq = document.getElementById('length-req');
const complexityReq = document.getElementById('complexity-req');
const savePasswordBtn = document.getElementById('savePasswordBtn');
const passwordError = document.getElementById('password-error');

function validatePassword() {
    const password = newPasswordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    // Validate password requirements
    const isLengthValid = password.length >= 8;
    const isComplexityValid = /[a-z]/.test(password) && 
                            /[A-Z]/.test(password) && 
                            /\d/.test(password) && 
                            /[@$!%*?&]/.test(password);
    
    // Update requirement indicators
    lengthReq.innerHTML = isLengthValid ? "✔️ Minimum of 8 characters" : "❌ Minimum of 8 characters";
    complexityReq.innerHTML = isComplexityValid ? "✔️ Uppercase, lowercase letters, one number and special character" : "❌ Uppercase, lowercase letters, one number and special character";
    
    // Check password match
    if (password && confirmPassword) {
        if (password !== confirmPassword) {
            passwordError.textContent = "Passwords do not match!";
        } else {
            passwordError.textContent = "";
        }
    } else {
        passwordError.textContent = "";
    }
    
    // Enable/disable submit button
    savePasswordBtn.disabled = !(isLengthValid && isComplexityValid && (password === confirmPassword));
}

// Toggle password visibility
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.add("active");
    } else {
        field.type = "password";
        icon.classList.remove("active");
    }
}

// Form validation
function validatePasswordForm() {
    validatePassword(); // Run validation one more time before submit
    
    if (savePasswordBtn.disabled) {
        alert("Please ensure all password requirements are met and passwords match!");
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // 自动跳转逻辑
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 3000);
    }
});