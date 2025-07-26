// ✅ 等待 DOM 完全加载后执行
window.addEventListener("DOMContentLoaded", function () {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardForm = document.querySelector(".card-form");
    const cardWidget = document.querySelector(".card-widget");

    function toggleCardDisplay(value) {
      if (value === "card") {
        cardForm.classList.add("active");
        if (cardWidget) cardWidget.style.display = "block";
      } else {
        cardForm.classList.remove("active");
        if (cardWidget) cardWidget.style.display = "none";
      }
    }

    const selected = document.querySelector('input[name="payment_method"]:checked');
    toggleCardDisplay(selected ? selected.value : "");

    paymentRadios.forEach(radio => {
      radio.addEventListener("change", function () {
        toggleCardDisplay(this.value);
      });
    });

    const cardNumberInput = document.getElementById("card-number");
    const cardNameInput = document.getElementById("card-name");
    const cardExpiryInput = document.getElementById("card-expiry");
    const cardCVVInput = document.getElementById("card-cvv");

    const cardNumberDisplay = document.getElementById("display-card-number");
    const cardNameDisplay = document.getElementById("display-card-name");
    const cardExpiryDisplay = document.getElementById("display-card-expiry");
    const cardCVVDisplay = document.getElementById("display-card-cvv");

    const cardInner = document.getElementById("card-inner");

    if (cardNumberInput) {
      cardNumberInput.addEventListener("input", function () {
        let digitsOnly = this.value.replace(/\D/g, "").substring(0, 16); // 限制最多 16 位数字
        const formatted = digitsOnly.replace(/(.{4})/g, "$1 ").trim();   // 每 4 位加空格
        this.value = formatted;

        cardNumberDisplay.textContent = formatted || "#### #### #### ####";
      });
    }

    if (cardNameInput) {
      cardNameInput.addEventListener("input", function () {
        cardNameDisplay.textContent = this.value.trim() || "FULL NAME";
      });
    }

    if (cardExpiryInput) {
      cardExpiryInput.addEventListener("input", function () {
        cardExpiryDisplay.textContent = this.value.trim() || "MM/YY";
      });
    }

    if (cardCVVInput) {
      cardCVVInput.addEventListener("input", function () {
        cardCVVDisplay.textContent = this.value.trim() || "###";
      });

      cardCVVInput.addEventListener("focus", function () {
        cardInner.style.transform = "rotateY(180deg)";
      });

      cardCVVInput.addEventListener("blur", function () {
        cardInner.style.transform = "rotateY(0deg)";
      });
    }

    // ✅ Place Order 按钮逻辑
    const placeOrderBtn = document.querySelector(".place-order-btn");
    const loadingOverlay = document.getElementById("loadingOverlay");
    const successPopup = document.getElementById("orderSuccessPopup");
    
    if (placeOrderBtn) {
      placeOrderBtn.addEventListener("click", function () {
        if (loadingOverlay) loadingOverlay.style.display = "flex";
    
        fetch("place_order.php")
          .then(res => res.json())
          .then(data => {
            // ✅ 不管成功失败，都假装等一等
            setTimeout(() => {
              if (loadingOverlay) loadingOverlay.style.display = "none";
    
              if (data.success) {
                if (successPopup) successPopup.style.display = "flex";
              } else {
                alert(data.message || "Order failed.");
              }
            }, 3000); // 假装 loading 1.2 秒
          })
          .catch(err => {
            if (loadingOverlay) loadingOverlay.style.display = "none";
            alert("Network error: " + err);
          });
      });
    }
    
});
