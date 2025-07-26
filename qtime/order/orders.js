function showNotificationModal(title, message, redirectUrl = null) {
    const modal = document.getElementById('notification-modal');
    const modalTitle = document.getElementById('notification-title');
    const modalMessage = document.getElementById('notification-message');
    const closeBtn = document.getElementById('notification-close-btn');

    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modal.style.display = 'flex';

    closeBtn.onclick = () => {
        modal.style.display = 'none';
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else {
            const currentUrl = new URL(window.location.href);
            window.location.href = currentUrl.toString();
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const confirmModal = document.getElementById('confirmation-modal');
    const confirmBtnModal = document.getElementById('confirm-cancel-btn');
    const cancelBtnModal = document.getElementById('cancel-cancel-btn');
    let currentCancelForm = null;
    const cancelButtons = document.querySelectorAll('.cancel-btn'); // 获取取消按钮


    cancelBtnModal.addEventListener('click', () => {
        confirmModal.style.display = 'none';
        currentCancelForm = null;
    });

    // 取消确认弹窗的确认按钮
    confirmBtnModal.addEventListener('click', () => {
        if (currentCancelForm) {
            currentCancelForm.querySelector('[name="cancel_order_item"]').click();
        }
        confirmModal.style.display = 'none';
        currentCancelForm = null;
    });

    // 为所有取消按钮添加事件监听器
    cancelButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const reasonSelect = this.parentElement.querySelector('select');
            const reason = reasonSelect.value;

            if (!reason) {
                showNotificationModal('Warning', 'Please select a cancellation reason');
            } else {
                currentCancelForm = this.closest('form');
                confirmModal.style.display = 'flex';
            }
        });
    });
});