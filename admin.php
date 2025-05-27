<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    // Not logged in or not admin, redirect to login page
    $_SESSION['auth_message'] = "You must be logged in as an administrator to access this page.";
    $_SESSION['auth_message_type'] = "error";
    header("Location: account.php");
    exit;
}

// Get admin user data
$adminData = [
    'user_id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'first_name' => $_SESSION['first_name'],
    'last_name' => $_SESSION['last_name'],
    'email' => $_SESSION['email']
];

// Get dashboard statistics
function getDashboardStats($conn) {
    $stats = [
        'total_sales' => 0,
        'total_orders' => 0,
        'total_customers' => 0,
        'total_products' => 0,
        'sales_growth' => 0,
        'orders_growth' => 0,
        'customers_growth' => 0,
        'products_growth' => 0
    ];
    
    // Get total sales
    $result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_sales'] = $row['total'] ?? 0;
    }
    
    // Get total orders
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['count'];
    }
    
    // Get total customers
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_customers'] = $row['count'];
    }
    
    // Get total products
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_products'] = $row['count'];
    }
    
    // Calculate growth percentages (example - in real app, would compare to previous period)
    $stats['sales_growth'] = 12.5;
    $stats['orders_growth'] = 8.3;
    $stats['customers_growth'] = 15.7;
    $stats['products_growth'] = 5.2;
    
    return $stats;
}

// Get recent orders for dashboard
function getRecentOrders($conn, $limit = 5) {
    $orders = [];
    $sql = "SELECT o.*, u.first_name, u.last_name 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            ORDER BY o.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        $stmt->close();
    }
    return $orders;
}

// Get top selling products for dashboard
function getTopSellingProducts($conn, $limit = 5) {
    $products = [];
    
    // Check if order_items table exists to avoid errors
    $tableCheck = $conn->query("SHOW TABLES LIKE 'order_items'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        // Verify the structure of the order_items table
        $columnCheck = $conn->query("SHOW COLUMNS FROM order_items LIKE 'order_item_id'");
        
        if ($columnCheck && $columnCheck->num_rows > 0) {
            // Table and column exist, proceed with original query
            $sql = "SELECT p.*, COUNT(oi.order_item_id) as order_count, SUM(oi.quantity) as total_quantity 
                    FROM products p 
                    JOIN order_items oi ON p.product_id = oi.product_id 
                    GROUP BY p.product_id 
                    ORDER BY total_quantity DESC 
                    LIMIT ?";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                
                $stmt->close();
            }
        } else {
            // Fall back to featured products (table exists but wrong structure)
            $sql = "SELECT * FROM products WHERE featured = 1 OR new_arrival = 1 ORDER BY created_at DESC LIMIT ?";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                
                $stmt->close();
            }
        }
    } else {
        // If order_items table doesn't exist, get featured products instead
        $sql = "SELECT * FROM products WHERE featured = 1 OR new_arrival = 1 ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            $stmt->close();
        }
    }
    
    // If we still have no products, just get the most recent ones
    if (empty($products)) {
        $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            $stmt->close();
        }
    }
    
    return $products;
}

// Get all products for product table
function getAllProducts($conn, $limit = 10, $offset = 0) {
    $products = [];
    $sql = "SELECT * FROM products ORDER BY product_id DESC LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        $stmt->close();
    }
    
    return $products;
}

// Get product categories for filter dropdown
function getCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT * FROM categories ORDER BY name");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Get total products count for pagination
function getProductsCount($conn) {
    $count = 0;
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    
    if ($result && $row = $result->fetch_assoc()) {
        $count = $row['count'];
    }
    
    return $count;
}

// Load dashboard data
$dashboardStats = getDashboardStats($conn);
$recentOrders = getRecentOrders($conn);
$topProducts = getTopSellingProducts($conn);
$categories = getCategories($conn);

