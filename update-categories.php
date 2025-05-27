<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Category Table Update</h1>";

// Check if the categories table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'categories'");
if ($tableExists->num_rows == 0) {
    echo "<p style='color: red;'>The categories table does not exist. Please run add-categories.php first.</p>";
    echo "<p><a href='add-categories.php'>Run add-categories.php</a></p>";
    exit;
}

// Add created_at and updated_at columns if they don't exist
echo "<h2>Checking Table Structure</h2>";

// Check if created_at column exists
$checkCreatedAt = $conn->query("SHOW COLUMNS FROM categories LIKE 'created_at'");
if ($checkCreatedAt->num_rows == 0) {
    $conn->query("ALTER TABLE categories ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    echo "<p>Added created_at column</p>";
}

// Check if updated_at column exists
$checkUpdatedAt = $conn->query("SHOW COLUMNS FROM categories LIKE 'updated_at'");
if ($checkUpdatedAt->num_rows == 0) {
    $conn->query("ALTER TABLE categories ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    echo "<p>Added updated_at column</p>";
}

// Display categories before update
echo "<h2>Categories Before Update</h2>";
$beforeResult = $conn->query("SELECT * FROM categories ORDER BY category_id");

if ($beforeResult->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Parent ID</th><th>Image</th><th>Created At</th><th>Updated At</th></tr>";
    
    while($row = $beforeResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["category_id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["description"] . "</td>";
        echo "<td>" . ($row["parent_id"] ? $row["parent_id"] : "NULL") . "</td>";
        echo "<td>" . $row["image"] . "</td>";
        echo "<td>" . $row["created_at"] . "</td>";
        echo "<td>" . $row["updated_at"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No categories found in database.</p>";
}

// Verify if there are any differences or missing categories from the image
echo "<h2>Updating Categories Based on Image</h2>";

// Example data modification (you can adjust based on what you observed in the image)
// For example, if you need to update certain descriptions or add missing categories

// Update a category description if it exists (adjust based on what you see in the image)
$stmt = $conn->prepare("UPDATE categories SET description = ? WHERE name = ? AND category_id = ?");
$description = "Men's clothing and accessories";
$name = "Men";
$id = 1;
$stmt->bind_param("ssi", $description, $name, $id);
if ($stmt->execute()) {
    echo "<p>Updated description for 'Men' category</p>";
} else {
    echo "<p>Error updating 'Men' category: " . $stmt->error . "</p>";
}
$stmt->close();

// Example of how to add a missing category if needed
// Check if a specific category exists first
$result = $conn->query("SELECT * FROM categories WHERE name = 'Sale Items'");
if ($result->num_rows == 0) {
    // Add the missing category
    $stmt = $conn->prepare("INSERT INTO categories (name, description, parent_id, image) VALUES (?, ?, ?, ?)");
    $name = "Sale Items";
    $description = "Products currently on sale";
    $parentId = NULL;
    $image = "images/categories/sale.jpg";
    $stmt->bind_param("ssis", $name, $description, $parentId, $image);
    
    if ($stmt->execute()) {
        echo "<p>Added new category: 'Sale Items'</p>";
    } else {
        echo "<p>Error adding 'Sale Items' category: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Display categories after update
echo "<h2>Categories After Update</h2>";
$afterResult = $conn->query("SELECT * FROM categories ORDER BY category_id");

if ($afterResult->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Parent ID</th><th>Image</th><th>Created At</th><th>Updated At</th></tr>";
    
    while($row = $afterResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["category_id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["description"] . "</td>";
        echo "<td>" . ($row["parent_id"] ? $row["parent_id"] : "NULL") . "</td>";
        echo "<td>" . $row["image"] . "</td>";
        echo "<td>" . $row["created_at"] . "</td>";
        echo "<td>" . $row["updated_at"] . "</td>";
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