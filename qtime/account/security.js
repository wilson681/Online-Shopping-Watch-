document.addEventListener("DOMContentLoaded", function () {
    // 获取 Password & Security Modal
    const passwordModal = document.getElementById("password-modal");
    const securityModal = document.getElementById("security-modal");
    const popup = document.getElementById("custom-popup");
    const popupMessage = document.getElementById("popup-message");
    const popupCloseBtn = document.getElementById("popup-close-btn");

    // 🔐 Password Modal
    window.showPasswordModal = function () {
        passwordModal.style.display = "block";
    };

    window.closePasswordModal = function () {
        passwordModal.style.display = "none";
    };

    // 🛡️ Security Question Modal
    window.showSecurityModal = function () {
        securityModal.style.display = "block";
        document.getElementById("security-change-section").style.display = "none";
        document.getElementById("verify-password").value = "";
    };

    window.closeSecurityModal = function () {
        securityModal.style.display = "none";
        document.getElementById("password-verify-section").style.display = "block";
        document.getElementById("security-change-section").style.display = "none";
        document.getElementById("verify-password").value = "";
        document.getElementById("new-security-question").value = "";
        document.getElementById("new-security-answer").value = "";
    };

    // ✅ Custom Popup 可关闭
    window.showPopup = function (message) {  // ✅ 变成全局
        popupMessage.innerText = message;
        popup.style.display = "block";
    };
    
    window.closePopup = function () {  // ✅ 变成全局
        popup.style.display = "none";
    };
    
    if (popupCloseBtn) {
        popupCloseBtn.addEventListener("click", window.closePopup);
    }
    

    popup.addEventListener("click", function (event) {
        if (event.target === popup) {
            closePopup();
        }
    });

    // 🔑 Password Validation
    const currentPasswordInput = document.getElementById("current-password");
    const newPasswordInput = document.getElementById("new-password");
    const confirmPasswordInput = document.getElementById("confirm-password");
    const savePasswordBtn = document.getElementById("save-password-btn");
    const lengthReq = document.getElementById("length-req");
    const complexityReq = document.getElementById("complexity-req");

    if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput || !savePasswordBtn) {
        return;
    }

    var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#_?&])[A-Za-z\d@$!%*#_?&]{8,}$/;

    function validatePassword() {
        const password = newPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        const isLengthValid = password.length >= 8;
        const isComplexityValid =passwordPattern.test(password);

        lengthReq.innerText = isLengthValid ? "✔️ Minimum of 8 characters" : "❌ Minimum of 8 characters";
        complexityReq.innerText = isComplexityValid
        ? "✔️ Uppercase, lowercase letters, one number & one special character"
        : "❌ Uppercase, lowercase letters, one number & one special character";

        savePasswordBtn.disabled = !(isLengthValid && isComplexityValid);
    }

    newPasswordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validatePassword);

    savePasswordBtn.addEventListener("click", function () {
        const currentPassword = currentPasswordInput.value.trim();
        const newPassword = newPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        if (!currentPassword || !newPassword || !confirmPassword) {
            showPopup("⚠️ Please fill in all fields!");
            return;
        }

        if (newPassword !== confirmPassword) {
            showPopup("❌ New passwords do not match!");
            return;
        }

        if (currentPassword == newPassword) {
            showPopup("❌ New password cannot be the same as the current password!");
            return;
        }

        fetch("security_ajax.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: "update_password",
                current_password: currentPassword,
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopup("✅ Password updated successfully!");
                closePasswordModal();
                currentPasswordInput.value = "";
                newPasswordInput.value = "";
                confirmPasswordInput.value = "";
                validatePassword();
            } else {
                showPopup(data.message);
            }
        })
        .catch(() => {
            showPopup("❌ Something went wrong!");
        });
    });
});



document.getElementById("verify-password-btn").addEventListener("click", function (e) {
    e.preventDefault();
    const verifyPassword = document.getElementById("verify-password").value.trim();

    if (!verifyPassword) {
        showPopup("⚠️ Please enter your password!");
        return;
    }

    fetch("security_ajax.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: "verify_password",
            password: verifyPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 验证成功后隐藏密码框，显示修改Security Question部分
            document.getElementById("password-verify-section").style.display = "none";
            document.getElementById("security-change-section").style.display = "block";
            showPopup("✅ Password verified!");
        } else {
            showPopup("❌ Wrong password!");
        }
    })
    .catch((error) => {
        console.error("AJAX error:", error);
        showPopup("❌ Something went wrong!");
    });
});

  // 🛡️ Save New Security Question & Answer (AJAX)
  document.getElementById("save-security-btn").addEventListener("click", function () {
    const newQuestion = document.getElementById("new-security-question").value;
    const newAnswer = document.getElementById("new-security-answer").value.trim();

    if (!newQuestion || !newAnswer) {
        showPopup("⚠️ Please select a question and provide an answer!");
        return;
    }

    fetch("security_ajax.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: "update_security_question",
            security_question: newQuestion,
            security_answer: newAnswer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopup("✅ Security question updated successfully!");
            closeSecurityModal();
            document.getElementById("security-change-section").style.display = "none";
            document.getElementById("new-security-question").value = "";
            document.getElementById("new-security-answer").value = "";
        } else {
            showPopup(data.message);
        }
    })
    .catch(err => {
        console.error("AJAX error:", err);
        showPopup("❌ Something went wrong!");
    });
});



// ✅ 显示/隐藏密码
function togglePassword(fieldId, icon) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.add("active");
    } else {
        field.type = "password";
        icon.classList.remove("active");
    }
}
