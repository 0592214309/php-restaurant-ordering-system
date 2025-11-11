<?php
// User Registration Page
// Allows new customers to create an account
// Password is hashed for security using PHP's password_hash()

include '../config/db.php';

// Message variable to show success/error
$message = '';

// When user submits the registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation: Check if all fields are filled
    if (empty($full_name)) {
        $message = 'Please enter your full name';
    } elseif (empty($email)) {
        $message = 'Please enter your email';
    } elseif (empty($phone)) {
        $message = 'Please enter your phone number';
    } elseif (empty($password)) {
        $message = 'Please enter a password';
    } elseif (empty($confirm_password)) {
        $message = 'Please confirm your password';
    }
    // Validation: Check if password is at least 8 characters
    elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters';
    }
    // Validation: Check if passwords match
    elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match';
    } else {
        // Validation: Check if email already exists
        $check_email_sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'Email already registered. Please use a different email or login.';
        } else {
            // Hash the password for security
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user into database
            $insert_sql = "INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'customer')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('ssss', $full_name, $email, $phone, $password_hash);
            
            if ($insert_stmt->execute()) {
                // Registration successful! Redirect to login page
                $message = 'Registration successful! Please login.';
                header('refresh:2;url=login.php'); // Redirect after 2 seconds
            } else {
                $message = 'Error during registration. Please try again.';
            }
            $insert_stmt->close();
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
    <title>Register - Restaurant</title>
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
            font-size: 0.95rem;
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
        }
        .success {
            background: #c8e6c9;
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }
        .error {
            background: #ffcdd2;
            color: #c62828;
            border: 2px solid #c62828;
        }
        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        .auth-link a {
            color: #4285F4;
            text-decoration: none;
            font-weight: bold;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
        .password-note {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="header">Create Your Account</div>
    
    <div class="auth-container">
        <!-- Show message if any -->
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="John Doe" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="024 123 4567" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required>
                <div class="password-note">Password must be at least 8 characters</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
            
            <button type="submit" class="submit-btn">Create Account</button>
        </form>
        
        <div class="auth-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
