<?php
// Delete Menu Item (Admin only)
include '../config/db.php';
include 'verify_admin.php';

$item_id = intval($_GET['id'] ?? 0);
if (!$item_id) {
    die('Invalid menu item ID.');
}

// Fetch item info for confirmation
$sql = "SELECT item_name FROM menu_items WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$stmt->bind_result($item_name);
$stmt->fetch();
$stmt->close();

if (!$item_name) {
    die('Menu item not found.');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM menu_items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    if ($stmt->execute()) {
        $message = 'Menu item deleted successfully!';
    } else {
        $message = 'Error deleting menu item.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Menu Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container { max-width: 400px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .form-header { color: #c82333; text-align: center; font-size: 1.5rem; margin-bottom: 1.5rem; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 6px; text-align: center; font-weight: bold; background: #ffcdd2; color: #c82333; border: 2px solid #c82333; }
        .success { background: #d4edda; color: #155724; border: 2px solid #155724; }
        .btn-danger { background: #c82333; color: #fff; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; margin-top: 1rem; }
        .btn-cancel { background: #4285F4; color: #fff; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="form-container">
        <div class="form-header">Delete Menu Item</div>
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : ''; ?>"><?php echo htmlspecialchars($message); ?></div>
            <p><a href="menu_manage.php">Back to Menu Management</a></p>
        <?php else: ?>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($item_name); ?></strong>?</p>
            <form method="POST">
                <button type="submit" class="btn-danger">Yes, Delete</button>
            </form>
            <form action="menu_manage.php" method="get">
                <button type="submit" class="btn-cancel">Cancel</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
