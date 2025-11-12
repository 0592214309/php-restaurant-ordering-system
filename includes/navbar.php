<?php
// Navigation Bar Component
// Include this in all pages to show consistent navigation
// Shows different links based on login status

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
// Cart count (if available)
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo/Home Link (use absolute path for consistency) -->
        <a href="/restaurant/index.php" class="navbar-brand">🍽️ Restaurant</a>
        
        <div class="navbar-menu">
            <!-- Links for all users -->
            <a href="/restaurant/index.php" class="nav-link">Home</a>
            <a href="/restaurant/customer/menu_cart.php" class="nav-link">Menu</a>

            <!-- Cart (always show, badge if items) -->
            <a href="/restaurant/customer/cart.php" class="nav-link">🛒 Cart<?php if ($cart_count > 0): ?><span class="cart-badge"><?php echo $cart_count; ?></span><?php endif; ?></a>

            <!-- Links for logged-in users -->
            <?php if ($is_logged_in): ?>
                <a href="/restaurant/customer/my_orders.php" class="nav-link">My Orders</a>
                <a href="/restaurant/customer/profile.php" class="nav-link">Profile</a>
                <span class="nav-user">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="/restaurant/auth/logout.php" class="nav-link logout-btn">Logout</a>
            <?php else: ?>
                <!-- Links for guests (not logged in) -->
                <a href="/restaurant/auth/login.php" class="nav-link">Login</a>
                <a href="/restaurant/auth/register.php" class="nav-link register-btn">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
