<?php
// Admin User Management
include '../config/db.php';
include 'verify_admin.php';

// Handle role/update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $allowed_roles = ['customer','admin','staff'];
    if (in_array($role, $allowed_roles)) {
        $stmt = $conn->prepare("UPDATE users SET role=?, is_active=? WHERE user_id=?");
        $stmt->bind_param('sii', $role, $is_active, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch users
$sql = "SELECT user_id, full_name, email, phone, role, created_at, is_active FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin - Users</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.container{max-width:1000px;margin:2rem auto;padding:1rem}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:0.5rem;border-bottom:1px solid #eee;text-align:left}
.action-btn{padding:0.35rem 0.6rem;border-radius:6px;text-decoration:none;color:#fff;background:#4285F4}
</style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h1>Users</h1>
    <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Active</th><th>Registered</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($result && $result->num_rows>0): while($u = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['phone']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                        <select name="role">
                            <option value="customer" <?php echo ($u['role']=='customer')?'selected':''; ?>>customer</option>
                            <option value="staff" <?php echo ($u['role']=='staff')?'selected':''; ?>>staff</option>
                            <option value="admin" <?php echo ($u['role']=='admin')?'selected':''; ?>>admin</option>
                        </select>
                </td>
                <td><input type="checkbox" name="is_active" <?php echo ($u['is_active'])?'checked':''; ?>></td>
                <td><?php echo $u['created_at']; ?></td>
                <td>
                        <button type="submit" name="update_user" class="action-btn">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
