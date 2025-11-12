<?php
// Category Management (Admin only)
include '../config/db.php';
include 'verify_admin.php';

// Handle add category
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    if (!$category_name) {
        $message = 'Category name is required.';
    } else {
        $sql = "INSERT INTO categories (category_name, description, display_order, is_active) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $category_name, $description, $display_order);
        if ($stmt->execute()) {
            $message = 'Category added successfully!';
        } else {
            $message = 'Error adding category.';
        }
        $stmt->close();
    }
}

// Fetch categories
$sql = "SELECT * FROM categories ORDER BY display_order, category_name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .manager { max-width: 800px; margin: 2rem auto; padding: 1rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f7fb; color: #333; }
        .action-btn { padding: 0.35rem 0.6rem; border-radius: 6px; text-decoration: none; color: #fff; background: #4285F4; }
        .form-container { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 1.5rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.7rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; transition: border-color 0.2s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #4285F4; box-shadow: 0 0 4px rgba(66,133,244,0.2); }
        .submit-btn { background: #4285F4; color: white; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: background 0.2s, box-shadow 0.2s; }
        .submit-btn:hover { background: #1565c0; box-shadow: 0 4px 12px rgba(66,133,244,0.2); }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 6px; text-align: center; font-weight: bold; background: #ffcdd2; color: #c62828; border: 2px solid #c62828; }
        .success { background: #d4edda; color: #155724; border: 2px solid #155724; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="manager">
        <h1>Category Management</h1>
        <div class="form-container">
            <h2>Add New Category</h2>
            <?php if ($message): ?>
                <div class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : ''; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" min="0" value="0">
                </div>
                <button type="submit" name="add_category" class="submit-btn">Add Category</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($cat = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($cat['description']); ?></td>
                            <td><?php echo $cat['display_order']; ?></td>
                            <td><?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <a href="edit_category.php?id=<?php echo $cat['category_id']; ?>" class="action-btn">Edit</a>
                                <a href="delete_category.php?id=<?php echo $cat['category_id']; ?>" class="action-btn" style="background:#c82333;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
