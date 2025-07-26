// ✅ 实时检查 Confirm Password 是否匹配
function checkPasswordMatch() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var errorText = document.getElementById("password-error");

    if (password !== confirmPassword) {
        errorText.textContent = "Passwords do not match!";
        errorText.style.color = "red";
    } else {
        errorText.textContent = "";
    }
}

// ✅ 切换密码可见性
function togglePassword(fieldId, icon) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.add("active"); // 眼睛变黑（已开启）
    } else {
        field.type = "password";
        icon.classList.remove("active"); // 眼睛恢复默认颜色
    }
}

// ✅ 选择安全问题后显示答案框
function toggleSecurityAnswer() {
    var question = document.getElementById("security_question").value;
    var answerGroup = document.getElementById("security_answer_group");

    if (question) {
        answerGroup.style.display = "block";
        document.getElementById("security_answer").setAttribute("required", "required");
    } else {
        answerGroup.style.display = "none";
        document.getElementById("security_answer").removeAttribute("required");
    }
}

// ✅ 验证 Email 格式
function validateEmail() {
    var email = document.querySelector("input[name='email']").value.trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showPopup("Invalid email format! Please enter a valid email.");
        return false;
    }
    return true;
}

// ✅ 验证手机号码格式
function validatePhone() {
    var phone = document.querySelector("input[name='phone']").value.trim();
    var phonePattern = /^[0-9]{10,11}$/; // 允许 10-11 位数字
    if (!phonePattern.test(phone)) {
        showPopup("Invalid phone number! Please enter a 10-11 digit number.");
        return false;
    }
    return true;
}

// ✅ 修正密码规则：至少 8 个字符，包含大小写字母、数字和特殊字符
function validatePasswordStrength() {
    var password = document.getElementById("password").value;
    var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#_?&])[A-Za-z\d@$!%*#_?&]{8,}$/;
    
    if (!passwordPattern.test(password)) {
        showPopup("Weak Password", "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.");
        return false;
    }
    return true;
}


// ✅ 确保用户勾选 "I Agree"
function validateCheckbox() {
    var agree = document.getElementById("agree").checked;
    if (!agree) {
        showPopup("You must agree to the Privacy Policy before proceeding!");
        return false;
    }
    return true;
}

// ✅ 确保用户选择安全问题并填写答案
function validateSecurityQuestion() {
    var question = document.getElementById("security_question").value;
    var answer = document.getElementById("security_answer").value.trim();

    if (!question) {
        alert("Please select a security question.");
        return false;
    }

    if (!answer) {
        alert("Please provide an answer for the selected security question.");
        return false;
    }

    return true;
}


// ✅ 显示自定义弹窗
function showPopup(title, message) {
    document.getElementById("popupTitle").textContent = title;
    document.getElementById("popupMessage").textContent = message;
    document.getElementById("popupOverlay").style.display = "block"; // 让背景变暗
    document.getElementById("customPopup").style.display = "block";  // 显示弹窗
}

// ✅ 关闭弹窗
function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("customPopup").style.display = "none";
}

// ✅ 仅在 JS 额外验证失败时，弹出自定义框
function validateForm(event) {
    // ✅ 让 HTML5 继续生效（用户未填 required，浏览器会阻止提交）
    if (!document.querySelector("form").checkValidity()) {
        return true; // 让 HTML5 继续拦截
    }

    event.preventDefault(); // ❌ 阻止提交（只在 JS 额外验证失败时）

    if (!validateEmail()) return false;
    if (!validatePhone()) return false;
    if (!validatePasswordStrength()) return false;
    if (!validateCheckbox()) return false;
    if (!validateSecurityQuestion()) return false;

    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    if (password !== confirmPassword) {
        showPopup("Password Mismatch", "Passwords do not match!");
        return false;
    }

    // ✅ 所有验证通过，显示成功弹窗
    showPopup("Registration Successful", "Your account has been created. Redirecting...");
    setTimeout(() => {
        document.querySelector("form").submit(); // 2 秒后提交表单
    }, 2000);
}

// ✅ 页面加载时执行
document.addEventListener("DOMContentLoaded", function () {
    // 绑定表单提交事件
    document.querySelector("form").addEventListener("submit", validateForm);

    // 监听 URL 参数，检查是否有错误
    const params = new URLSearchParams(window.location.search);
    if (params.has("error") && params.get("error") === "email_exists") {
        showPopup("Registration Error", "This email is already registered. Please use another one.");
    }
});
