<?php
// Place Order from Cart (Phase 4)
include '../config/db.php';
include '../auth/verify.php'; // Require login

// Initialize cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_phone = trim($_POST['delivery_phone'] ?? '');
    
    if (empty($delivery_phone)) {
        $message = 'Please enter delivery phone number.';
    } else {
        // Get user info
        $user_id = $_SESSION['user_id'];
        $user_sql = "SELECT full_name FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param('i', $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result()->fetch_assoc();
        $user_stmt->close();
        
        $customer_name = $user_result['full_name'];
        
        // Calculate total from cart
        $item_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        $total_sql = "SELECT SUM(price * ?) as total FROM menu_items WHERE item_id IN ($item_ids)";
        
        // Get cart items with prices
        $items_sql = "SELECT item_id, price FROM menu_items WHERE item_id IN ($item_ids)";
        $items_result = $conn->query($items_sql);
        $total = 0;
        $cart_items = [];
        while ($row = $items_result->fetch_assoc()) {
            $qty = $_SESSION['cart'][$row['item_id']];
            $subtotal = $row['price'] * $qty;
            $total += $subtotal;
            $cart_items[] = ['item_id' => $row['item_id'], 'price' => $row['price'], 'qty' => $qty, 'subtotal' => $subtotal];
        }
        
        // Insert order
        $order_sql = "INSERT INTO orders (user_id, customer_name, customer_phone, total_amount, status) VALUES (?, ?, ?, ?, 'pending')";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param('issd', $user_id, $customer_name, $delivery_phone, $total);
        
        if ($order_stmt->execute()) {
            $order_id = $order_stmt->insert_id;
            $order_stmt->close();
            
            // Insert order items
            $item_sql = "INSERT INTO order_items (order_id, item_id, quantity, price_at_order, subtotal) VALUES (?, ?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            
            $success = true;
            foreach ($cart_items as $item) {
                $item_stmt->bind_param('iiidd', $order_id, $item['item_id'], $item['qty'], $item['price'], $item['subtotal']);
                if (!$item_stmt->execute()) {
                    $success = false;
                    break;
                }
            }
            $item_stmt->close();
            
            if ($success) {
                $_SESSION['cart'] = [];
                header('Location: order_confirmation.php?id=' . $order_id);
                exit;
            } else {
                $message = 'Error processing order items.';
            }
        } else {
            $message = 'Error placing order.';
        }
    }
}

// Calculate cart total for display
$item_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$items_sql = "SELECT item_id, price FROM menu_items WHERE item_id IN ($item_ids)";
$items_result = $conn->query($items_sql);
$total = 0;
while ($row = $items_result->fetch_assoc()) {
    $qty = $_SESSION['cart'][$row['item_id']];
    $total += $row['price'] * $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .checkout-container { max-width: 600px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: bold; }
        .form-group input { width: 100%; padding: 0.7rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
        .submit-btn { background: #28a745; color: white; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 6px; background: #ffcdd2; color: #c62828; }
        .order-summary { background: #f5f7fb; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="checkout-container">
        <h1>Checkout</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="order-summary">
            <p><strong>Order Total: GHS <?php echo number_format($total, 2); ?></strong></p>
            <p><a href="cart.php">← Back to Cart</a></p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="delivery_phone">Delivery Phone Number *</label>
                <input type="text" id="delivery_phone" name="delivery_phone" placeholder="0240000000" required>
            </div>
            <button type="submit" class="submit-btn">Place Order</button>
        </form>
    </div>
</body>
</html>
