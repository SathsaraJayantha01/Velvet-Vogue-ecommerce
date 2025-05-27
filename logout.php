<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Get session token if available
    $session_token = $_SESSION['session_token'] ?? null;
    
    // Update user_sessions table if applicable
    if ($session_token && isset($conn)) {
        $stmt = $conn->prepare("UPDATE user_sessions SET logout_time = NOW() WHERE session_token = ?");
        if ($stmt) {
            $stmt->bind_param("s", $session_token);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // If a session cookie is used, delete it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Delete any remember-me cookies
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 42000, '/');
    }
    
    // Destroy the session completely
    session_destroy();
    
    // Start new session for flash message
    session_start();
    $_SESSION['auth_message'] = "You have been successfully logged out.";
    $_SESSION['auth_message_type'] = "success";
}

// Make sure this redirect cannot be bypassed
echo "<script>window.location.href = 'index.php';</script>";

// Also use PHP header redirect with exit to ensure redirection works
header("Location: index.php");
exit();
?>