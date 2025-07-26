document.addEventListener("DOMContentLoaded", function () {
    const userIcon = document.getElementById("user-icon");
    const dropdownMenu = document.getElementById("dropdown-menu");
    const loginLink = document.getElementById("login-link");
    const accountLink = document.getElementById("account-link");
    const logoutLink = document.getElementById("logout-link");
    const wishlistLink = document.getElementById("wishlist-link");
    const ordersLink = document.getElementById("orders-link");

    const isLoggedIn = document.body.dataset.user === "logged_in";

    if (isLoggedIn) {
        loginLink.style.display = "none";
        accountLink.style.display = "block";
        logoutLink.style.display = "block";
        wishlistLink.style.display = "block";
        ordersLink.style.display = "block";
    } else {
        loginLink.style.display = "block";
        accountLink.style.display = "none";
        logoutLink.style.display = "none";
        wishlistLink.style.display = "none";
        ordersLink.style.display = "none";
    }

    let hideTimeout;
    function showMenu() {
        clearTimeout(hideTimeout);
        dropdownMenu.style.display = "block";
        dropdownMenu.style.opacity = "1";
        dropdownMenu.style.visibility = "visible";
    }
    function hideMenu() {
        hideTimeout = setTimeout(() => {
            dropdownMenu.style.opacity = "0";
            dropdownMenu.style.visibility = "hidden";
            setTimeout(() => {
                dropdownMenu.style.display = "none";
            }, 300);
        }, 500);
    }
    userIcon.addEventListener("mouseenter", showMenu);
    dropdownMenu.addEventListener("mouseenter", showMenu);
    userIcon.addEventListener("mouseleave", hideMenu);
    dropdownMenu.addEventListener("mouseleave", hideMenu);

    // Search
    const searchBox = document.getElementById("search-box");
    const searchImage = document.querySelector(".search-icon");

    searchBox.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            performSearch();
        }
    });
    searchImage.addEventListener("click", performSearch);

    function performSearch() {
        const query = searchBox.value.trim();
        if (query) {
            window.location.href = `../homepage/home.php?search=${encodeURIComponent(query)}`;
        }
    }

    // Cart icon click
    const cartIcon = document.querySelector(".cart-icon");
    if (cartIcon) {
        cartIcon.addEventListener("click", function () {
            triggerCartSidebar();
        });
    }

    // Quantity change inside sidebar
    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("cart-qty-input")) {
            const cartItem = e.target.closest(".cart-item");
            const unitPrice = parseFloat(cartItem.querySelector(".cart-item-price").textContent.replace(/[^\d.]/g, ""));
            const newQty = parseInt(e.target.value);
            const maxQty = parseInt(e.target.getAttribute("data-stock"));
            const productId = parseInt(cartItem.dataset.productId);

            if (newQty < 1) {
                e.target.value = 1;
                showCartPopup("Quantity Too Low", "Minimum quantity is 1.");
                return;
            }

            if (newQty > maxQty) {
                e.target.value = maxQty;
                showCartPopup("Out of Stock", `Only ${maxQty} item(s) in stock.`);
                return;
            }

            const newSubtotal = unitPrice * newQty;
            cartItem.querySelector(".cart-item-subtotal").textContent = `Subtotal: RM ${newSubtotal.toFixed(2)}`;
            updateCartTotal();

            fetch("../cart/update_cart_quantity_ajax.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product_id: productId, quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    showCartPopup("Update Failed", "Unable to update cart item.");
                }
            })
            .catch(() => {
                showCartPopup("Error", "Server error while updating cart.");
            });
        }
    });

    // Remove item
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-btn")) {
            const cartItem = e.target.closest(".cart-item");
            const productId = cartItem.dataset.productId;

            fetch("../cart/remove_from_cart_ajax.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cartItem.remove();
                    updateCartTotal();
                } else {
                    showCartPopup("Remove Failed", data.message || "Failed to remove item.");
                }
            })
            .catch(() => {
                showCartPopup("Error", "Server error while removing item.");
            });
        }
    });

    // Validate before navigating to cart/checkout
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("view-cart-btn") || e.target.classList.contains("checkout-btn")) {
            e.preventDefault();
            const url = e.target.classList.contains("view-cart-btn") ? "../cart/cart.php" : "../cart/checkout.php";

            fetch("../cart/cart_validate_ajax.php")
                .then(res => res.json())
                .then(data => {
                    if (data.valid) {
                        window.location.href = url;
                    } else {
                        showCartPopup("Cart Error", data.message || "One or more items exceed stock.");
                    }
                })
                .catch(() => {
                    showCartPopup("Error", "Failed to validate cart.");
                });
        }
    });

    function updateCartTotal() {
        let total = 0;
        document.querySelectorAll(".cart-item").forEach(item => {
            const qty = parseInt(item.querySelector(".cart-qty-input").value);
            const price = parseFloat(item.querySelector(".cart-item-price").textContent.replace(/[^\d.]/g, ""));
            total += qty * price;
        });

        const totalDiv = document.querySelector(".cart-total");
        if (totalDiv) {
            totalDiv.innerHTML = `<strong>Total: RM ${total.toFixed(2)}</strong>`;
        }
    }
});

function triggerCartSidebar() {
    const sidebar = document.getElementById("cartSidebar");
    sidebar.classList.add("show");

    fetch("../cart/cart_sidebar_ajax.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("cartSidebarContent").innerHTML = html;
        })
        .catch(() => {
            document.getElementById("cartSidebarContent").innerHTML = "<p>Failed to load cart.</p>";
        });
}

function closeCartSidebar() {
    document.getElementById("cartSidebar").classList.remove("show");
}

function showCartPopup(title, message, callback = null) {
    document.getElementById("cartPopupTitle").textContent = title;
    document.getElementById("cartPopupMessage").textContent = message;
    document.getElementById("cartPopupOverlay").style.display = "block";
    document.getElementById("cartPopup").style.display = "block";

    const okBtn = document.querySelector(".cart-popup-btn");
    okBtn.onclick = function () {
        closeCartPopup();
        if (typeof callback === "function") callback();
    };
}

function closeCartPopup() {
    document.getElementById("cartPopupOverlay").style.display = "none";
    document.getElementById("cartPopup").style.display = "none";
}
