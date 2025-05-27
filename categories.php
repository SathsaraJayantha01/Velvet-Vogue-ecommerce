<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Get user data if logged in
$userData = [];
if($isLoggedIn) {
    $userData = [
        'username' => $_SESSION['username'] ?? '',
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'customer'
    ];
}

// Get cart count from session
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Get URL parameters for filtering
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$collection = isset($_GET['collection']) ? $_GET['collection'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Set page title and breadcrumb based on filters
$pageTitle = 'All Products';
$breadcrumb = 'Products';

if(!empty($gender)) {
    if($gender == 'men') {
        $pageTitle = 'Men\'s Collection';
        $breadcrumb = 'Men';
    } elseif($gender == 'women') {
        $pageTitle = 'Women\'s Collection';
        $breadcrumb = 'Women';
    }
} elseif(!empty($type)) {
    if($type == 'casual') {
        $pageTitle = 'Casual Wear';
        $breadcrumb = 'Casual Wear';
    } elseif($type == 'formal') {
        $pageTitle = 'Formal Wear';
        $breadcrumb = 'Formal Wear';
    } elseif($type == 'accessories') {
        $pageTitle = 'Accessories';
        $breadcrumb = 'Accessories';
    }
} elseif(!empty($collection)) {
    $pageTitle = ucfirst($collection) . ' Collection';
    $breadcrumb = ucfirst($collection) . ' Collection';
} elseif(!empty($filter)) {
    if($filter == 'new') {
        $pageTitle = 'New Arrivals';
        $breadcrumb = 'New Arrivals';
    } elseif($filter == 'sale') {
        $pageTitle = 'Sale Items';
        $breadcrumb = 'Sale Items';
    }
} elseif($category_id > 0) {
    // Get category name from database
    $stmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $pageTitle = $category['name'];
        $breadcrumb = $category['name'];
    }
    
    $stmt->close();
}

