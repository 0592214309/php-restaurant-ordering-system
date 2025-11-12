<?php
// Shopping Cart Page
// Shows items in cart, allows update/remove, and proceed to checkout
include '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add/update/remove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $item_id = intval($_POST['item_id']);
        $qty = max(1, intval($_POST['quantity']));
        $_SESSION['cart'][$item_id] = $qty;
    }
    if (isset($_POST['update_item'])) {
        $item_id = intval($_POST['item_id']);
        $qty = max(1, intval($_POST['quantity']));
        $_SESSION['cart'][$item_id] = $qty;
    }
    if (isset($_POST['remove_item'])) {
        $item_id = intval($_POST['item_id']);
        unset($_SESSION['cart'][$item_id]);
    }
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Fetch menu items in cart
$cart_items = [];
$total = 0;
if ($_SESSION['cart']) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql = "SELECT item_id, item_name, price FROM menu_items WHERE item_id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['item_id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $cart_items[] = $row;
        $total += $row['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .cart-container { max-width: 800px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.8rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f5f7fb; color: #333; }
        .action-btn { padding: 0.35rem 0.6rem; border-radius: 6px; text-decoration: none; color: #fff; background: #4285F4; }
        .remove-btn { background: #c82333; }
        .checkout-btn { background: #28a745; margin-top: 1rem; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        <?php if ($cart_items): ?>
        <form method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td>GHS <?php echo number_format($item['price'],2); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width:60px;">
                                <button type="submit" name="update_item" class="action-btn">Update</button>
                            </form>
                        </td>
                        <td>GHS <?php echo number_format($item['subtotal'],2); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <button type="submit" name="remove_item" class="action-btn remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="text-align:right;font-weight:bold">Total: GHS <?php echo number_format($total,2); ?></p>
            <button type="submit" name="clear_cart" class="action-btn" style="background:#ffc107;color:#333;">Clear Cart</button>
            <a href="place_order.php" class="action-btn checkout-btn">Proceed to Checkout</a>
        </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
