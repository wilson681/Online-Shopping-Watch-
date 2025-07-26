document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
  
    form?.addEventListener("submit", function (e) {
      e.preventDefault(); 
  
      const name = form.querySelector("input[name='name']").value.trim();
      const description = form.querySelector("textarea[name='description']").value.trim();
      const price = form.querySelector("input[name='price']").value.trim();
      const stock = form.querySelector("input[name='stock']").value.trim();
      const colorChecks = form.querySelectorAll("input[name='colors[]']:checked");
      const featureChecks = form.querySelectorAll("input[name='features[]']:checked");
  
      if (!name || !description || !price || !stock || colorChecks.length === 0 || featureChecks.length === 0) {
        showPopup("❌ Please fill in all fields and select at least one color and feature.", "error");
        return;
      }
  
      const formData = new FormData(form);
  
      fetch("update_product.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          showPopup("✅ " + data.message, "success");
          setTimeout(() => {
            window.location.href = "admin.php?section=product";
          }, 1500);
        } else {
          showPopup("❌ " + data.message, "error");
        }
      })
      .catch(() => {
        showPopup("❌ Failed to update. Server error.", "error");
      });
    });
  
    function showPopup(message, type) {
      let popup = document.getElementById("popupMessage");
      if (!popup) {
        popup = document.createElement("div");
        popup.id = "popupMessage";
        document.body.appendChild(popup);
      }
  
      popup.textContent = message;
      popup.className = `popup-message ${type}`;
      popup.style.display = "block";
  
      setTimeout(() => {
        popup.style.display = "none";
      }, 3000);
    }
  });
  