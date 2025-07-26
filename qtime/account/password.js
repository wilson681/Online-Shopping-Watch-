// 显示/隐藏密码
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.textContent = "🙈";
    } else {
        field.type = "password";
        icon.textContent = "👁️";
    }
}

// 密码强度检测
function validatePasswordStrength() {
    const password = document.getElementById("new_password").value;
    const strengthText = document.getElementById("password-strength");

    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (regex.test(password)) {
        strengthText.innerHTML = "✅ 强密码";
        strengthText.style.color = "green";
    } else {
        strengthText.innerHTML = "❌ 密码必须至少8位，包含大小写、数字和特殊字符";
        strengthText.style.color = "red";
    }
}
