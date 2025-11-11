<?php
// Simple database connection for beginners
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurant";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // If connection fails, show a simple error message
    die("Connection failed: " . $conn->connect_error);
}
// Connection is successful!
?>
