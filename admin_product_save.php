<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php?redirect=admin_product_form.php&error=Please login as admin to access this page');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $quantity = intval($_POST['quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    $sku = trim($_POST['sku']);
    $image = trim($_POST['image']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
    $type = !empty($_POST['type']) ? $_POST['type'] : null;
    $status = $_POST['status'];
    
    // JSON data
    $sizes = trim($_POST['sizes']);
    $colors = trim($_POST['colors']);
    $specifications = trim($_POST['specifications']);
    
    // Validate JSON data
    try {
        if (!empty($sizes)) {
            json_decode($sizes);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON format in sizes field");
            }
        } else {
            $sizes = '[]';
        }
        
        if (!empty($colors)) {
            json_decode($colors);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON format in colors field");
            }
        } else {
            $colors = '[]';
        }
        
        if (!empty($specifications)) {
            json_decode($specifications);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON format in specifications field");
            }
        } else {
            $specifications = '{}';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: admin_product_form.php' . ($product_id ? "?id=$product_id" : ''));
        exit;
    }
    
    // Validate required fields
    if (empty($name) || empty($description) || $price <= 0) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header('Location: admin_product_form.php' . ($product_id ? "?id=$product_id" : ''));
        exit;
    }
    
    // Check if SKU is already used by another product
    $stmt = $conn->prepare("SELECT product_id FROM products WHERE sku = ? AND product_id != ?");
    $stmt->bind_param("si", $sku, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "SKU already exists. Please use a unique SKU.";
        header('Location: admin_product_form.php' . ($product_id ? "?id=$product_id" : ''));
        exit;
    }
    
    $stmt->close();
    
    // Check if it's an update or insert
    if ($product_id) {
        // Update existing product
        $stmt = $conn->prepare("UPDATE products SET 
            name = ?, 
            description = ?, 
            price = ?, 
            sale_price = ?, 
            quantity = ?, 
            featured = ?, 
            new_arrival = ?, 
            sku = ?, 
            image = ?, 
            category_id = ?, 
            gender = ?, 
            type = ?, 
            status = ?, 
            sizes = ?, 
            colors = ?, 
            specifications = ? 
            WHERE product_id = ?");
        
        $stmt->bind_param(
            "ssddiiississsssi",
            $name,
            $description,
            $price,
            $sale_price,
            $quantity,
            $featured,
            $new_arrival,
            $sku,
            $image,
            $category_id,
            $gender,
            $type,
            $status,
            $sizes,
            $colors,
            $specifications,
            $product_id
        );
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating product: " . $stmt->error;
        }
        
        $stmt->close();
        
        // Redirect to the product edit page
        header("Location: admin_product_form.php?id=$product_id");
        exit;
    } else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (
            name, 
            description, 
            price, 
            sale_price, 
            quantity, 
            featured, 
            new_arrival, 
            sku, 
            image, 
            category_id, 
            gender, 
            type, 
            status, 
            sizes, 
            colors, 
            specifications
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "ssddiiississsss",
            $name,
            $description,
            $price,
            $sale_price,
            $quantity,
            $featured,
            $new_arrival,
            $sku,
            $image,
            $category_id,
            $gender,
            $type,
            $status,
            $sizes,
            $colors,
            $specifications
        );
        
        if ($stmt->execute()) {
            $new_product_id = $conn->insert_id;
            $_SESSION['success'] = "Product added successfully.";
            
            // Redirect to the new product's edit page
            header("Location: admin_product_form.php?id=$new_product_id");
            exit;
        } else {
            $_SESSION['error'] = "Error adding product: " . $stmt->error;
            
            // Redirect back to the form
            header("Location: admin_product_form.php");
            exit;
        }
        
        $stmt->close();
    }
} else {
    // Not a POST request
    header('Location: admin_products.php');
    exit;
}

// Close connection
$conn->close();
?> 