<?php
// My Orders Page - Shows logged-in user's order history
// Only accessible to logged-in users
include '../config/db.php';
include '../auth/verify.php'; // Check if user is logged in

// Get user's orders from database
$user_id = $_SESSION['user_id'];
$orders_sql = "SELECT order_id, customer_name, total_amount, status, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Restaurant</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .orders-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .order-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(66,133,244,0.1);
            border-left: 4px solid #4285F4;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #ddd;
        }
        .order-id {
            font-weight: bold;
            color: #4285F4;
        }
        .order-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-preparing {
            background: #cce5ff;
            color: #004085;
        }
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .detail-item {
            font-size: 0.9rem;
        }
        .detail-label {
            color: #666;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .detail-value {
            color: #333;
            margin-top: 0.2rem;
        }
        .no-orders {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(66,133,244,0.1);
        }
        .btn-view-order {
            background: #4285F4;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-view-order:hover {
            background: #1565c0;
        }
    </style>
</head>
<body>
    <!-- Include Navigation Bar -->
    <?php include '../includes/navbar.php'; ?>
    
    <div class="header">Your Order History</div>
    
    <div class="orders-container">
        <?php
        if ($orders_result && $orders_result->num_rows > 0) {
            while ($order = $orders_result->fetch_assoc()) {
                // Format status for display
                $status = $order['status'];
                $status_class = 'status-' . strtolower($status);
                
                // Get order items for this order
                $items_sql = "SELECT oi.quantity, oi.price_at_order, oi.subtotal, mi.item_name FROM order_items oi JOIN menu_items mi ON oi.item_id = mi.item_id WHERE oi.order_id = ?";
                $items_stmt = $conn->prepare($items_sql);
                $items_stmt->bind_param('i', $order['order_id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                $items_count = $items_result->num_rows;
                
                echo '<div class="order-card">';
                echo '<div class="order-header">';
                echo '<div>';
                echo '<div class="order-id">Order #' . htmlspecialchars($order['order_id']) . '</div>';
                echo '<div style="font-size: 0.9rem; color: #666; margin-top: 0.3rem;">' . date('M d, Y H:i', strtotime($order['order_date'])) . '</div>';
                echo '</div>';
                echo '<span class="order-status ' . $status_class . '">' . ucfirst($status) . '</span>';
                echo '</div>';
                
                echo '<div class="order-details">';
                echo '<div class="detail-item">';
                echo '<div class="detail-label">Items</div>';
                echo '<div class="detail-value">' . $items_count . ' item' . ($items_count != 1 ? 's' : '') . '</div>';
                echo '</div>';
                echo '<div class="detail-item">';
                echo '<div class="detail-label">Total Amount</div>';
                echo '<div class="detail-value" style="color: #4285F4; font-weight: bold;">GHS ' . number_format($order['total_amount'], 2) . '</div>';
                echo '</div>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo '<div class="no-orders">';
            echo '<h3 style="color: #666; margin-top: 0;">No Orders Yet</h3>';
            echo '<p style="color: #999;">You haven\'t placed any orders yet. Start by browsing our menu!</p>';
            echo '<a href="../index.php" style="display: inline-block; background: #4285F4; color: white; padding: 0.7rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 1rem;">Browse Menu</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
