<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "velvet_vogue";

// Create connection
try {
    $conn = new mysqli('localhost', 'root', '', 'velvet_vogue');
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set character set
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Log error instead of displaying it to users in production
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}
?>