<?php
// Admin Login Page (stub for phase 2)
include '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $message = 'Please enter both email and password.';
    } else {
        $sql = "SELECT user_id, full_name, password_hash, role FROM users WHERE email = ? AND role = 'admin' AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['user_id'] = $admin['user_id'];
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['role'] = $admin['role'];
                header('Location: ../admin/index.php');
                exit;
            } else {
                $message = 'Invalid credentials.';
            }
        } else {
            $message = 'Invalid credentials.';
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auth-container { background: white; padding: 2rem; margin: 2rem auto; border-radius: 12px; max-width: 400px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); }
        .auth-container h2 { color: #4285F4; text-align: center; margin-top: 0; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: bold; }
        .form-group input { width: 100%; padding: 0.7rem; border: 2px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; transition: border-color 0.2s; }
        .form-group input:focus { outline: none; border-color: #4285F4; box-shadow: 0 0 4px rgba(66,133,244,0.2); }
        .submit-btn { background: #4285F4; color: white; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: background 0.2s, box-shadow 0.2s; }
        .submit-btn:hover { background: #1565c0; box-shadow: 0 4px 12px rgba(66,133,244,0.2); }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 6px; text-align: center; font-weight: bold; background: #ffcdd2; color: #c62828; border: 2px solid #c62828; }
    </style>
</head>
<body>
    <div class="header">Admin Login</div>
    <div class="auth-container">
        <h2>Welcome, Admin!</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="admin@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>
</body>
</html>
