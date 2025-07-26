function showPopup(message) {
    const popup = document.getElementById("custom-popup");
    document.getElementById("popup-message").innerText = message;
    popup.style.display = "block";
}

function closePopup() {
    document.getElementById("custom-popup").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const saveButton = document.querySelector(".save-btn");

    function enableEdit(field) {
        document.getElementById(field).disabled = false;
        saveButton.style.display = "block";
    }

    function validateFields() {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();

        let missingFields = [];

        if (!name) missingFields.push("Name");
        if (!email) missingFields.push("Email");
        if (!phone) missingFields.push("Phone");

        if (missingFields.length > 0) {
            showPopup(`⚠️ Please fill in: ${missingFields.join(", ")}`);
            return false;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            showPopup("❌ Invalid email format!");
            return false;
        }

        const phonePattern = /^[0-9]{10,11}$/;
        if (!phonePattern.test(phone)) {
            showPopup("❌ Invalid phone number! Must be 10-11 digits.");
            return false;
        }

        return true;
    }
    

    saveButton.addEventListener("click", function (event) {
        event.preventDefault();

        if (!validateFields()) {
            return;
        }

        const formData = new FormData();
        formData.append("name", document.getElementById("name").value);
        formData.append("email", document.getElementById("email").value);
        formData.append("phone", document.getElementById("phone").value);
        formData.append("dob", document.getElementById("dob").value);

        fetch("update_account.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopup("✅ Profile updated successfully!");
                saveButton.style.display = "none";
                document.querySelectorAll(".account-info input").forEach(input => input.disabled = true);
            } else {
                showPopup(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            showPopup("❌ Something went wrong! Please try again.");
        });
    });

    window.enableEdit = enableEdit;
    window.closePopup = closePopup;
});



document.addEventListener("DOMContentLoaded", function () {
    const profileUpload = document.getElementById("profileUpload");
    const profileImage = document.getElementById("profileImage");

    profileUpload.addEventListener("change", function () {
        const file = this.files[0];

        if (file) {
            const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
            if (!allowedTypes.includes(file.type)) {
                showPopup("❌ Only JPG, JPEG, and PNG files are allowed!");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                profileImage.src = e.target.result; // ✅ **即时预览**
            };
            reader.readAsDataURL(file);

            uploadProfilePicture(file);
        }
    });

    function uploadProfilePicture(file) {
        const formData = new FormData();
        formData.append("profile_picture", file);

        fetch("update_profile_picture.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ✅ **更新头像 & 强制刷新**
                profileImage.src = data.new_image_url + "?t=" + new Date().getTime();

                // ✅ **弹出成功提示**
                showPopup("✅ Profile picture updated successfully!");
            } else {
                showPopup(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            showPopup("❌ Something went wrong!");
        });
    }
});