// Function to get all categories
function getCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT * FROM categories ORDER BY name");
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Get all categories for filter section
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="top-bar">
            <div class="container">
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> +1 234 567 8900</span>
                    <span><i class="fas fa-envelope"></i> info@velvetvogue.com</span>
                </div>
                <div class="account-nav">
                    <?php if($isLoggedIn): ?>
                        <a href="account.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($userData['first_name']); ?>'s Account</a>
                    <?php else: ?>
                        <a href="account.php"><i class="fas fa-user"></i> My Account</a>
                    <?php endif; ?>
                    <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> Cart (<span id="cart-count"><?php echo $cartCount; ?></span>)</a>
                </div>
            </div>
        </div>
        <div class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="index.php">
                        <h1>Velvet Vogue</h1>
                    </a>
                </div>
                <div class="search-bar">
                    <form action="search.php" method="get">
                        <input type="text" name="query" placeholder="Search for products...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <nav class="main-nav">
            <div class="container">
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="categories.php?gender=men" <?php if($gender == 'men') echo 'class="active"'; ?>>Men</a></li>
                    <li><a href="categories.php?gender=women" <?php if($gender == 'women') echo 'class="active"'; ?>>Women</a></li>
                    <li><a href="categories.php?type=casual" <?php if($type == 'casual') echo 'class="active"'; ?>>Casual Wear</a></li>
                    <li><a href="categories.php?type=formal" <?php if($type == 'formal') echo 'class="active"'; ?>>Formal Wear</a></li>
                    <li><a href="categories.php?type=accessories" <?php if($type == 'accessories') echo 'class="active"'; ?>>Accessories</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li id="category-breadcrumb"><?php echo htmlspecialchars($breadcrumb); ?></li>
            </ul>
        </div>
    </div>

    <!-- Product Category Section -->
    <section class="product-category">
        <div class="container">
            <div class="category-header">
                <h1 id="category-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                <div class="sort-filter">
                    <div class="sort">
                        <label for="sort-select">Sort by:</label>
                        <select id="sort-select">
                            <option value="default">Featured</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                        </select>
                    </div>
                    <div class="filter-toggle">
                        <button id="filter-toggle-btn"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            
            <div class="category-content">
                <aside class="filter-sidebar" id="filter-sidebar">
                    <div class="filter-header">
                        <h3>Filter Products</h3>
                        <button class="close-filter"><i class="fas fa-times"></i></button>
                    </div>
                    
                    <div class="filter-section">
                        <h4>Gender</h4>
                        <div class="filter-options">
                            <label>
                                <input type="checkbox" name="gender" value="men" class="filter-checkbox" <?php if($gender == 'men') echo 'checked'; ?>> Men
                            </label>
                            <label>
                                <input type="checkbox" name="gender" value="women" class="filter-checkbox" <?php if($gender == 'women') echo 'checked'; ?>> Women
                            </label>
                            <label>
                                <input type="checkbox" name="gender" value="unisex" class="filter-checkbox"> Unisex
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h4>Category</h4>
                        <div class="filter-options">
                            <?php foreach($categories as $category): ?>
                                <?php if($category['parent_id'] === null): ?>
                                    <label>
                                        <input type="checkbox" name="category" value="<?php echo $category['category_id']; ?>" class="filter-checkbox" <?php if($category_id == $category['category_id']) echo 'checked'; ?>> 
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h4>Size</h4>
                        <div class="filter-options size-options">
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="xs" class="filter-checkbox"> XS
                            </label>
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="s" class="filter-checkbox"> S
                            </label>
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="m" class="filter-checkbox"> M
                            </label>
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="l" class="filter-checkbox"> L
                            </label>
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="xl" class="filter-checkbox"> XL
                            </label>
                            <label class="size-btn">
                                <input type="checkbox" name="size" value="xxl" class="filter-checkbox"> XXL
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <input type="range" id="price-range" min="0" max="500" step="10" value="500">
                            <div class="price-inputs">
                                <span>$0</span>
                                <span id="price-range-value">$500</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h4>Color</h4>
                        <div class="filter-options color-options">
                            <label class="color-btn" style="background-color: #000;">
                                <input type="checkbox" name="color" value="black" class="filter-checkbox">
                                <span class="color-name">Black</span>
                            </label>
                            <label class="color-btn" style="background-color: #fff; border: 1px solid #ddd;">
                                <input type="checkbox" name="color" value="white" class="filter-checkbox">
                                <span class="color-name">White</span>
                            </label>
                            <label class="color-btn" style="background-color: #ff0000;">
                                <input type="checkbox" name="color" value="red" class="filter-checkbox">
                                <span class="color-name">Red</span>
                            </label>
                            <label class="color-btn" style="background-color: #0000ff;">
                                <input type="checkbox" name="color" value="blue" class="filter-checkbox">
                                <span class="color-name">Blue</span>
                            </label>
                            <label class="color-btn" style="background-color: #00ff00;">
                                <input type="checkbox" name="color" value="green" class="filter-checkbox">
                                <span class="color-name">Green</span>
                            </label>
                            <label class="color-btn" style="background-color: #ffff00;">
                                <input type="checkbox" name="color" value="yellow" class="filter-checkbox">
                                <span class="color-name">Yellow</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button id="apply-filters" class="btn-primary">Apply Filters</button>
                        <button id="clear-filters" class="btn-secondary">Clear All</button>
                    </div>
                </aside>
                
                <div class="product-listing">
                    <div class="product-grid" id="product-grid">
                        <!-- Products will be loaded dynamically using JavaScript -->
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading products...</p>
                        </div>
                    </div>
                    <div class="pagination" id="pagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Velvet Vogue</h3>
                    <p>Trendy casual and formal wear for young adults who want to express their identity through style.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="categories.php?gender=men">Men</a></li>
                        <li><a href="categories.php?gender=women">Women</a></li>
                        <li><a href="categories.php?type=accessories">Accessories</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="account.php">My Account</a></li>
                        <li><a href="cart.php">Shopping Cart</a></li>
                        <li><a href="shipping.php">Shipping & Returns</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Fashion Street, New York, NY 10001</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-envelope"></i> info@velvetvogue.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Velvet Vogue. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Initialize page data for JavaScript -->
    <script>
        // Pass PHP data to JavaScript
        const pageData = {
            gender: <?php echo json_encode($gender); ?>,
            type: <?php echo json_encode($type); ?>,
            category_id: <?php echo $category_id; ?>,
            collection: <?php echo json_encode($collection); ?>,
            filter: <?php echo json_encode($filter); ?>,
            pageTitle: <?php echo json_encode($pageTitle); ?>
        };
    </script>
    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/categories.js"></script>
</body>
</html>