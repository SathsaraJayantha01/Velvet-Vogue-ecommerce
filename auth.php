<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function checkUserLoggedIn() {
    global $conn;
    
    // Check if session exists
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['session_token'])) {
        // Verify session token is valid in database
        $session_token = $_SESSION['session_token'];
        $stmt = $conn->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND logout_time IS NULL");
        $stmt->bind_param("s", $session_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            // Session is valid
            return true;
        }
        
        // Invalid session, clear session data
        $_SESSION = array();
        session_destroy();
    }
    
    // Check if remember me cookie exists
    if (isset($_COOKIE['session_token'])) {
        $session_token = $_COOKIE['session_token'];
        
        // Check if the session token exists in the database
        $stmt = $conn->prepare("SELECT s.*, u.user_id, u.username, u.email, u.first_name, u.last_name, u.role 
                               FROM user_sessions s 
                               JOIN users u ON s.user_id = u.user_id 
                               WHERE s.session_token = ? AND s.logout_time IS NULL");
        $stmt->bind_param("s", $session_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            // Valid session token, restore session
            $session = $result->fetch_assoc();
            
            // Start a new session
            session_start();
            
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $session['user_id'];
            $_SESSION['username'] = $session['username'];
            $_SESSION['email'] = $session['email'];
            $_SESSION['first_name'] = $session['first_name'];
            $_SESSION['last_name'] = $session['last_name'];
            $_SESSION['role'] = $session['role'];
            $_SESSION['session_token'] = $session_token;
            
            return true;
        }
        
        // Invalid session token, clear the cookie
        setcookie('session_token', '', time() - 3600, '/');
    }
    
    return false;
}

// Check if user is logged in
$isLoggedIn = checkUserLoggedIn();

// Prepare the response
if ($isLoggedIn) {
    $response = [
        'status' => 'success',
        'logged_in' => true,
        'user' => [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name'],
            'role' => $_SESSION['role']
        ]
    ];
} else {
    $response = [
        'status' => 'error',
        'logged_in' => false,
        'message' => 'Not logged in'
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;