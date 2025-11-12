<?php
// Add Menu Item (Admin only)
include '../config/db.php';
include 'verify_admin.php';
include 'csrf.php';
$csrf_token = get_csrf_token();

// Fetch categories for dropdown
$cat_sql = "SELECT category_id, category_name FROM categories WHERE is_active = 1 ORDER BY display_order, category_name";
$cat_result = $conn->query($cat_sql);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
        $message = 'Invalid request (CSRF).';
    } else {
        $item_name = trim($_POST['item_name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $image_url = NULL;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = '../assets/images/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = basename($_FILES['image']['name']);
        $target_file = $target_dir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = str_replace('../', '', $target_file);
        } else {
            $message = 'Image upload failed.';
        }
    }

    // Basic validation
    if (!$item_name || !$category_id || $price <= 0) {
        $message = 'Please fill all required fields and enter a valid price.';
    } else {
        $sql = "INSERT INTO menu_items (category_id, item_name, description, price, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issdsi', $category_id, $item_name, $description, $price, $image_url, $is_available);
        if ($stmt->execute()) {
            $message = 'Menu item added successfully!';
        } else {
            $message = 'Error adding menu item.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container { max-width: 500px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .form-header { color: #4285F4; text-align: center; font-size: 1.5rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.7rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; transition: border-color 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4285F4; box-shadow: 0 0 4px rgba(66,133,244,0.2); }
        .submit-btn { background: #4285F4; color: white; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: background 0.2s, box-shadow 0.2s; }
        .submit-btn:hover { background: #1565c0; box-shadow: 0 4px 12px rgba(66,133,244,0.2); }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 6px; text-align: center; font-weight: bold; background: #ffcdd2; color: #c62828; border: 2px solid #c62828; }
        .success { background: #d4edda; color: #155724; border: 2px solid #155724; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="form-container">
        <div class="form-header">Add New Menu Item</div>
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : ''; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">'; ?>
            <div class="form-group">
                <label for="item_name">Item Name *</label>
                <input type="text" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php if ($cat_result && $cat_result->num_rows > 0): ?>
                        <?php while ($cat = $cat_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price (GHS) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_available" checked> Available</label>
            </div>
            <button type="submit" class="submit-btn">Add Item</button>
        </form>
    </div>
</body>
</html>
