<?php
require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QTIME</title>
    <link rel="stylesheet" href="../css/head.css">
    <script src="../head.js" defer></script> <!-- ✅ JS defer 最安全 -->
</head>

<body data-user="<?php echo isset($_SESSION['user_id']) ? 'logged_in' : 'guest'; ?>">

<!-- ✅ 🛒 购物车侧边栏结构 -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-sidebar-header">
        <h3>Your Cart</h3>
        <button onclick="closeCartSidebar()" class="close-btn">X</button>
    </div>
    <div class="cart-sidebar-content" id="cartSidebarContent">
        <!-- AJAX 动态插入内容 -->
    </div>
</div>

<!-- ✅ 🌟 顶部 Header 区域 -->
<header class="header">
    <!-- 🔍 搜索框（左侧） -->
    <div class="search-container">
        <input type="text" id="search-box" name="search" placeholder="Search product...">
        <img src="../image/search.svg" class="search-icon" alt="Search">
    </div>

    <!-- 🔥 Logo（中间） -->
    <div class="logo">
        <a href="../homepage/home.php">QTIME</a>
    </div>

    <!-- 👤 用户 & 🛒 图标（右侧） -->
    <div class="icons-container">
        <div class="user-menu">
            <img src="../image/user.svg" class="user-icon" id="user-icon" alt="User">
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="../login/login.php" id="login-link">Log In</a>
                <a href="../account/account.php" id="account-link" style="display:none;">My Account</a>
                <a href="../wishlist/wishlist.php" id="wishlist-link" style="display:none;">Wishlist</a>
                <a href="../order/orders.php" id="orders-link" style="display:none;">Order History</a>
                <a href="../login/logout.php" id="logout-link" style="display:none;">Log Out</a>
            </div>
        </div>
        <img src="../image/cart.svg" class="cart-icon" alt="Cart" onclick="triggerCartSidebar()">
    </div>
</header>

<!-- ✅ Cart 专属弹窗结构 -->
<div class="cart-popup-overlay" id="cartPopupOverlay"></div>
<div class="cart-popup" id="cartPopup">
    <h3 id="cartPopupTitle">Title</h3>
    <p id="cartPopupMessage">Message here</p>
    <button class="cart-popup-btn" onclick="closeCartPopup()">OK</button>
</div>


</body>
</html>
