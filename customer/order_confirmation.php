<?php
// Order Confirmation Page
include '../config/db.php';
include '../auth/verify.php';

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    die('Invalid order ID.');
}

// Fetch order (verify belongs to logged-in user)
$sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die('Order not found.');
}

// Fetch order items
$items_sql = "SELECT oi.*, mi.item_name FROM order_items oi JOIN menu_items mi ON oi.item_id = mi.item_id WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result();
$items_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container { max-width: 600px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .success-msg { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; text-align: center; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.8rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f5f7fb; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <h1>Order Confirmed! ✓</h1>
        <div class="success-msg">
            Thank you for your order. Order #<?php echo $order['order_id']; ?> has been placed successfully.
        </div>
        
        <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        <p><strong>Delivery Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
        
        <h3>Items Ordered</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>GHS <?php echo number_format($item['price_at_order'], 2); ?></td>
                    <td>GHS <?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <p class="total">Total: GHS <?php echo number_format($order['total_amount'], 2); ?></p>
        
        <p><a href="my_orders.php">View All Orders</a> | <a href="menu_cart.php">Continue Shopping</a></p>
    </div>
</body>
</html>
