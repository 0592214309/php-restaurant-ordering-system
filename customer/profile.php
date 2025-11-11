<?php
// User Profile Page
// Shows logged-in user's info and allows password change
include '../config/db.php';
include '../auth/verify.php';
$user_id = $_SESSION['user_id'];
$message = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if (strlen($new_password) < 8) {
        $message = 'New password must be at least 8 characters.';
    } elseif ($new_password !== $confirm_password) {
        $message = 'New passwords do not match.';
    } else {
        $sql = "SELECT password_hash FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();
        if (!password_verify($old_password, $hash)) {
            $message = 'Old password is incorrect.';
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $update->bind_param('si', $new_hash, $user_id);
            if ($update->execute()) {
                $message = 'Password updated successfully!';
            } else {
                $message = 'Error updating password.';
            }
            $update->close();
        }
    }
}
// Get user info
$sql = "SELECT full_name, email, phone, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $role);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container { max-width: 500px; margin: 2rem auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(66,133,244,0.1); padding: 2rem; }
        .profile-header { color: #4285F4; text-align: center; font-size: 1.5rem; margin-bottom: 1.5rem; }
        .profile-info { margin-bottom: 2rem; }
        .profile-info div { margin-bottom: 0.7rem; }
        .profile-label { color: #666; font-weight: bold; }
        .profile-value { color: #333; }
        .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1rem; }
        .success { background: #c8e6c9; color: #2e7d32; border: 1px solid #2e7d32; }
        .error { background: #ffcdd2; color: #c62828; border: 1px solid #c62828; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.3rem; color: #333; font-weight: bold; }
        .form-group input { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #4285F4; box-shadow: 0 0 4px rgba(66,133,244,0.2); }
        .submit-btn { background: #4285F4; color: white; border: none; padding: 0.8rem; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: background 0.2s; }
        .submit-btn:hover { background: #1565c0; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="profile-container">
        <div class="profile-header">My Profile</div>
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="profile-info">
            <div><span class="profile-label">Name:</span> <span class="profile-value"><?php echo htmlspecialchars($name); ?></span></div>
            <div><span class="profile-label">Email:</span> <span class="profile-value"><?php echo htmlspecialchars($email); ?></span></div>
            <div><span class="profile-label">Phone:</span> <span class="profile-value"><?php echo htmlspecialchars($phone); ?></span></div>
            <div><span class="profile-label">Role:</span> <span class="profile-value"><?php echo htmlspecialchars($role); ?></span></div>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="submit-btn">Change Password</button>
        </form>
    </div>
</body>
</html>
