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

document.addEventListener("DOMContentLoaded", function () {
    // ✅ 如果有错误，显示弹窗
    if (errorMessage.trim() !== "") {
        showError(errorMessage);
    }
});

// 🔥 显示美化后的错误弹窗
function showError(message) {
    let modal = document.createElement("div");
    modal.classList.add("modal");
    modal.innerHTML = `
        <div class="modal-content">
            <h3>Oops! Something went wrong</h3>
            <p>${message}</p>
            <button onclick="closeModal()">Try Again</button>
        </div>
    `;
    document.body.appendChild(modal);
}

// 🔥 关闭弹窗
function closeModal() {
    let modal = document.querySelector(".modal");
    if (modal) modal.remove();
}