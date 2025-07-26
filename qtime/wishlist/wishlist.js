// ✅ Wishlist Remove Button Logic with Custom Popup

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".wishlist-remove-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const productId = this.dataset.productId;
      if (!productId) return;

      showWishlistPopup("Confirm Removal", "Are you sure you want to remove this product?", () => {
        fetch("wishlist_remove_ajax.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ product_id: productId })
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              this.closest(".wishlist-item").remove();
              showWishlistPopup("Removed", "This product has been removed from your wishlist.");
            } else {
              showWishlistPopup("Error", data.message || "Failed to remove item.");
            }
          })
          .catch(() => {
            showWishlistPopup("Error", "Unable to connect to server.");
          });
      });
    });
  });
});

// ✅ 專屬 Wishlist 彈窗控制
function showWishlistPopup(title, message, callback = null) {
  document.getElementById("wishlistPopupTitle").textContent = title;
  document.getElementById("wishlistPopupMessage").textContent = message;
  document.getElementById("wishlistPopupOverlay").style.display = "block";
  document.getElementById("wishlistPopup").style.display = "block";

  const okBtn = document.getElementById("wishlistPopupBtn");
  okBtn.onclick = function () {
    closeWishlistPopup();
    if (typeof callback === "function") callback();
  };
}

function closeWishlistPopup() {
  document.getElementById("wishlistPopupOverlay").style.display = "none";
  document.getElementById("wishlistPopup").style.display = "none";
}
