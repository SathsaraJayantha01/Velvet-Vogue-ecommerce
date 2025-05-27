<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Contact Inquiries Sample Data Import</h1>";

// Check if the contact_inquiries table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'contact_inquiries'");
if ($tableExists->num_rows == 0) {
    echo "<p style='color: red;'>The contact_inquiries table does not exist. Please run setup-database.php first.</p>";
    echo "<p><a href='setup-database.php'>Run setup-database.php</a></p>";
    exit;
}

// Check if table already has data
$check = $conn->query("SELECT COUNT(*) as count FROM contact_inquiries");
$row = $check->fetch_assoc();
if ($row['count'] > 0) {
    echo "<p>Contact inquiries table already contains data. Skipping insertion.</p>";
    echo "<p>If you want to reset the data, run: <code>TRUNCATE TABLE contact_inquiries</code> in your MySQL client first.</p>";
    echo "<p><a href='admin_inquiries.php'>Go to Inquiries Admin</a></p>";
    exit;
}

// Sample contact inquiries data
$inquiries = [
    [
        'name' => 'John Smith',
        'email' => 'john.smith@example.com',
        'subject' => 'Order Status Inquiry',
        'message' => 'Hello,\n\nI placed an order (Order #VV-10234) three days ago and haven\'t received any shipping confirmation yet. Could you please check the status of my order?\n\nThanks,\nJohn Smith',
        'status' => 'resolved'
    ],
    [
        'name' => 'Sarah Johnson',
        'email' => 'sarah.j@example.com',
        'subject' => 'Product Size Question',
        'message' => 'Hi Velvet Vogue team,\n\nI\'m interested in purchasing the Elegant Evening Dress, but I\'m unsure about the sizing. I usually wear a size medium. Would you recommend I get a medium or a large for this particular dress?\n\nBest regards,\nSarah',
        'status' => 'resolved'
    ],
    [
        'name' => 'Michael Brown',
        'email' => 'mbrown@example.com',
        'subject' => 'Return Policy Question',
        'message' => 'I recently purchased a suit from your store but it doesn\'t fit quite right. What is your return policy? Can I exchange it for a different size?\n\nRegards,\nMichael Brown',
        'status' => 'in_progress'
    ],
    [
        'name' => 'Emily Davis',
        'email' => 'emily.davis@example.com',
        'subject' => 'Website Feedback',
        'message' => 'I just wanted to let you know that I love your new website design! It\'s very easy to navigate and the product photos are excellent. Keep up the good work!\n\n- Emily',
        'status' => 'pending'
    ],
    [
        'name' => 'James Wilson',
        'email' => 'jwilson@example.com',
        'subject' => 'Product Availability',
        'message' => 'Hello,\n\nI\'ve been trying to purchase the Classic Leather Jacket in Medium size, but it\'s been out of stock for weeks. Do you know when it will be available again?\n\nThanks,\nJames',
        'status' => 'pending'
    ],
    [
        'name' => 'Sophia Martinez',
        'email' => 'smartinez@example.com',
        'subject' => 'Damaged Item Received',
        'message' => 'I received my order yesterday, but the blue blouse has a tear in the sleeve. I would like to return this and get a replacement. Please advise on the next steps.\n\nThank you,\nSophia Martinez',
        'status' => 'in_progress'
    ]
];

// Insert inquiries into the database
$success_count = 0;
$error_count = 0;

foreach ($inquiries as $inquiry) {
    $stmt = $conn->prepare("INSERT INTO contact_inquiries (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $inquiry['name'], $inquiry['email'], $inquiry['subject'], $inquiry['message'], $inquiry['status']);
    
    if ($stmt->execute()) {
        $success_count++;
    } else {
        echo "<p>Error inserting inquiry from {$inquiry['name']}: " . $stmt->error . "</p>";
        $error_count++;
    }
    
    $stmt->close();
}

echo "<h2>Contact Inquiry Import Results</h2>";
echo "<p>Successfully imported: $success_count inquiries</p>";
if ($error_count > 0) {
    echo "<p>Failed to import: $error_count inquiries</p>";
}

// Display the imported inquiries
echo "<h2>Contact Inquiries in Database</h2>";
$result = $conn->query("SELECT * FROM contact_inquiries ORDER BY inquiry_id");

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Created At</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["inquiry_id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["subject"]) . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "<td>" . $row["created_at"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No inquiries found in database.</p>";
}

// Close connection
$conn->close();

echo "<p><a href='admin_inquiries.php'>Go to Inquiries Admin</a></p>";
echo "<p><a href='index.html'>Go to Homepage</a></p>";
?> 