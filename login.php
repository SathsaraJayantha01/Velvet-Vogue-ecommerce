<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    // Validate inputs
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If there are no validation errors, check if the user exists
    if (empty($errors)) {
        // Check if the user exists
        $stmt = $conn->prepare("SELECT user_id, username, email, password, first_name, last_name, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // User found, verify password
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];
                
                // Generate a unique session token
                $session_token = bin2hex(random_bytes(32));
                $_SESSION['session_token'] = $session_token;
                
                // Record login in user_sessions table
                $user_id = $user['user_id'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $ip_address = $_SERVER['REMOTE_ADDR'];
                
                // Check if user_sessions table exists, create if not
                $tableCheck = $conn->query("SHOW TABLES LIKE 'user_sessions'");
                if ($tableCheck->num_rows == 0) {
                    // Table does not exist, create it
                    $createTable = "CREATE TABLE user_sessions (
                        session_id INT(11) NOT NULL AUTO_INCREMENT,
                        user_id INT(11) NOT NULL,
                        session_token VARCHAR(255) NOT NULL,
                        ip_address VARCHAR(45) NOT NULL,
                        user_agent TEXT NOT NULL,
                        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        logout_time TIMESTAMP NULL DEFAULT NULL,
                        PRIMARY KEY (session_id),
                        KEY user_id (user_id),
                        KEY session_token (session_token)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                    
                    $conn->query($createTable);
                }
                
                // Insert session data
                $sessionStmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent) VALUES (?, ?, ?, ?)");
                $sessionStmt->bind_param("isss", $user_id, $session_token, $ip_address, $user_agent);
                $sessionStmt->execute();
                $sessionStmt->close();
                
                // Set remember me cookie if requested
                if ($remember) {
                    setcookie('session_token', $session_token, time() + (86400 * 30), '/'); // 30 days
                }
                
                // Success message
                $_SESSION['auth_message'] = "Login successful! Welcome back, " . $user['first_name'] . "!";
                $_SESSION['auth_message_type'] = "success";
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: customer_dashboard.php");
                }
                exit;
            } else {
                // Invalid password
                $_SESSION['auth_message'] = "Invalid email or password";
                $_SESSION['auth_message_type'] = "error";
            }
        } else {
            // User not found
            $_SESSION['auth_message'] = "Invalid email or password";
            $_SESSION['auth_message_type'] = "error";
        }
        
        $stmt->close();
    } else {
        // Validation errors
        $_SESSION['auth_message'] = implode("<br>", $errors);
        $_SESSION['auth_message_type'] = "error";
    }
    
    // Redirect back to the account page with the error message
    header("Location: account.php");
    exit;
}

// If not POST request, redirect to account page
header("Location: account.php");
exit;
?>