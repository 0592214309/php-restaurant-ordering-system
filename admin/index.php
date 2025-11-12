<?php
// Simple Admin Dashboard - Phase 3 starter
include '../config/db.php';
include 'verify_admin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 1000px; margin: 2rem auto; padding: 2rem; }
        .admin-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
        .admin-card { background: white; border-radius: 8px; padding: 1.2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .admin-card h3 { color: #4285F4; margin-top: 0; }
        .admin-links a { display: inline-block; margin-top: 0.5rem; color: #fff; background: #4285F4; padding: 0.5rem 0.9rem; border-radius: 6px; text-decoration: none; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> — use the links below to manage the site.</p>
        <div class="admin-cards">
            <div class="admin-card">
                <h3>Menu Management</h3>
                <p>Add, edit, or remove menu items and categories.</p>
                <div class="admin-links">
                    <a href="menu_manage.php">Open Menu Manager</a>
                </div>
            </div>

            <div class="admin-card">
                <h3>Order Management</h3>
                <p>View and update order statuses.</p>
                <div class="admin-links">
                    <a href="orders.php">View Orders</a>
                </div>
            </div>

            <div class="admin-card">
                <h3>User Management</h3>
                <p>Manage users, roles, and activation.</p>
                <div class="admin-links">
                    <a href="users.php">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>