<?php
// Basic Menu Management listing (read-only) to start Phase 3
include '../config/db.php';
include 'verify_admin.php';

// Fetch menu items
$sql = "SELECT m.item_id, m.item_name, m.price, c.category_name, m.is_available FROM menu_items m JOIN categories c ON m.category_id = c.category_id ORDER BY c.display_order, m.item_name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .manager { max-width: 1000px; margin: 2rem auto; padding: 1rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f7fb; color: #333; }
        .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: bold; }
        .available { background: #d4edda; color: #155724; }
        .unavailable { background: #f8d7da; color: #721c24; }
        .action-btn { padding: 0.35rem 0.6rem; border-radius: 6px; text-decoration: none; color: #fff; background: #4285F4; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="manager">
        <h1>Menu Management</h1>
        <p><a href="add_menu_item.php" class="action-btn">+ Add New Item</a></p>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>GHS <?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <?php if ($row['is_available']): ?>
                                    <span class="badge available">Available</span>
                                <?php else: ?>
                                    <span class="badge unavailable">Unavailable</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_menu_item.php?id=<?php echo $row['item_id']; ?>" class="action-btn">Edit</a>
                                <a href="delete_menu_item.php?id=<?php echo $row['item_id']; ?>" class="action-btn" style="background:#c82333;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No menu items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>