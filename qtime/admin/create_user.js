document.getElementById('createUserForm').addEventListener('submit', function(e) { 
    e.preventDefault();
//comment i already write at upuservalid.js
    if (!validateEmail() || !validatePhone() || !validatePasswordStrength()) {
        return;
    }
 
    let formData = new FormData(this);

    fetch('create_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showPopup('✅ Create successful!', 'success');
            setTimeout(() => {
                window.location.href = 'admin.php?section=member';
            }, 2000);
        } else {
            showPopup(result.message, 'error');
        }
    })
    .catch(() => showPopup('Server error!', 'error'));
});

function showPopup(message, type = 'success') {
    let popup = type === 'success' ? document.getElementById('successMsg') : document.getElementById('errorMsg');
    if (!popup) {
        console.log(message);
        return;
    }
    popup.textContent = message;
    popup.classList.add('show-popup');
    setTimeout(() => {
        popup.classList.remove('show-popup');
    }, 3000);
}

function triggerFileInput() {
    document.getElementById('profileFileInput').click();
}

document.getElementById('profileFileInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    }
});

function validateEmail() {
    let email = document.querySelector("input[name='email']").value.trim();
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showPopup("Invalid email format!", 'error');
        return false;
    }
    return true;
}

function validatePhone() {
    let phone = document.querySelector("input[name='phone']").value.trim();
    let phonePattern = /^[0-9]{10,11}$/; 
    if (!phonePattern.test(phone)) {
        showPopup("Invalid phone number! Must be 10-11 digits.", 'error');
        return false;
    }
    return true;
}

function validatePasswordStrength() {
    let passwordElem = document.getElementById("password");
    if (!passwordElem) {
        console.error("Password input not found!");
        return false;
    }
    let password = passwordElem.value;
    let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#_?&])[A-Za-z\d@$!%*#_?&]{8,}$/;
    if (!passwordPattern.test(password)) {
        showPopup("Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.", "error");
        return false;
    }
    return true;
}
