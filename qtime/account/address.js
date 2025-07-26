document.addEventListener("DOMContentLoaded", function () {
    const saveButton = document.getElementById("save-address-btn");

    if (saveButton) {
        saveButton.addEventListener("click", function (event) {
            event.preventDefault();

            if (!validateAddressFields()) {
                return;
            }

            const formData = new FormData();
            formData.append("action", "update_address"); // ✅ 传递 action 参数
            formData.append("address", document.getElementById("display-address").value);
            formData.append("postcode", document.getElementById("display-postcode").value);
            formData.append("state", document.getElementById("state").value);

            fetch("address_ajax.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPopup("✅ Address updated successfully!");
                    saveButton.style.display = "none";

                    document.getElementById("display-address").disabled = true;
                    document.getElementById("display-postcode").disabled = true;
                    document.getElementById("state").disabled = true;
                } else {
                    showPopup(data.message);
                }
            })
            .catch(() => {
                showPopup("❌ Something went wrong!");
            });
});
}
});

// ✅ 让 `Edit` 按钮可用
window.enableEditing = function () {
    const addressInput = document.getElementById("display-address");
    const postcodeInput = document.getElementById("display-postcode");
    const stateSelect = document.getElementById("state");
    const saveButton = document.getElementById("save-address-btn");

    if (!addressInput || !postcodeInput || !stateSelect || !saveButton) {
        console.error("❌ Error: Some elements are missing!");
        return;
    }

    addressInput.removeAttribute("disabled");
    postcodeInput.removeAttribute("disabled");
    stateSelect.removeAttribute("disabled");

    addressInput.style.cursor = "text";
    postcodeInput.style.cursor = "text";
    stateSelect.style.cursor = "pointer";

    saveButton.style.display = "inline-block";
};

// ✅ 地址字段验证
function validateAddressFields() {
    const address = document.getElementById("display-address").value.trim();
    const postcode = document.getElementById("display-postcode").value.trim();
    const state = document.getElementById("state").value;

    if (!address || !postcode || !state) {
        showPopup("⚠️ Please fill in all fields!");
        return false;
    }
    return true;
}

// ✅ 自定义弹窗
function showPopup(message) {
    const popup = document.getElementById("custom-popup");
    document.getElementById("popup-message").innerText = message;
    popup.style.display = "block";
}

// ✅ 关闭弹窗
function closePopup() {
    document.getElementById("custom-popup").style.display = "none";
}
