<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin (if you have authentication)
/*
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php?redirect=manage-products.php&error=Please login as admin to access this page');
    exit;
}
*/

// Function to add a new product (if form submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $quantity = intval($_POST['quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    
    // Insert the product
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, sale_price, quantity, featured, new_arrival) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddiii", $name, $description, $price, $sale_price, $quantity, $featured, $new_arrival);
    
    if ($stmt->execute()) {
        $message = "Product added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding product: " . $stmt->error;
        $message_type = "error";
    }
    
    $stmt->close();
}

// Check if action is to delete a product
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Delete the product
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $message = "Product deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting product: " . $stmt->error;
        $message_type = "error";
    }
    
    $stmt->close();
}

// Get all products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY product_id");

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        .products-table th, .products-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e1e1e1;
        }
        .products-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .add-product-form {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .form-group {
            flex: 1;
            min-width: 250px;
            margin-right: 1rem;
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
        }
        .checkbox-group {
            margin-bottom: 1rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #000;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        
        <?php if(isset($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <h2>Add New Product</h2>
        <form class="add-product-form" method="post" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="sale_price">Sale Price (optional)</label>
                    <input type="number" id="sale_price" name="sale_price" step="0.01" min="0">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0" required>
                </div>
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="featured" value="1"> Featured
                    </label>
                    <label>
                        <input type="checkbox" name="new_arrival" value="1"> New Arrival
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>
        
        <h2>Product List</h2>
        <?php if(count($products) > 0): ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Sale Price</th>
                        <th>Quantity</th>
                        <th>Featured</th>
                        <th>New Arrival</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['sale_price'] ? '$' . number_format($product['sale_price'], 2) : '-'; ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $product['new_arrival'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $product['created_at']; ?></td>
                            <td><?php echo $product['updated_at']; ?></td>
                            <td class="actions">
                                <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?php echo $product['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found in the database.</p>
            <p>To add sample products, you can uncomment and modify the code in the add-sample-products.php section.</p>
        <?php endif; ?>

        <a href="index.php" class="btn btn-primary">Back to Homepage</a>
    </div>
</body>
</html>