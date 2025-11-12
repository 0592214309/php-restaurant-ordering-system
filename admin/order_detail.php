<?php
// Order detail view for admin
include '../config/db.php';
include 'verify_admin.php';

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) die('Invalid order id');

// Get order
$stmt = $conn->prepare("SELECT o.*, u.full_name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$order) die('Order not found');

// Get items
$stmt = $conn->prepare("SELECT oi.*, mi.item_name FROM order_items oi JOIN menu_items mi ON oi.item_id = mi.item_id WHERE oi.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order #<?php echo $order['order_id']; ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.container{max-width:800px;margin:2rem auto;padding:1rem}
.card{background:white;border-radius:8px;padding:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:0.5rem;border-bottom:1px solid #eee;text-align:left}
</style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h1>Order #<?php echo $order['order_id']; ?></h1>
    <div class="card">
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> <?php if ($order['user_name']) echo '(' . htmlspecialchars($order['user_name']) . ')'; ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
        <h3>Items</h3>
        <table class="table">
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php while($it = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($it['item_name']); ?></td>
                    <td><?php echo $it['quantity']; ?></td>
                    <td>GHS <?php echo number_format($it['price_at_order'],2); ?></td>
                    <td>GHS <?php echo number_format($it['subtotal'],2); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <p style="text-align:right;font-weight:bold">Total: GHS <?php echo number_format($order['total_amount'],2); ?></p>
    </div>
</div>
</body>
</html>
