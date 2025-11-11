<?php
// User Login Page
// Allows customers to login with email and password
// Creates a session if login is successful

include '../config/db.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Message variable to show success/error
$message = '';

// When user submits the login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation: Check if email and password are provided
    if (empty($email)) {
        $message = 'Please enter your email';
    } elseif (empty($password)) {
        $message = 'Please enter your password';
    } else {
        // Search for user with this email
        $login_sql = "SELECT user_id, full_name, email, password_hash, role FROM users WHERE email = ? AND is_active = 1";
        $stmt = $conn->prepare($login_sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            // User found, check password
            $user = $result->fetch_assoc();
            
            // Verify password using PHP's password_verify()
            if (password_verify($password, $user['password_hash'])) {
                // Login successful! Create session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect to homepage
                header('Location: ../index.php');
                exit;
            } else {
                // Password is incorrect
                $message = 'Invalid email or password';
            }
        } else {
            // User not found
            $message = 'Invalid email or password';
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
    <title>Login - Restaurant</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auth-container {
            background: white;
            padding: 2rem;
            margin: 2rem auto;
            border-radius: 12px;
            max-width: 400px;
            box-shadow: 0 2px 8px rgba(66,133,244,0.1);
        }
        .auth-container h2 {
            color: #4285F4;
            text-align: center;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.7rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4285F4;
            box-shadow: 0 0 4px rgba(66,133,244,0.2);
        }
        .submit-btn {
            background: #4285F4;
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            width: 100%;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .submit-btn:hover {
            background: #1565c0;
            box-shadow: 0 4px 12px rgba(66,133,244,0.2);
        }
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            background: #ffcdd2;
            color: #c62828;
            border: 2px solid #c62828;
        }
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        .auth-links p {
            color: #666;
            margin: 0.5rem 0;
        }
        .auth-links a {
            color: #4285F4;
            text-decoration: none;
            font-weight: bold;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">Login to Your Account</div>
    
    <div class="auth-container">
        <h2>Welcome Back!</h2>
        
        <!-- Show error message if any -->
        <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="submit-btn">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="../index.php">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>
