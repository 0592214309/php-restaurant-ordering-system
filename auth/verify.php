<?php
// Session verification helper
// Check if user is logged in, redirect to login if not
// Use this on pages that require authentication

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/../auth/login.php');
    exit;
}

// User is logged in, can continue
?>
