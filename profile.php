<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $response = [
        'status' => 'error',
        'message' => 'You must be logged in to update your profile.'
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $displayName = trim($_POST['display_name']);
    $email = trim($_POST['email']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmNewPassword = trim($_POST['confirm_new_password']);
    
    // Validation
    $errors = [];
    
    // Check required fields
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if changing email
    if ($email !== $_SESSION['email']) {
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists. Please use a different email.";
        }
        
        $stmt->close();
    }
    
    // Check if changing password
    $updatePassword = false;
    if (!empty($newPassword) || !empty($confirmNewPassword)) {
        // Verify current password
        if (empty($currentPassword)) {
            $errors[] = "Current password is required to change your password";
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                if (!password_verify($currentPassword, $user['password'])) {
                    $errors[] = "Current password is incorrect";
                }
            }
            
            $stmt->close();
        }
        
        // Validate new password
        if (empty($newPassword)) {
            $errors[] = "New password is required";
        } elseif (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        // Check if passwords match
        if ($newPassword !== $confirmNewPassword) {
            $errors[] = "New passwords do not match";
        }
        
        $updatePassword = true;
    }
    
    // If there are no errors, update the user profile
    if (empty($errors)) {
        if ($updatePassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update user with new password
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->bind_param("sssssi", $firstName, $lastName, $displayName, $email, $hashedPassword, $_SESSION['user_id']);
        } else {
            // Update user without changing password
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->bind_param("ssssi", $firstName, $lastName, $displayName, $email, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            // Update successful, update session variables
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['username'] = $displayName;
            $_SESSION['email'] = $email;
            
            $response = [
                'status' => 'success',
                'message' => 'Profile updated successfully.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error updating profile: ' . $stmt->error
            ];
        }
        
        $stmt->close();
    } else {
        // Return validation errors
        $response = [
            'status' => 'error',
            'message' => implode('<br>', $errors)
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}