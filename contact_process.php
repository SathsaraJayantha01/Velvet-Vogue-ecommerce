<?php
// Include database connection
require_once 'config.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if the contact_inquiries table exists, create if not
$sql = "CREATE TABLE IF NOT EXISTS contact_inquiries (
    inquiry_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (inquiry_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (!$conn->query($sql)) {
    $response = [
        'status' => 'error',
        'message' => 'Error creating contact_inquiries table: ' . $conn->error
    ];
    echo json_encode($response);
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    // Validation
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate subject
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    // Validate message
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        $response = [
            'status' => 'error',
            'message' => implode('<br>', $errors)
        ];
        echo json_encode($response);
        exit;
    }
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO contact_inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    // Try to execute the statement
    if ($stmt->execute()) {
        // Success
        $response = [
            'status' => 'success',
            'message' => 'Your inquiry has been submitted successfully! We will get back to you soon.'
        ];
    } else {
        // Error
        $response = [
            'status' => 'error',
            'message' => 'There was an error submitting your inquiry: ' . $stmt->error
        ];
    }
    
    // Close statement
    $stmt->close();
    
    // Return response
    echo json_encode($response);
    exit;
}

// If not a POST request, return method not allowed
$response = [
    'status' => 'error',
    'message' => 'Method not allowed'
];
echo json_encode($response);
?> 