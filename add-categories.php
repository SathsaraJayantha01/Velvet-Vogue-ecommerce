<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if categories table exists, create if not
$sql = "CREATE TABLE IF NOT EXISTS categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT(11) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Categories table created successfully or already exists<br>";
} else {
    echo "Error creating categories table: " . $conn->error . "<br>";
    exit;
}

// Check if table already has data
$check = $conn->query("SELECT COUNT(*) as count FROM categories");
$row = $check->fetch_assoc();
if ($row['count'] > 0) {
    echo "Categories table already contains data. Skipping insertion.<br>";
    echo "<p>If you want to reset the data, run: <code>TRUNCATE TABLE categories</code> in your MySQL client first.</p>";
    echo "<p><a href='index.html'>Go to Homepage</a></p>";
    exit;
}

// Categories data from the image
$categories = [
    [
        'name' => 'Men',
        'description' => 'Men\'s apparel collection',
        'parent_id' => NULL,
        'image' => 'images/categories/men.jpg'
    ],
    [
        'name' => 'Women',
        'description' => 'Women\'s fashion collection',
        'parent_id' => NULL,
        'image' => 'images/categories/women.jpg'
    ],
    [
        'name' => 'Casual Wear',
        'description' => 'Comfortable everyday casual clothing',
        'parent_id' => NULL,
        'image' => 'images/categories/casual.jpg'
    ],
    [
        'name' => 'Formal Wear',
        'description' => 'Elegant formal attire for special occasions',
        'parent_id' => NULL,
        'image' => 'images/categories/formal.jpg'
    ],
    [
        'name' => 'Accessories',
        'description' => 'Stylish accessories to complete your look',
        'parent_id' => NULL,
        'image' => 'images/categories/accessories.jpg'
    ],
    [
        'name' => 'Men\'s Casual',
        'description' => 'Casual wear for men',
        'parent_id' => 1,
        'image' => 'images/categories/mens-casual.jpg'
    ],
    [
        'name' => 'Men\'s Formal',
        'description' => 'Formal wear for men',
        'parent_id' => 1,
        'image' => 'images/categories/mens-formal.jpg'
    ],
    [
        'name' => 'Women\'s Casual',
        'description' => 'Casual wear for women',
        'parent_id' => 2,
        'image' => 'images/categories/womens-casual.jpg'
    ],
    [
        'name' => 'Women\'s Formal',
        'description' => 'Formal wear for women',
        'parent_id' => 2,
        'image' => 'images/categories/womens-formal.jpg'
    ]
];

// Insert categories into the database
$success_count = 0;
$error_count = 0;

foreach ($categories as $category) {
    $stmt = $conn->prepare("INSERT INTO categories (name, description, parent_id, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $category['name'], $category['description'], $category['parent_id'], $category['image']);
    
    if ($stmt->execute()) {
        $success_count++;
    } else {
        echo "Error inserting category {$category['name']}: " . $stmt->error . "<br>";
        $error_count++;
    }
    
    $stmt->close();
}

echo "<h2>Category Import Results</h2>";
echo "<p>Successfully imported: $success_count categories</p>";
if ($error_count > 0) {
    echo "<p>Failed to import: $error_count categories</p>";
}

// Display the imported categories
echo "<h2>Categories in Database</h2>";
$result = $conn->query("SELECT * FROM categories ORDER BY category_id");

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Parent ID</th><th>Image</th><th>Created At</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["category_id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["description"] . "</td>";
        echo "<td>" . ($row["parent_id"] ? $row["parent_id"] : "NULL") . "</td>";
        echo "<td>" . $row["image"] . "</td>";
        echo "<td>" . $row["created_at"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No categories found in database.</p>";
}

// Close connection
$conn->close();

echo "<p><a href='index.html'>Go to Homepage</a></p>";
?> 