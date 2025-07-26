function confirmDelete(productId) {
    const modal = document.getElementById("deleteModal");
    const confirmBtn = document.getElementById("confirmDelete");
  
    // display modal
    modal.style.display = "block";
  
    //click confirm button
    confirmBtn.onclick = function () {
      window.location.href = `delete_product.php?product_id=${productId}`;
    };
  
    // click cancel button
    document.getElementById("cancelDelete").onclick = function () {
      modal.style.display = "none";
    };
  
  }
  