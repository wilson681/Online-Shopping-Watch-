const statusForm = document.getElementById('updateStatusForm');

if (statusForm) {
  statusForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const orderId = document.querySelector('input[name="order_id"]').value;
    const formData = new FormData(this);

    fetch(`update_status.php?action=update_status&order_id=${orderId}`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(result => {
        if (result.status === 'success') {
            showStatusPopup(result.message, 'success');
            setTimeout(() => {
                window.location.href = 'admin.php?section=order';
            }, 2000);
        } else {
            showStatusPopup(result.message, 'error');
        }
    })
    .catch(() => showStatusPopup('Failed to update status.', 'error'));
  });
}

function showStatusPopup(message, type = 'success') {
    let popup = type === 'success' ? document.getElementById('statusSuccess') : document.getElementById('statusError');
    if (!popup) return;

    popup.textContent = message;
    popup.classList.add('show');

    setTimeout(() => {
        popup.classList.remove('show');
    }, 3000);
}
