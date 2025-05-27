<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "velvet_vogue";

// Create connection
$conn = new mysqli('localhost', 'root', '' , 'velvet_vogue');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($dbname);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    phone VARCHAR(20),
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully or already exists<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Check if admin user exists
$sql = "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Create default admin user
    $adminUsername = "admin";
    $adminEmail = "admin@velvetvogue.com";
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $adminFirstName = "Admin";
    $adminLastName = "User";
    
    $sql = "INSERT INTO users (username, email, password, first_name, last_name, role) 
            VALUES ('$adminUsername', '$adminEmail', '$adminPassword', '$adminFirstName', '$adminLastName', 'admin')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Default admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "Admin user already exists<br>";
}

// Close connection
$conn->close();

echo "<p>Database setup completed. <a href='index.html'>Go to homepage</a> | <a href='add-products.php'>Add Products</a></p>";
?>