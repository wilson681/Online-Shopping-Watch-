document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const editBtn = document.getElementById("editButton");
    const submitBtn = document.getElementById("submitButton");
    const cancelBtn = document.getElementById("cancelButton");
    const exitBtn = document.querySelector(".btn-exit");
  
    const inputs = form.querySelectorAll("input[type='text'], input[type='number'], textarea");
    const checkboxes = form.querySelectorAll("input[type='checkbox']");
    const selects = form.querySelectorAll("select");
  
    const fileInput = document.getElementById("newImageInput");
    const addImageBox = document.getElementById("addImageBox");
    const imageGallery = document.getElementById("imageGallery");
    const addImageTrigger = document.getElementById("triggerImageInput");
    let originalValues = {};
    let isEditMode = false;
  
    // Activate Edit Mode
    editBtn?.addEventListener("click", () => {
      isEditMode = true;
  
      // Save values before editing
      inputs.forEach(input => {
        originalValues[input.name] = input.value;
        input.removeAttribute("readonly");
      });
  
      checkboxes.forEach(cb => cb.disabled = false);
      selects.forEach(sel => sel.disabled = false);
  
      editBtn.style.display = "none";
      submitBtn.style.display = "inline-block";
      cancelBtn.style.display = "inline-block";
      exitBtn.style.display = "none";
  
      // Show add image slot
      addImageBox.style.display = "flex";
  
      // Show delete icons on existing images
      document.querySelectorAll(".delete-icon").forEach(icon => {
        icon.style.display = "flex";
      });
    });
  
    // Cancel Editing
    cancelBtn?.addEventListener("click", () => {
      isEditMode = false;
  
      inputs.forEach(input => {
        input.value = originalValues[input.name] || "";
        input.setAttribute("readonly", true);
      });
  
      checkboxes.forEach(cb => cb.disabled = true);
      selects.forEach(sel => sel.disabled = true);
  
      editBtn.style.display = "inline-block";
      submitBtn.style.display = "none";
      cancelBtn.style.display = "none";
      exitBtn.style.display = "inline-block";
  
      addImageBox.style.display = "none";
  
      // Hide delete icons
      document.querySelectorAll(".delete-icon").forEach(icon => {
        icon.style.display = "none";
      });
  
      // Remove newly added previews
      document.querySelectorAll(".image-card.preview").forEach(el => el.remove());
      fileInput.value = ""; // Reset input
    });
  


addImageTrigger?.addEventListener("click", () => {
  if (isEditMode) fileInput.click();
});

  
    // Handle new image preview
    fileInput?.addEventListener("change", () => {
      Array.from(fileInput.files).forEach(file => {
        if (!file.type.startsWith("image/")) return;
  
        const reader = new FileReader();
        reader.onload = (e) => {
          const imageCard = document.createElement("div");
          imageCard.className = "image-card preview";
  
          const img = document.createElement("img");
          img.src = e.target.result;
          img.alt = "Preview";
  
          const deleteIcon = document.createElement("span");
          deleteIcon.className = "delete-icon";
          deleteIcon.innerHTML = "&times;";
          deleteIcon.addEventListener("click", () => imageCard.remove());
  
          imageCard.append(deleteIcon, img);
          imageGallery.insertBefore(imageCard, addImageBox);
        };
        reader.readAsDataURL(file);
      });
    });
  
    // Handle old image deletion (PHP redirect version)
    document.querySelectorAll(".image-card[data-image-id]").forEach(card => {
        const deleteBtn = card.querySelector(".delete-icon");
        if (deleteBtn) {
          deleteBtn.addEventListener("click", () => {
            const imageId = card.dataset.imageId;
            // ✅ 直接跳转删除，无需确认
            window.location.href = `delete_image.php?image_id=${imageId}&product_id=${form.product_id.value}`;
          });
        }
      });
      
  });
  