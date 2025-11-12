<?php
// Admin role verification helper
// Include this at the top of admin pages to require admin role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in or not admin, redirect to admin login
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/admin_login.php');
    exit;
}

// Admin is verified; continue
?>