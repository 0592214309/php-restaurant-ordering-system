<?php
// Homepage for Restaurant Ordering System
// Shows menu items and lets user place a simple order
// All code is commented for beginners
include 'config/db.php'; // Connect to database
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
    <div class="header">Welcome to Our Restaurant!</div>
    <div class="menu">
    <div style="text-align: center; padding: 1rem; margin-bottom: 1rem;">
        <a href="customer/place_order.php" style="display: inline-block; background: #4285F4; color: white; padding: 0.7rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background 0.2s; box-shadow: 0 2px 6px rgba(66,133,244,0.1);" onmouseover="this.style.background='#1565c0'; this.style.boxShadow='0 4px 12px rgba(66,133,244,0.18)';" onmouseout="this.style.background='#4285F4'; this.style.boxShadow='0 2px 6px rgba(66,133,244,0.1)';">
            🛒 Place an Order
        </a>
    </div>
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
                echo '<button class="order-btn" onclick="showOrderMessage(\'' . addslashes($row['item_name']) . '\')">Order</button>';
                echo '</div>';
            }
        } else {
            echo '<p>No menu items found. Please check back later!</p>';
        }
        ?>
    </div>
</body>
</html>
