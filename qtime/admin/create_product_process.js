document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const fileInput = document.getElementById("newImageInput");
    const addImageTrigger = document.getElementById("triggerImageInput");
    const imageGallery = document.getElementById("imageGallery");
    const addImageBox = document.getElementById("addImageBox");
    const popup = document.getElementById("popupMessage");
  
    // click + to choose image
    addImageTrigger?.addEventListener("click", () => {
      fileInput.click();
    });
  
    // image preview
    fileInput?.addEventListener("change", () => {
      Array.from(fileInput.files).forEach(file => {
        if (!file.type.startsWith("image/")) return; //check file type is image
  
        const reader = new FileReader();// Create a new file reader
        reader.onload = (e) => {
          const imageCard = document.createElement("div");
          imageCard.className = "image-card preview";
  
          const img = document.createElement("img");// Create an img element
          img.src = e.target.result;// Set image source to preview
  
          const deleteIcon = document.createElement("span");// Create a delete button
          deleteIcon.className = "delete-icon";
          deleteIcon.innerHTML = "&times;";
          deleteIcon.addEventListener("click", () => {
            imageCard.remove();
          });
  
          imageCard.append(deleteIcon, img);
          imageGallery.insertBefore(imageCard, addImageBox);
        };
        reader.readAsDataURL(file);
      });
    });
  
    // form submit validation
    form?.addEventListener("submit", function (e) {
      e.preventDefault();
  
      const name = form.querySelector("input[name='name']").value.trim();
      const description = form.querySelector("textarea[name='description']").value.trim();
      const price = form.querySelector("input[name='price']").value.trim();
      const stock = form.querySelector("input[name='stock']").value.trim();
      const brand = form.querySelector("select[name='brand_id']").value;
      const category = form.querySelector("select[name='category_id']").value;
      const colorChecks = form.querySelectorAll("input[name='colors[]']:checked");
      const featureChecks = form.querySelectorAll("input[name='features[]']:checked");
  
      if (!name || !description || !price || !stock || !brand || !category || colorChecks.length === 0 || featureChecks.length === 0) {
        // remind user to fill in all these fields 
        showPopup("Please fill in all fields and select at least one color and feature.", "error");
        return;
      }
  
      const formData = new FormData(form);// Prepare form data (including images)
      // Send form data to server using fetch API
      fetch("create_product_process.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())// Convert server response to JSON
        .then(data => {
          if (data.status === "success") {
            showPopup(data.message, "success");// Show success message
            setTimeout(() => {
              window.location.href = "admin.php?section=product"; //after upload success, redirect to admin product section
            }, 1500);
          } else {
            showPopup(data.message, "error");
          }
        })
        .catch(() => {
          showPopup("Failed to submit. Server error.", "error");
        });
    });
  
    function showPopup(message, type) {
        const popup = document.getElementById("popupMessage");
        popup.innerHTML = message;
        popup.className = `popup-message ${type}`;
        popup.style.display = "block";
        popup.style.opacity = "1"; 
      
        setTimeout(() => {
          popup.style.opacity = "0"; 
          setTimeout(() => {
            popup.style.display = "none";
          }, 300); 
        }, 3000);
      }
    });      