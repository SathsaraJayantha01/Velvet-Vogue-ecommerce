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

// Function to get featured products
function getFeaturedProducts($conn, $limit = 4) {
    $products = [];
    $sql = "SELECT * FROM products WHERE featured = 1 AND status = 'active' LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    $stmt->close();
    return $products;
}

// Function to get new arrivals
function getNewArrivals($conn, $limit = 4) {
    $products = [];
    $sql = "SELECT * FROM products WHERE new_arrival = 1 AND status = 'active' ORDER BY created_at DESC LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    $stmt->close();
    return $products;
}

// Get featured products and new arrivals
$featuredProducts = getFeaturedProducts($conn);
$newArrivals = getNewArrivals($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue - Trendy Casual & Formal Wear</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="categories.php?gender=men">Men</a></li>
                    <li><a href="categories.php?gender=women">Women</a></li>
                    <li><a href="categories.php?type=casual">Casual Wear</a></li>
                    <li><a href="categories.php?type=formal">Formal Wear</a></li>
                    <li><a href="categories.php?type=accessories">Accessories</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Banner Section -->
    <section class="hero-banner">
        <div class="container">
            <div class="banner-content">
                <h2>Express Your Identity</h2>
                <p>Discover the latest trends in casual and formal wear</p>
                <a href="categories.php" class="btn-primary">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <div class="container">
            <div class="section-title">
                <h2>Featured Products</h2>
                <p>Our handpicked collection for you</p>
            </div>
            <div class="product-grid" id="featured-products">
                <?php if(count($featuredProducts) > 0): ?>
                    <?php foreach($featuredProducts as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <?php if($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                    <div class="product-tag sale">Sale</div>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <button class="quick-view" data-product="<?php echo $product['product_id']; ?>">
                                        <i class="fas fa-eye"></i> Quick View
                                    </button>
                                    <button class="add-to-wishlist" data-product="<?php echo $product['product_id']; ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                </h3>
                                <div class="product-price">
                                    <?php if($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                        <span class="current-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                        <span class="old-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="add-to-cart-btn" data-product="<?php echo $product['product_id']; ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No featured products available at the moment.</p>
                <?php endif; ?>
            </div>
            <div class="view-more">
                <a href="categories.php" class="btn-secondary">View All Products</a>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="new-arrivals">
        <div class="container">
            <div class="section-title">
                <h2>New Arrivals</h2>
                <p>The latest additions to our collection</p>
            </div>
            <div class="product-grid" id="new-arrivals">
                <?php if(count($newArrivals) > 0): ?>
                    <?php foreach($newArrivals as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <div class="product-tag new">New</div>
                                <div class="product-actions">
                                    <button class="quick-view" data-product="<?php echo $product['product_id']; ?>">
                                        <i class="fas fa-eye"></i> Quick View
                                    </button>
                                    <button class="add-to-wishlist" data-product="<?php echo $product['product_id']; ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                </h3>
                                <div class="product-price">
                                    <?php if($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                        <span class="current-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                        <span class="old-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="add-to-cart-btn" data-product="<?php echo $product['product_id']; ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No new arrivals available at the moment.</p>
                <?php endif; ?>
            </div>
            <div class="view-more">
                <a href="categories.php?filter=new" class="btn-secondary">View All New Arrivals</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Subscribe to Our Newsletter</h2>
                <p>Stay updated with our latest collections and exclusive offers</p>
                <form id="newsletter-form" action="subscribe.php" method="post">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn-primary">Subscribe</button>
                </form>
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

    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>