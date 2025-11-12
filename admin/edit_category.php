<?php
// Edit Category (Admin only)
include '../config/db.php';
include 'verify_admin.php';

$category_id = intval($_GET['id'] ?? 0);
if (!$category_id) {
    die('Invalid category ID.');
}

// Fetch current category
$sql = "SELECT * FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $category_id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cat) {
    die('Category not found.');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    if (!$category_name) {
        $message = 'Category name is required.';
    } else {
        $sql = "UPDATE categories SET category_name=?, description=?, display_order=? WHERE category_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $category_name, $description, $display_order, $category_id);
        if ($stmt->execute()) {
            $message = 'Category updated successfully!';
            $stmt->close();
            $stmt2 = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
            $stmt2->bind_param('i', $category_id);
            $stmt2->execute();
            $cat = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        } else {
            $message = 'Error updating category.';
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container { max-width: 500px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .form-header { color: #4285F4; text-align: center; font-size: 1.5rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.2rem; }
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
    <div class="form-container">
        <div class="form-header">Edit Category</div>
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : ''; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="category_name">Category Name *</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($cat['category_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($cat['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" min="0" value="<?php echo $cat['display_order']; ?>">
            </div>
            <button type="submit" class="submit-btn">Update Category</button>
        </form>
    </div>
</body>
</html>
