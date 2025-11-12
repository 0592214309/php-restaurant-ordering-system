<?php
// Customer Menu Page with Add to Cart
include '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));
    
    if ($item_id > 0) {
        $_SESSION['cart'][$item_id] = ($_SESSION['cart'][$item_id] ?? 0) + $qty;
        $message = 'Item added to cart!';
    }
}

// Fetch menu items by category
$sql = "SELECT c.category_id, c.category_name FROM categories WHERE is_active = 1 ORDER BY c.display_order, c.category_name";
$cat_result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Restaurant</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .menu-section { margin: 2rem 0; }
        .category-title { color: #4285F4; font-size: 1.5rem; margin: 1.5rem 2rem 1rem 2rem; border-bottom: 2px solid #4285F4; padding-bottom: 0.5rem; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin: 1rem 2rem; }
        .menu-card { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); overflow: hidden; transition: box-shadow 0.2s; }
        .menu-card:hover { box-shadow: 0 4px 16px rgba(66,133,244,0.2); }
        .card-content { padding: 1rem; }
        .card-title { font-weight: bold; color: #333; margin-bottom: 0.5rem; }
        .card-desc { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .card-price { font-size: 1.3rem; color: #4285F4; font-weight: bold; margin-bottom: 1rem; }
        .qty-input { width: 60px; padding: 0.4rem; border: 1px solid #ddd; border-radius: 4px; }
        .add-btn { background: #28a745; color: white; border: none; padding: 0.6rem 1rem; border-radius: 6px; cursor: pointer; font-weight: bold; transition: background 0.2s; }
        .add-btn:hover { background: #218838; }
        .message { background: #d4edda; color: #155724; padding: 1rem; margin: 1rem 2rem; border-radius: 6px; border: 1px solid #c3e6cb; }
        .cart-info { text-align: right; margin-right: 2rem; font-weight: bold; color: #4285F4; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="header">Browse Our Menu 🍽️</div>
    
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="cart-info">
            🛒 Cart Items: <?php echo array_sum($_SESSION['cart']); ?> 
            <a href="cart.php" style="color: #4285F4; text-decoration: underline;">View Cart</a>
        </div>
    <?php endif; ?>

    <?php if ($cat_result && $cat_result->num_rows > 0): ?>
        <?php while ($cat = $cat_result->fetch_assoc()): ?>
            <?php
            // Get items for this category
            $item_sql = "SELECT * FROM menu_items WHERE category_id = ? AND is_available = 1 ORDER BY item_name";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param('i', $cat['category_id']);
            $item_stmt->execute();
            $items = $item_stmt->get_result();
            $item_stmt->close();
            
            if ($items->num_rows > 0):
            ?>
            <div class="menu-section">
                <div class="category-title"><?php echo htmlspecialchars($cat['category_name']); ?></div>
                <div class="menu-grid">
                    <?php while ($item = $items->fetch_assoc()): ?>
                    <div class="menu-card">
                        <div class="card-content">
                            <div class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></div>
                            <div class="card-desc"><?php echo htmlspecialchars($item['description']); ?></div>
                            <div class="card-price">GHS <?php echo number_format($item['price'], 2); ?></div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                    <button type="submit" name="add_to_cart" class="add-btn">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <a href="../auth/login.php" class="add-btn" style="display: block; text-align: center; text-decoration: none;">Login to Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>
</body>
</html>
