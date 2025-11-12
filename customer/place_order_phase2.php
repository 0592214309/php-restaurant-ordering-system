<?php
// Place Order Page - Phase 2
// Allows logged-in customers to select items and place an order
// Only accessible to logged-in users

include '../config/db.php';
include '../auth/verify.php'; // Check if user is logged in

// Get logged-in user info
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Variable to store success/error messages
$message = '';

// When user submits the order form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get delivery info from the form
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $selected_items = $_POST['items'] ?? [];
    
    // Validation: Check if phone is provided
    if (empty($customer_phone)) {
        $message = 'Please enter your delivery phone number';
    } elseif (empty($selected_items)) {
        $message = 'Please select at least one item';
    } else {
        // Calculate total amount and prepare items
        $total_amount = 0;
        $order_items = [];
        
        foreach ($selected_items as $item_id => $quantity) {
            if ($quantity > 0) {
                // Get item price from database using prepared statement (secure)
                $item_sql = "SELECT item_name, price FROM menu_items WHERE item_id = ?";
                $item_stmt = $conn->prepare($item_sql);
                $item_stmt->bind_param('i', $item_id);
                $item_stmt->execute();
                $item_result = $item_stmt->get_result();
                
                if ($item_result && $item_result->num_rows > 0) {
                    $item = $item_result->fetch_assoc();
                    $subtotal = $item['price'] * $quantity;
                    $total_amount += $subtotal;
                    $order_items[$item_id] = [
                        'quantity' => $quantity,
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                        'name' => $item['item_name']
                    ];
                }
                $item_stmt->close();
            }
        }
        
        // If there are items, insert order into database
        if (!empty($order_items)) {
            // Step 1: Insert order into orders table with user_id
            $insert_order_sql = "INSERT INTO orders (user_id, customer_name, customer_phone, total_amount) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_order_sql);
            $stmt->bind_param('issd', $user_id, $user_name, $customer_phone, $total_amount);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            // Step 2: Insert each selected item into order_items table
            foreach ($order_items as $item_id => $item_data) {
                $insert_item_sql = "INSERT INTO order_items (order_id, item_id, quantity, price_at_order, subtotal) VALUES (?, ?, ?, ?, ?)";
                $item_stmt = $conn->prepare($insert_item_sql);
                $price = $item_data['price'];
                $quantity = $item_data['quantity'];
                $subtotal = $item_data['subtotal'];
                $item_stmt->bind_param('iiidd', $order_id, $item_id, $quantity, $price, $subtotal);
                $item_stmt->execute();
                $item_stmt->close();
            }
            
            // Success! Show confirmation
            $message = 'Order placed successfully! Order ID: ' . $order_id . ' | Total: GHS ' . number_format($total_amount, 2);
        } else {
            $message = 'Please select at least one item';
        }
    }
}

// Get all menu items grouped by category
$sql = "SELECT c.category_name, m.item_id, m.item_name, m.price FROM menu_items m JOIN categories c ON m.category_id = c.category_id WHERE m.is_available = 1 ORDER BY c.display_order, m.item_name";
$result = $conn->query($sql);
$items_by_category = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category_name'];
        if (!isset($items_by_category[$category])) {
            $items_by_category[$category] = [];
        }
        $items_by_category[$category][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .order-form {
            background: white;
            padding: 2rem;
            margin: 2rem;
            border-radius: 12px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 8px rgba(66,133,244,0.1);
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #4285F4;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4285F4;
            box-shadow: 0 0 4px rgba(66,133,244,0.2);
        }
        .category-section {
            margin-bottom: 1.5rem;
        }
        .category-section h4 {
            color: #4285F4;
            margin-top: 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem;
            background: #f5f5f5;
            margin-bottom: 0.5rem;
            border-radius: 6px;
        }
        .item-row input {
            width: 60px !important;
            padding: 0.3rem !important;
        }
        .submit-btn {
            background: #4285F4;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            transition: background 0.2s;
        }
        .submit-btn:hover {
            background: #1565c0;
        }
        .message {
            padding: 1rem;
            margin: 2rem;
            border-radius: 6px;
            text-align: center;
        }
        .success {
            background: #c8e6c9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }
        .error {
            background: #ffcdd2;
            color: #c62828;
            border: 1px solid #c62828;
        }
        .back-link {
            text-align: center;
            margin: 1rem;
        }
        .back-link a {
            color: #4285F4;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Include Navigation Bar -->
    <?php include '../includes/navbar.php'; ?>
    
    <div class="header">Place Your Order</div>
    
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="order-form">
        <!-- Welcome message with user name -->
        <div class="welcome-message">
            Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋
        </div>
        
        <!-- Delivery Phone -->
        <div class="form-group">
            <label for="customer_phone">Delivery Phone Number</label>
            <input type="tel" id="customer_phone" name="customer_phone" placeholder="Enter delivery phone number" required>
        </div>
        
        <!-- Menu Items Organized by Category -->
        <div class="form-group">
            <label>Select Items (Enter Quantity)</label>
            <?php foreach ($items_by_category as $category => $items): ?>
                <div class="category-section">
                    <h4><?php echo htmlspecialchars($category); ?></h4>
                    <?php foreach ($items as $item): ?>
                        <div class="item-row">
                            <span><?php echo htmlspecialchars($item['item_name']); ?> - GHS <?php echo number_format($item['price'], 2); ?></span>
                            <input type="number" name="items[<?php echo $item['item_id']; ?>]" value="0" min="0" max="100">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Place Order</button>
    </form>
    
    <div class="back-link">
        <a href="../index.php">← Back to Menu</a>
    </div>
</body>
</html>
