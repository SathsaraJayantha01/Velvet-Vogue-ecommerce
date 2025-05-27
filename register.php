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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    
    // Additional fields (optional)
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $city = isset($_POST['city']) ? trim($_POST['city']) : null;
    $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : null;
    $country = isset($_POST['country']) ? trim($_POST['country']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    
    // Default role is customer
    $role = 'customer';
    
    // Validation
    $errors = [];
    
    // Check if username is empty
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    // Check if email is empty
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if password is empty
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if first name is empty
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    
    // Check if last name is empty
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Username already exists";
    }
    $stmt->close();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    $stmt->close();
    
    // If there are no errors, insert the user
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, address, city, postal_code, country, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $username, $email, $hashed_password, $first_name, $last_name, $address, $city, $postal_code, $country, $phone, $role);
        
        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['auth_message'] = 'Registration successful! You can now login.';
            $_SESSION['auth_message_type'] = 'success';
        } else {
            // Registration failed
            $_SESSION['auth_message'] = 'Registration failed: ' . $stmt->error;
            $_SESSION['auth_message_type'] = 'error';
        }
        
        $stmt->close();
    } else {
        // Set validation errors message
        $_SESSION['auth_message'] = implode('<br>', $errors);
        $_SESSION['auth_message_type'] = 'error';
    }
    
    // Check if this is an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Return JSON response for AJAX requests
        $response = [
            'status' => isset($_SESSION['auth_message_type']) && $_SESSION['auth_message_type'] === 'success' ? 'success' : 'error',
            'message' => $_SESSION['auth_message']
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        // Redirect back to account page for standard form submissions
        header('Location: account.php');
        exit;
    }
}