// Handle pagination for products tab
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$allProducts = getAllProducts($conn, $limit, $offset);
$totalProducts = getProductsCount($conn);
$totalPages = ceil($totalProducts / $limit);

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-page">
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <div class="admin-header-content">
                <div class="logo">
                    <h1>Velvet Vogue <span class="admin-badge">Admin</span></h1>
                </div>
                <div class="admin-nav">
                    <a href="index.php" class="view-site-btn"><i class="fas fa-globe"></i> View Site</a>
                    <div class="admin-user-menu">
                        <div class="admin-user" id="admin-user-toggle">
                            <img src="images/admin-avatar.jpg" alt="Admin" class="admin-avatar">
                            <span><?php echo htmlspecialchars($adminData['first_name'] . ' ' . $adminData['last_name']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="admin-dropdown" id="admin-dropdown">
                            <ul>
                                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Admin Main Content -->
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <nav class="admin-sidebar-nav">
                <ul>
                    <li><a href="#dashboard" class="active" data-tab="dashboard-tab"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#products" data-tab="products-tab"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="#orders" data-tab="orders-tab"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="#customers" data-tab="customers-tab"><i class="fas fa-users"></i> Customers</a></li>
                    <li><a href="#categories" data-tab="categories-tab"><i class="fas fa-tags"></i> Categories</a></li>
                    <li><a href="#settings" data-tab="settings-tab"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Admin Content -->
        <main class="admin-content">
            <!-- Dashboard Tab -->
            <div class="admin-tab active" id="dashboard-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                </div>
                
                <div class="admin-stats">
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="admin-stat-info">
                            <h3>Total Sales</h3>
                            <p><?php echo formatCurrency($dashboardStats['total_sales']); ?></p>
                            <div class="stat-change positive">
                                +<?php echo $dashboardStats['sales_growth']; ?>% <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="admin-stat-info">
                            <h3>Total Orders</h3>
                            <p><?php echo $dashboardStats['total_orders']; ?></p>
                            <div class="stat-change positive">
                                +<?php echo $dashboardStats['orders_growth']; ?>% <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="admin-stat-info">
                            <h3>Total Customers</h3>
                            <p><?php echo $dashboardStats['total_customers']; ?></p>
                            <div class="stat-change positive">
                                +<?php echo $dashboardStats['customers_growth']; ?>% <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="admin-stat-info">
                            <h3>Total Products</h3>
                            <p><?php echo $dashboardStats['total_products']; ?></p>
                            <div class="stat-change positive">
                                +<?php echo $dashboardStats['products_growth']; ?>% <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="admin-dashboard-content">
                    <div class="admin-dashboard-row">
                        <div class="admin-dashboard-column">
                            <div class="admin-card">
                                <div class="admin-card-header">
                                    <h3>Recent Orders</h3>
                                    <a href="#orders" class="view-all" data-tab="orders-tab">View All</a>
                                </div>
                                <div class="admin-card-content">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(count($recentOrders) > 0): ?>
                                                <?php foreach($recentOrders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo $order['order_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                                    <td>
                                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" style="text-align: center;">No orders found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="admin-dashboard-column">
                            <div class="admin-card">
                                <div class="admin-card-header">
                                    <h3>Top Selling Products</h3>
                                    <a href="#products" class="view-all" data-tab="products-tab">View All</a>
                                </div>
                                <div class="admin-card-content">
                                    <ul class="admin-product-list">
                                        <?php if(count($topProducts) > 0): ?>
                                            <?php foreach($topProducts as $product): ?>
                                            <li class="admin-product-item">
                                                <div class="admin-product-image">
                                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                </div>
                                                <div class="admin-product-info">
                                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                                    <p>SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></p>
                                                </div>
                                                <div class="admin-product-stats">
                                                    <div class="admin-product-sales"><?php echo isset($product['total_quantity']) ? $product['total_quantity'] . ' sold' : 'Featured'; ?></div>
                                                    <div class="admin-product-revenue"><?php echo formatCurrency($product['price']); ?></div>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="admin-product-item">No products found</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products Tab -->
            <div class="admin-tab" id="products-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-box"></i> Products</h2>
                    <button id="add-product-btn" class="admin-btn primary">Add New Product</button>
                </div>
                
                <div class="admin-filters">
                    <div class="admin-search">
                        <input type="text" id="product-search" placeholder="Search products...">
                        <button id="filter-products-btn"><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="admin-filter-group">
                        <select id="category-filter">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="status-filter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-products"></th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body">
                            <?php if(count($allProducts) > 0): ?>
                                <?php foreach($allProducts as $product): ?>
                                <tr data-id="<?php echo $product['product_id']; ?>">
                                    <td><input type="checkbox" class="product-checkbox"></td>
                                    <td>
                                        <?php if(!empty($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumbnail">
                                        <?php else: ?>
                                            <div class="product-thumbnail" style="background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: #ccc;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if(!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                            <span class="old-price"><?php echo formatCurrency($product['price']); ?></span>
                                            <span class="current-price"><?php echo formatCurrency($product['sale_price']); ?></span>
                                        <?php else: ?>
                                            <?php echo formatCurrency($product['price']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['quantity']; ?></td>
                                    <td>
                                        <?php if($product['quantity'] > 10): ?>
                                            <span class="status-instock">In Stock</span>
                                        <?php elseif($product['quantity'] > 0): ?>
                                            <span class="status-lowstock">Low Stock</span>
                                        <?php else: ?>
                                            <span class="status-outofstock">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="edit-btn" data-id="<?php echo $product['product_id']; ?>"><i class="fas fa-edit"></i></button>
                                            <button class="delete-btn" data-id="<?php echo $product['product_id']; ?>"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center;">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-pagination">
                    <?php if($totalPages > 1): ?>
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="bulk-actions">
                    <select id="bulk-action">
                        <option value="">Bulk Actions</option>
                        <option value="delete">Delete</option>
                        <option value="mark-instock">Mark as In Stock</option>
                        <option value="mark-outofstock">Mark as Out of Stock</option>
                    </select>
                    <button id="apply-bulk-action" class="admin-btn primary">Apply</button>
                </div>
            </div>
            
            <!-- Orders Tab -->
            <div class="admin-tab" id="orders-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-shopping-cart"></i> Orders</h2>
                </div>
                
                <!-- Orders content will be loaded via AJAX -->
                <div class="admin-loading">Loading orders...</div>
            </div>
            
            <!-- Customers Tab -->
            <div class="admin-tab" id="customers-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-users"></i> Customers</h2>
                </div>
                
                <!-- Customers content will be loaded via AJAX -->
                <div class="admin-loading">Loading customers...</div>
            </div>
            
            <!-- Categories Tab -->
            <div class="admin-tab" id="categories-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-tags"></i> Categories</h2>
                    <button id="add-category-btn" class="admin-btn primary">Add New Category</button>
                </div>
                
                <!-- Categories content will be loaded via AJAX -->
                <div class="admin-loading">Loading categories...</div>
            </div>
            
            <!-- Settings Tab -->
            <div class="admin-tab" id="settings-tab">
                <div class="admin-section-header">
                    <h2><i class="fas fa-cog"></i> Settings</h2>
                </div>
                
                <!-- Settings content will be loaded via AJAX -->
                <div class="admin-loading">Loading settings...</div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="admin-modal" id="product-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 id="product-modal-title">Add New Product</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form id="product-form" action="admin_product_save.php" method="post">
                    <input type="hidden" id="product-id" name="product_id" value="">
                    
                    <div class="form-section">
                        <h3>Basic Information</h3>
                        <div class="form-group">
                            <label for="product-name">Product Name *</label>
                            <input type="text" id="product-name" name="name" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-sku">SKU</label>
                                <input type="text" id="product-sku" name="sku">
                            </div>
                            <div class="form-group">
                                <label for="product-category">Category</label>
                                <select id="product-category" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-price">Price *</label>
                                <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="product-sale-price">Sale Price</label>
                                <input type="number" id="product-sale-price" name="sale_price" step="0.01" min="0">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-stock">Stock Quantity *</label>
                                <input type="number" id="product-stock" name="quantity" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="product-status">Status</label>
                                <select id="product-status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-description">Description *</label>
                            <textarea id="product-description" name="description" rows="6" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" id="product-featured" name="featured" value="1">
                                    Featured Product
                                </label>
                            </div>
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" id="product-new" name="new_arrival" value="1">
                                    New Arrival
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Product Options</h3>
                        
                        <div class="form-subsection">
                            <h4>Available Sizes</h4>
                            <div class="checkbox-group size-options">
                                <label><input type="checkbox" name="size[]" value="xs"> XS</label>
                                <label><input type="checkbox" name="size[]" value="s"> S</label>
                                <label><input type="checkbox" name="size[]" value="m"> M</label>
                                <label><input type="checkbox" name="size[]" value="l"> L</label>
                                <label><input type="checkbox" name="size[]" value="xl"> XL</label>
                                <label><input type="checkbox" name="size[]" value="xxl"> XXL</label>
                            </div>
                        </div>
                        
                        <div class="form-subsection">
                            <h4>Available Colors</h4>
                            <div class="checkbox-group color-options-admin">
                                <label><input type="checkbox" name="color[]" value="black"> Black</label>
                                <label><input type="checkbox" name="color[]" value="white"> White</label>
                                <label><input type="checkbox" name="color[]" value="red"> Red</label>
                                <label><input type="checkbox" name="color[]" value="blue"> Blue</label>
                                <label><input type="checkbox" name="color[]" value="green"> Green</label>
                                <label><input type="checkbox" name="color[]" value="yellow"> Yellow</label>
                                <label><input type="checkbox" name="color[]" value="purple"> Purple</label>
                                <label><input type="checkbox" name="color[]" value="gray"> Gray</label>
                            </div>
                            <button type="button" id="add-color-btn" class="admin-btn secondary">Add Custom Color</button>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Product Images</h3>
                        
                        <div class="product-images-container">
                            <div class="product-image-upload">
                                <label>Main Product Image</label>
                                <div class="image-preview" id="main-image-preview">
                                    <input type="hidden" id="product-image" name="image">
                                    <label for="product-main-image" class="upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Upload Image</span>
                                    </label>
                                    <input type="file" id="product-main-image" accept="image/*">
                                </div>
                            </div>
                            
                            <div class="additional-images">
                                <label>Additional Images</label>
                                <div class="image-preview-container">
                                    <div class="image-preview-small">
                                        <input type="hidden" name="additional_images[]">
                                        <button type="button" class="remove-image">&times;</button>
                                    </div>
                                    <div class="image-preview-small">
                                        <input type="hidden" name="additional_images[]">
                                        <button type="button" class="remove-image">&times;</button>
                                    </div>
                                    <div class="add-more-images">
                                        <label for="product-additional-images">
                                            <i class="fas fa-plus"></i>
                                        </label>
                                        <input type="file" id="product-additional-images" accept="image/*" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Additional Information</h3>
                        
                        <div class="form-group">
                            <label for="product-specifications">Specifications (each line: key: value)</label>
                            <textarea id="product-specifications" name="specifications" rows="6" placeholder="material: 100% Cotton&#10;fit: Regular fit&#10;care: Machine wash cold"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-weight">Weight (kg)</label>
                                <input type="number" id="product-weight" name="weight" step="0.01" min="0">
                            </div>
                            <div class="form-group">
                                <label for="product-country">Country of Origin</label>
                                <input type="text" id="product-country" name="country">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-tags">Tags (comma separated)</label>
                            <input type="text" id="product-tags" name="tags" placeholder="shirt, cotton, casual">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="admin-btn primary">Save Product</button>
                        <button type="button" class="admin-btn secondary cancel-modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="admin-modal" id="category-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 id="category-modal-title">Add New Category</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form id="category-form" action="admin_category_save.php" method="post">
                    <input type="hidden" id="category-id" name="category_id" value="">
                    
                    <div class="form-group">
                        <label for="category-name">Category Name *</label>
                        <input type="text" id="category-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category-description">Description</label>
                        <textarea id="category-description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category-parent">Parent Category</label>
                        <select id="category-parent" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach($categories as $category): ?>
                                <?php if(empty($category['parent_id'])): ?>
                                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category-image">Category Image</label>
                        <div class="image-preview" id="category-image-preview">
                            <input type="hidden" id="category-image-input" name="image">
                            <label for="category-image-file" class="upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Upload Image</span>
                            </label>
                            <input type="file" id="category-image-file" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="admin-btn primary">Save Category</button>
                        <button type="button" class="admin-btn secondary cancel-modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="admin-modal" id="delete-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2>Confirm Delete</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="form-actions">
                    <button id="confirm-delete-btn" class="admin-btn primary">Delete</button>
                    <button class="admin-btn secondary cancel-modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Notification -->
    <div class="admin-notification" id="admin-notification">
        <div class="notification-content">
            <i class="fas fa-check-circle notification-icon"></i>
            <p class="notification-message"></p>
        </div>
        <button class="close-notification">&times;</button>
    </div>

    <script src="js/main.js"></script>
    <script src="js/admin.js"></script>
    
    <!-- Initialize admin data -->
    <script>
        // Pass PHP data to JavaScript
        const adminData = {
            userId: <?php echo $adminData['user_id']; ?>,
            username: "<?php echo htmlspecialchars($adminData['username']); ?>",
            firstName: "<?php echo htmlspecialchars($adminData['first_name']); ?>",
            lastName: "<?php echo htmlspecialchars($adminData['last_name']); ?>"
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize admin dashboard functionality
            initAdminDashboard();
            
            // Initialize user dropdown
            const userToggle = document.getElementById('admin-user-toggle');
            const userDropdown = document.getElementById('admin-dropdown');
            
            if (userToggle && userDropdown) {
                userToggle.addEventListener('click', function() {
                    userDropdown.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userToggle.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.remove('active');
                    }
                });
            }
            
            // Handle sidebar navigation with tab switching
            const sidebarLinks = document.querySelectorAll('.admin-sidebar-nav a');
            const tabs = document.querySelectorAll('.admin-tab');
            
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links and tabs
                    sidebarLinks.forEach(link => link.classList.remove('active'));
                    tabs.forEach(tab => tab.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Show corresponding tab
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                    
                    // Update URL hash
                    window.location.hash = this.getAttribute('href');
                });
            });
            
            // Check URL hash on page load
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const link = document.querySelector(`.admin-sidebar-nav a[href="#${hash}"]`);
                if (link) {
                    link.click();
                }
            }
        });
    </script>
</body>
</html>