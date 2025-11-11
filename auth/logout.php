<?php
// Logout Page
// Destroys the session and redirects to home

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session
session_destroy();

// Redirect to homepage
header('Location: ../index.php');
exit;
?>
