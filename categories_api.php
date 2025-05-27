<?php
// Include database connection
require_once 'config.php';

// Set header to return JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Get all categories or filter by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Get category by ID
        $category_id = intval($_GET['id']);
        
        $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $category = $result->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => $category
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Category not found'
            ]);
        }
        
        $stmt->close();
    } elseif (isset($_GET['parent_id'])) {
        // Get categories by parent ID
        $parent_id = intval($_GET['parent_id']);
        
        // Get subcategories for a specific parent
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $categories,
            'count' => count($categories)
        ]);
        
        $stmt->close();
    } else {
        // Get all categories
        $result = $conn->query("SELECT * FROM categories ORDER BY category_id");
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        // Create hierarchical structure
        $hierarchical = [];
        $parentMap = [];
        
        // First, find all parent categories
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $category['subcategories'] = [];
                $hierarchical[] = $category;
                $parentMap[$category['category_id']] = count($hierarchical) - 1;
            }
        }
        
        // Then, add subcategories to their parents
        foreach ($categories as $category) {
            if ($category['parent_id'] !== null && isset($parentMap[$category['parent_id']])) {
                $hierarchical[$parentMap[$category['parent_id']]]['subcategories'][] = $category;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'flat' => $categories,
                'hierarchical' => $hierarchical
            ],
            'count' => count($categories)
        ]);
    }
} else {
    // Method not allowed
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}

// Close connection
$conn->close();
?> 