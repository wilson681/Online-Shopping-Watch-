document.addEventListener("DOMContentLoaded", function() {
// all console logs are for debugging purposes
  let deleteUserId = null;
  let deleteLink = null;

  const modal = document.getElementById("deleteModal");
  const confirmDeleteBtn = document.getElementById("confirmDelete");
  const cancelDeleteBtn = document.getElementById("cancelDelete");
  const modalMessage = document.getElementById("modalMessage"); //display username

  function showPopup(message, type = 'success') {
    let popup = type === 'success' ? document.getElementById('successMsg') : document.getElementById('errorMsg');
    
    if (!popup) return;

    popup.textContent = message;
    popup.classList.add('show-popup');

    setTimeout(() => {
      popup.classList.remove('show-popup');
    }, 3000);
  }

  document.addEventListener("click", function(e) {
    const target = e.target;
    if (target.classList.contains("delete-btn")) {
      e.preventDefault();
      deleteLink = target;
      deleteUserId = target.getAttribute("href").split('user_id=')[1]; // get user_id from href
      const username = target.getAttribute("data-username"); // get username from data-username

      if (modal && modalMessage) {
        modalMessage.textContent = `Confirm Delete USER : ${username}?`;
        modal.style.display = "block";
      }
    }
  });

  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener("click", function() {
      if (deleteLink && deleteUserId) {
        const url = `delete_user.php?user_id=${deleteUserId}`;
        
        fetch(url, { method: "GET" })
          .then(response => response.json())
          .then(data => {
            console.log("rollback data:", data);
            if (data.success) {
              showPopup("Delete success", "success");
              const row = deleteLink.closest("tr");
              if (row) {
                row.remove(); 
              }
            } else {
              showPopup("Delete unsuccessful, please try again", "error");
            }
            if (modal) modal.style.display = "none";
            deleteLink = null;
            deleteUserId = null;
          })
          .catch(error => {
            console.error("Delete error:", error);
            showPopup("Delete error, please try again later", "error");
            if (modal) modal.style.display = "none";
            deleteLink = null;
            deleteUserId = null;
          });
      }
    });
  }

  // cancel button 
  if (cancelDeleteBtn) {
    cancelDeleteBtn.addEventListener("click", function() {
      if (modal) modal.style.display = "none";
      deleteLink = null;
      deleteUserId = null;
    });
  }

  window.addEventListener("click", function(e) {
    if (modal && e.target === modal) {
      modal.style.display = "none";
      deleteLink = null;
      deleteUserId = null;
    }
  });
});
