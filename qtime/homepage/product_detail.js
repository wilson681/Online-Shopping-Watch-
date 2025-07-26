document.addEventListener("DOMContentLoaded", function () {
    // ✅ 图片轮播功能
    let currentImageIndex = 0;
    const images = window.productImages || [];
    const imgTag = document.getElementById("product-image");

    document.getElementById("prev-btn").addEventListener("click", () => {
        if (images.length > 0) {
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            imgTag.src = images[currentImageIndex];
        }
    });

    document.getElementById("next-btn").addEventListener("click", () => {
        if (images.length > 0) {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            imgTag.src = images[currentImageIndex];
        }
    });
    

    // ✅ 变量准备（注意：必须放在 DOMContentLoaded 内）
    const addToCartBtn = document.getElementById("add-to-cart");
    const quantityInput = document.getElementById("quantity");
    const stockAvailable = parseInt(quantityInput?.getAttribute("max")) || 0;
    const productId = parseInt(new URLSearchParams(window.location.search).get("product_id"));
    const userId = window.userId || 0;
    const wishlistBtn = document.getElementById("wishlist-btn");

    // ✅ Add to Cart 事件监听
    if (addToCartBtn) {
        addToCartBtn.addEventListener("click", function () {
            const quantity = Number(quantityInput.value.trim());
          

            // ✅ 验证数量
            if (isNaN(quantity) || quantity < 1) {
                showProductPopup("Invalid Quantity", "Please enter at least 1.");
                return;
            }

            if (quantity > stockAvailable) {
                showProductPopup("Quantity Exceeded", `Only ${stockAvailable} item(s) available in stock.`);
                return;
            }

            // ✅ 发 AJAX 添加购物车
            fetch("../cart/add_to_cart_ajax.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showProductPopup("Success", "Item added to cart!", () => {
                        if (typeof triggerCartSidebar === "function") {
                            triggerCartSidebar(); // ✅ 成功弹窗关闭后，再弹出购物车
                        }
                    }); 
                } else {
                    showProductPopup("Error", data.message || "Failed to add to cart.");
                }
            })
            .catch(() => {
                showProductPopup("Error", "Server not found or add_to_cart_ajax.php missing.");
            });
        });
    }

    if (wishlistBtn) {
        wishlistBtn.addEventListener("click", function () {
            if (!userId || userId === 0) {
                showProductPopup("Login Required", "You must log in to add to wishlist.");
                return;
            }
    
            const isActive = wishlistBtn.classList.contains("active");
    
            if (!isActive) {
                // ✅ 添加到 wishlist
                fetch("../wishlist/wishlist_ajax.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ product_id: productId, user_id: userId, action: "add" })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        wishlistBtn.classList.add("active");
                        showProductPopup("Sucessful", "Product added to wishlist!");
                    } else {
                        showProductPopup("Error", data.message || "Failed to add to wishlist.");
                    }
                })
                .catch(() => {
                    showProductPopup("Error", "Unable to connect to server.");
                });
            } else {
                // ✅ 弹出确认框取消收藏
                showProductPopup("Remove Wishlist", "Do you want to remove this product from your wishlist?", function () {
                    fetch("../wishlist/wishlist_ajax.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ product_id: productId, user_id: userId, action: "remove" })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            wishlistBtn.classList.remove("active");
                            showProductPopup("Removed", "Product removed from wishlist!");
                        } else {
                            showProductPopup("Error", data.message || "Failed to remove from wishlist.");
                        }
                    })
                    .catch(() => {
                        showProductPopup("Error", "Unable to connect to server.");
                    });
                });
            }
        });
    }
    
});


// ✅ 专属 product_detail 弹窗 - 支持确认操作
function showProductPopup(title, message, callback = null) {
    document.getElementById("productPopupTitle").textContent = title;
    document.getElementById("productPopupMessage").textContent = message;

    // 如果是确认操作，按钮显示 "Yes"，否则显示 "OK"
    const okBtn = document.getElementById("productPopupBtn");
    okBtn.textContent = callback ? "Yes" : "OK";

    document.getElementById("productPopupOverlay").style.display = "block";
    document.getElementById("productPopup").style.display = "block";

    okBtn.onclick = function () {
        closeProductPopup();
        if (typeof callback === "function") {
            callback(); // ✅ 如果有 callback，执行
        }
    };
}


function closeProductPopup() {
    document.getElementById("productPopupOverlay").style.display = "none";
    document.getElementById("productPopup").style.display = "none";
}

