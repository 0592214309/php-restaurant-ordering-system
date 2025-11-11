<?php
// Navigation Bar Component
// Include this in all pages to show consistent navigation
// Shows different links based on login status

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo/Home Link -->
        <a href="<?php echo $is_logged_in ? '../index.php' : '../index.php'; ?>" class="navbar-brand">🍽️ Restaurant</a>
        
        <div class="navbar-menu">
            <!-- Links for all users -->
            <a href="../index.php" class="nav-link">Home</a>
            <a href="../customer/menu.php" class="nav-link">Menu</a>
            
            <!-- Links for logged-in users -->
            <?php if ($is_logged_in): ?>
                <a href="../customer/my_orders.php" class="nav-link">My Orders</a>
                <a href="../customer/profile.php" class="nav-link">Profile</a>
                <span class="nav-user">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../auth/logout.php" class="nav-link logout-btn">Logout</a>
            <?php else: ?>
                <!-- Links for guests (not logged in) -->
                <a href="../auth/login.php" class="nav-link">Login</a>
                <a href="../auth/register.php" class="nav-link register-btn">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
