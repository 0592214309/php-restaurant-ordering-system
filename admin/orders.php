<?php
// Admin Orders Management
include '../config/db.php';
include 'verify_admin.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $allowed = ['pending','confirmed','preparing','delivered','cancelled'];
    if (in_array($new_status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param('si', $new_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch orders with user info
$sql = "SELECT o.order_id, o.user_id, o.customer_name, o.customer_phone, o.total_amount, o.status, o.order_date, u.full_name AS user_name, u.email AS user_email
        FROM orders o LEFT JOIN users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container { max-width: 1100px; margin: 2rem auto; padding: 1rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f7fb; color: #333; }
        .status { padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <h1>Orders</h1>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?>
                                <?php if ($order['user_id']): ?>
                                    <div style="font-size:0.85rem;color:#666;">(<?php echo htmlspecialchars($order['user_name'] ?? $order['user_email']); ?>)</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                            <td>GHS <?php echo number_format($order['total_amount'],2); ?></td>
                            <td><span class="status"><?php echo htmlspecialchars($order['status']); ?></span></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo ($order['status']=='pending')?'selected':''; ?>>pending</option>
                                        <option value="confirmed" <?php echo ($order['status']=='confirmed')?'selected':''; ?>>confirmed</option>
                                        <option value="preparing" <?php echo ($order['status']=='preparing')?'selected':''; ?>>preparing</option>
                                        <option value="delivered" <?php echo ($order['status']=='delivered')?'selected':''; ?>>delivered</option>
                                        <option value="cancelled" <?php echo ($order['status']=='cancelled')?'selected':''; ?>>cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="submit-btn">Update</button>
                                </form>
                                <a href="order_detail.php?id=<?php echo $order['order_id']; ?>" class="action-btn">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
