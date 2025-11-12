<?php
// Homepage for Restaurant Ordering System - Phase 2
// Shows menu items from database
// All code is commented for beginners
// --- DEBUG: Show errors ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/db.php'; // Connect to database

// --- DEBUG: Session info ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo '<div style="background:#fff3cd;color:#856404;padding:0.5rem 1rem;margin:1rem;border-radius:6px;font-size:0.95rem;">';
echo '<strong>DEBUG:</strong> Session user_id: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'none');
echo ' | DB connection: ' . ($conn ? 'OK' : 'FAIL');
echo '</div>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Ordering System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>
</head>
<body>
    <!-- Include Navigation Bar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="header">Welcome to Our Restaurant! 🍽️</div>
    
    <!-- "Place Order" button for guests (hidden for logged-in users) -->
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])):
    ?>
    <div style="text-align: center; padding: 1rem; margin-bottom: 1rem; background: white; margin: 1rem 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(66,133,244,0.1);">
        <p style="color: #333; margin: 0 0 1rem 0;">Ready to order? Login or register to place your order!</p>
        <a href="auth/login.php" style="display: inline-block; background: #4285F4; color: white; padding: 0.7rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold; margin-right: 1rem;">Login</a>
        <a href="auth/register.php" style="display: inline-block; background: #28a745; color: white; padding: 0.7rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold;">Register</a>
    </div>
    <?php endif; ?>
    
    <!-- Menu Items Display -->
    <div class="menu">
        <?php
        // Get menu items from database
        $sql = "SELECT m.item_id, m.item_name, m.description, m.price, c.category_name FROM menu_items m JOIN categories c ON m.category_id = c.category_id WHERE m.is_available = 1 ORDER BY c.display_order, m.item_name";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="menu-card">';
                echo '<h3>' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '<p><strong>Category:</strong> ' . htmlspecialchars($row['category_name']) . '</p>';
                echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                echo '<div class="price">GHS ' . number_format($row['price'], 2) . '</div>';
                
                // If user is logged in, show "Order" button; otherwise show "Login to Order"
                if (isset($_SESSION['user_id'])) {
                    echo '<button class="order-btn" onclick="showOrderMessage(\'' . addslashes($row['item_name']) . '\')">Order</button>';
                } else {
                    echo '<a href="auth/login.php" class="order-btn" style="display: inline-block; text-align: center;">Login to Order</a>';
                }
                echo '</div>';
            }
        } else {
            echo '<p style="text-align: center; grid-column: 1 / -1;">No menu items found. Please check back later!</p>';
        }
        ?>
    </div>
</body>
</html>
