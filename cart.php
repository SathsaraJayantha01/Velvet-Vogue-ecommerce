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
        'user_id' => $_SESSION['user_id'] ?? '',
        'role' => $_SESSION['role'] ?? 'customer'
    ];
}

// Get cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartCount = count($cartItems);

// Calculate cart totals
$subtotal = 0;
$shipping = 0; // Free shipping for now
$discount = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$total = $subtotal - $discount + $shipping;

// Function to get recommended products
function getRecommendedProducts($conn, $limit = 4) {
    $products = [];
    
    // First try to get featured products
    $sql = "SELECT * FROM products WHERE featured = 1 AND status = 'active' LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows < $limit) {
        // If not enough featured products, get new arrivals
        $stmt->close();
        
        $sql = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    $stmt->close();
    return $products;
}

// Get recommended products
$recommendedProducts = getRecommendedProducts($conn);

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
    <title>Shopping Cart - Velvet Vogue</title>
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
                    <a href="cart.php" class="cart-icon active"><i class="fas fa-shopping-cart"></i> Cart (<span id="cart-count"><?php echo $cartCount; ?></span>)</a>
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>Shopping Cart</li>
            </ul>
        </div>
    </div>

    <!-- Shopping Cart Section -->
    <section class="shopping-cart">
        <div class="container">
            <div class="section-title">
                <h1>Shopping Cart</h1>
            </div>
            
            <div class="cart-container" id="cart-container">
                <!-- This section will be shown if the cart is empty -->
                <div class="empty-cart" id="empty-cart" <?php if(count($cartItems) > 0): ?>style="display: none;"<?php endif; ?>>
                    <div class="empty-cart-content">
                        <i class="fas fa-shopping-cart"></i>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added any products to your cart yet.</p>
                        <a href="categories.php" class="btn-primary">Start Shopping</a>
                    </div>
                </div>
                
                <!-- This section will be shown if the cart has items -->
                <div class="cart-content" id="cart-with-items" <?php if(count($cartItems) == 0): ?>style="display: none;"<?php endif; ?>>
                    <div class="cart-items">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th class="product-col">Product</th>
                                    <th class="price-col">Price</th>
                                    <th class="quantity-col">Quantity</th>
                                    <th class="subtotal-col">Subtotal</th>
                                    <th class="remove-col"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items-container">
                                <?php if(count($cartItems) > 0): ?>
                                    <?php foreach($cartItems as $itemId => $item): ?>
                                        <tr class="cart-item" data-id="<?php echo $itemId; ?>">
                                            <td class="product-col">
                                                <div class="product-info">
                                                    <div class="product-image">
                                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                    </div>
                                                    <div class="product-details">
                                                        <h3><a href="product.php?id=<?php echo $itemId; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                                                        <div class="product-meta">
                                                            <?php if(isset($item['size'])): ?>
                                                                <p>Size: <span><?php echo htmlspecialchars($item['size']); ?></span></p>
                                                            <?php endif; ?>
                                                            <?php if(isset($item['color'])): ?>
                                                                <p>Color: <span><?php echo htmlspecialchars($item['color']); ?></span></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="price-col"><?php echo formatCurrency($item['price']); ?></td>
                                            <td class="quantity-col">
                                                <div class="quantity-selector">
                                                    <button class="quantity-btn minus" data-id="<?php echo $itemId; ?>"><i class="fas fa-minus"></i></button>
                                                    <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="10" data-id="<?php echo $itemId; ?>">
                                                    <button class="quantity-btn plus" data-id="<?php echo $itemId; ?>"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </td>
                                            <td class="subtotal-col"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                                            <td class="remove-col">
                                                <button class="remove-item" data-id="<?php echo $itemId; ?>"><i class="fas fa-times"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="cart-actions">
                        <div class="coupon">
                            <input type="text" placeholder="Coupon code" id="coupon-code">
                            <button class="btn-secondary apply-coupon" id="apply-coupon">Apply Coupon</button>
                        </div>
                        <button class="btn-secondary update-cart" id="update-cart">Update Cart</button>
                    </div>
                    
                    <div class="cart-summary-container">
                        <div class="cart-summary">
                            <h2>Cart Summary</h2>
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="cart-subtotal"><?php echo formatCurrency($subtotal); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span id="cart-shipping"><?php echo $shipping > 0 ? formatCurrency($shipping) : 'Free'; ?></span>
                            </div>
                            <div class="summary-row discount" id="discount-row" <?php if($discount <= 0): ?>style="display: none;"<?php endif; ?>>
                                <span>Discount</span>
                                <span id="cart-discount"><?php echo formatCurrency($discount * -1); ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="cart-total"><?php echo formatCurrency($total); ?></span>
                            </div>
                            <button id="checkout-btn" class="btn-primary btn-block">Proceed to Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- You May Also Like Section -->
            <section class="related-products">
                <div class="section-title">
                    <h2>You May Also Like</h2>
                </div>
                <div class="product-grid" id="recommended-products">
                    <?php if(count($recommendedProducts) > 0): ?>
                        <?php foreach($recommendedProducts as $product): ?>
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
                                            <span class="current-price"><?php echo formatCurrency($product['sale_price']); ?></span>
                                            <span class="old-price"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="current-price"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="add-to-cart-btn" data-product="<?php echo $product['product_id']; ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recommended products available at the moment.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </section>

    <!-- Checkout Modal -->
    <div class="modal" id="checkout-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Checkout</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="checkout-form" action="process_order.php" method="post">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="zip">ZIP Code</label>
                                <input type="text" id="zip" name="zip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <option value="AU">Australia</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="credit-card" name="payment_method" value="credit-card" checked>
                                <label for="credit-card">Credit Card</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                <label for="paypal">PayPal</label>
                            </div>
                        </div>
                        
                        <div id="credit-card-fields">
                            <div class="form-group">
                                <label for="card-number">Card Number</label>
                                <input type="text" id="card-number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry-date">Expiry Date</label>
                                    <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="XXX">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="card-name">Name on Card</label>
                                <input type="text" id="card-name" name="card_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="modal-subtotal"><?php echo formatCurrency($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span id="modal-shipping"><?php echo $shipping > 0 ? formatCurrency($shipping) : 'Free'; ?></span>
                        </div>
                        <div class="summary-row discount" id="modal-discount-row" <?php if($discount <= 0): ?>style="display: none;"<?php endif; ?>>
                            <span>Discount</span>
                            <span id="modal-discount"><?php echo formatCurrency($discount * -1); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="modal-total"><?php echo formatCurrency($total); ?></span>
                        </div>
                        
                        <!-- Hidden fields to pass cart data -->
                        <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                        <input type="hidden" name="shipping" value="<?php echo $shipping; ?>">
                        <input type="hidden" name="discount" value="<?php echo $discount; ?>">
                        <input type="hidden" name="total" value="<?php echo $total; ?>">
                        <input type="hidden" name="cart_items" value="<?php echo htmlspecialchars(json_encode($cartItems)); ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Place Order</button>
                        <button type="button" class="btn-secondary cancel-checkout">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <script>
    // Initialize cart data for JavaScript
    const cartData = {
        items: <?php echo json_encode($cartItems); ?>,
        subtotal: <?php echo $subtotal; ?>,
        shipping: <?php echo $shipping; ?>,
        discount: <?php echo $discount; ?>,
        total: <?php echo $total; ?>
    };
    </script>
    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>