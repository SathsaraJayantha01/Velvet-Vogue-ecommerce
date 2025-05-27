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

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 1; // Default to product ID 1 if none specified

// In a real application, you would fetch product data from the database here
// For now, we'll use static data for demonstration
$product = [
    'id' => $productId,
    'name' => 'Classic Men\'s Casual Shirt',
    'price' => 79.99,
    'sale_price' => 59.99,
    'sku' => 'VV-MS-1001',
    'category' => 'Men, Casual Wear',
    'description' => 'A comfortable and stylish casual shirt for men. Made with 100% premium cotton, this versatile shirt is perfect for casual outings or semi-formal occasions. Features a classic fit, button-down collar, and durable stitching.',
    'in_stock' => true,
    'images' => [
        'casual-shirt-1.jpg',
        'casual-shirt-2.jpg',
        'casual-shirt-3.jpg',
        'casual-shirt-4.jpg'
    ]
];

// Calculate discount percentage
$discountPercent = 0;
if (isset($product['sale_price']) && $product['sale_price'] < $product['price'] && $product['price'] > 0) {
    $discountPercent = round(($product['price'] - $product['sale_price']) / $product['price'] * 100);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Velvet Vogue</title>
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
            <ul id="product-breadcrumb">
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Products</a></li>
                <li><a href="categories.php?gender=men">Men</a></li>
                <li><a href="categories.php?type=casual">Casual Wear</a></li>
                <li><?php echo htmlspecialchars($product['name']); ?></li>
            </ul>
        </div>
    </div>

    <!-- Product Detail Section -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-content">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="images/products/<?php echo htmlspecialchars($product['images'][0]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="main-product-image">
                    </div>
                    <div class="thumbnail-gallery">
                        <?php foreach($product['images'] as $index => $image): ?>
                        <div class="thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>" data-image="images/products/<?php echo htmlspecialchars($image); ?>">
                            <img src="images/products/<?php echo htmlspecialchars($image); ?>" alt="Product Thumbnail <?php echo $index + 1; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-meta">
                        <div class="product-sku">SKU: <?php echo htmlspecialchars($product['sku']); ?></div>
                        <div class="product-category">Category: <a href="categories.php?gender=men">Men</a>, <a href="categories.php?type=casual">Casual Wear</a></div>
                        <div class="product-availability"><i class="fas fa-check-circle"></i> In Stock</div>
                    </div>
                    
                    <div class="product-price">
                        <span class="current-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                        <span class="old-price">$<?php echo number_format($product['price'], 2); ?></span>
                        <span class="discount">-<?php echo $discountPercent; ?>%</span>
                    </div>
                    
                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                    
                    <div class="product-options">
                        <div class="option-group">
                            <label>Size:</label>
                            <div class="size-options">
                                <button class="size-btn" data-size="s">S</button>
                                <button class="size-btn active" data-size="m">M</button>
                                <button class="size-btn" data-size="l">L</button>
                                <button class="size-btn" data-size="xl">XL</button>
                                <button class="size-btn disabled" data-size="xxl">XXL</button>
                            </div>
                            <a href="#" class="size-guide-link">Size Guide</a>
                        </div>
                        
                        <div class="option-group">
                            <label>Color:</label>
                            <div class="color-options">
                                <div class="color-option active" style="background-color: #3a5295;" data-color="Blue"></div>
                                <div class="color-option" style="background-color: #222222;" data-color="Black"></div>
                                <div class="color-option" style="background-color: #f5f5f5;" data-color="White"></div>
                                <div class="color-option" style="background-color: #B76E79;" data-color="Rose"></div>
                            </div>
                        </div>
                        
                        <div class="option-group">
                            <label>Quantity:</label>
                            <div class="quantity-selector">
                                <button class="minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="10" id="quantity">
                                <button class="plus"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-delivery">
                        <button class="btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                        <button class="btn-secondary wishlist-btn" data-product-id="<?php echo $product['id']; ?>"><i class="far fa-heart"></i> Add to Wishlist</button>
                    </div>
                    
                    <div class="product-delivery">
                        <div class="delivery-info">
                            <i class="fas fa-truck"></i>
                            <span>Free shipping on orders over $50</span>
                        </div>
                        <div class="delivery-info">
                            <i class="fas fa-exchange-alt"></i>
                            <span>30-day easy returns</span>
                        </div>
                        <div class="delivery-info">
                            <i class="fas fa-shield-alt"></i>
                            <span>2-year warranty</span>
                        </div>
                    </div>
                    
                    <div class="product-share">
                        <span>Share:</span>
                        <div class="social-share">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-pinterest"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tabs-nav">
                    <button class="tab-btn active" data-tab="description">Description</button>
                    <button class="tab-btn" data-tab="specifications">Specifications</button>
                    <button class="tab-btn" data-tab="reviews">Reviews (12)</button>
                    <button class="tab-btn" data-tab="shipping">Shipping & Returns</button>
                </div>
                
                <div class="tabs-content">
                    <!-- Description Tab -->
                    <div class="tab-panel active" id="description-panel">
                        <div class="product-full-description">
                            <h3>Product Description</h3>
                            <p>Elevate your casual wardrobe with our Classic Men's Casual Shirt. Crafted from premium 100% cotton, this versatile piece offers exceptional comfort and breathability for all-day wear.</p>
                            
                            <p>The classic fit strikes the perfect balance between relaxed and tailored, making it suitable for various occasions from casual outings to semi-formal events. The timeless button-down collar adds a touch of sophistication, while the durable double-stitching ensures longevity.</p>
                            
                            <h4>Features:</h4>
                            <ul>
                                <li>Premium 100% cotton fabric</li>
                                <li>Classic fit with room for movement</li>
                                <li>Button-down collar</li>
                                <li>Single chest pocket</li>
                                <li>Reinforced stitching at stress points</li>
                                <li>Machine washable</li>
                            </ul>
                            
                            <h4>Care Instructions:</h4>
                            <p>Machine wash cold with similar colors. Tumble dry low. Warm iron if needed. Do not bleach.</p>
                        </div>
                    </div>
                    
                    <!-- Specifications Tab -->
                    <div class="tab-panel" id="specifications-panel">
                        <div class="product-specifications">
                            <h3>Product Specifications</h3>
                            <table class="specs-table">
                                <tr>
                                    <th>Material</th>
                                    <td>100% Premium Cotton</td>
                                </tr>
                                <tr>
                                    <th>Fit</th>
                                    <td>Classic Fit</td>
                                </tr>
                                <tr>
                                    <th>Collar</th>
                                    <td>Button-Down</td>
                                </tr>
                                <tr>
                                    <th>Sleeve</th>
                                    <td>Long Sleeve with Button Cuff</td>
                                </tr>
                                <tr>
                                    <th>Closure</th>
                                    <td>Button Front</td>
                                </tr>
                                <tr>
                                    <th>Pocket</th>
                                    <td>Single Chest Pocket</td>
                                </tr>
                                <tr>
                                    <th>Weight</th>
                                    <td>Medium (180 GSM)</td>
                                </tr>
                                <tr>
                                    <th>Care</th>
                                    <td>Machine Washable</td>
                                </tr>
                                <tr>
                                    <th>Origin</th>
                                    <td>Imported</td>
                                </tr>
                                <tr>
                                    <th>Model Size</th>
                                    <td>Model is 6'1" and wearing size M</td>
                                </tr>
                            </table>
                            
                            <h4>Size Chart (in inches)</h4>
                            <table class="size-chart">
                                <tr>
                                    <th>Size</th>
                                    <th>Chest</th>
                                    <th>Waist</th>
                                    <th>Length</th>
                                    <th>Sleeve</th>
                                </tr>
                                <tr>
                                    <td>S</td>
                                    <td>38-40</td>
                                    <td>36-38</td>
                                    <td>28</td>
                                    <td>33</td>
                                </tr>
                                <tr>
                                    <td>M</td>
                                    <td>40-42</td>
                                    <td>38-40</td>
                                    <td>29</td>
                                    <td>34</td>
                                </tr>
                                <tr>
                                    <td>L</td>
                                    <td>42-44</td>
                                    <td>40-42</td>
                                    <td>30</td>
                                    <td>35</td>
                                </tr>
                                <tr>
                                    <td>XL</td>
                                    <td>44-46</td>
                                    <td>42-44</td>
                                    <td>31</td>
                                    <td>36</td>
                                </tr>
                                <tr>
                                    <td>XXL</td>
                                    <td>46-48</td>
                                    <td>44-46</td>
                                    <td>32</td>
                                    <td>37</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Reviews Tab -->
                    <div class="tab-panel" id="reviews-panel">
                        <div class="product-reviews">
                            <h3>Customer Reviews</h3>
                            <div class="reviews-summary">
                                <div class="average-rating">
                                    <div class="rating-value">4.5</div>
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <div class="rating-count">Based on 12 reviews</div>
                                </div>
                                
                                <div class="rating-breakdown">
                                    <div class="rating-bar">
                                        <span class="rating-level">5 Stars</span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: 75%"></div>
                                        </div>
                                        <span class="rating-percent">75%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span class="rating-level">4 Stars</span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: 15%"></div>
                                        </div>
                                        <span class="rating-percent">15%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span class="rating-level">3 Stars</span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: 5%"></div>
                                        </div>
                                        <span class="rating-percent">5%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span class="rating-level">2 Stars</span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: 3%"></div>
                                        </div>
                                        <span class="rating-percent">3%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span class="rating-level">1 Star</span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: 2%"></div>
                                        </div>
                                        <span class="rating-percent">2%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="reviews-list">
                                <?php
                                // Sample review data - in a real application, this would come from database
                                $reviews = [
                                    [
                                        'author' => 'John D.',
                                        'rating' => 5,
                                        'date' => 'June 15, 2023',
                                        'title' => 'Excellent quality and fit',
                                        'content' => 'I\'ve purchased many casual shirts over the years, but this one stands out for its quality. The fabric is soft yet durable, and the fit is perfect. I\'m 6\'0" and the medium size fits me perfectly. Would definitely buy again in other colors.'
                                    ],
                                    [
                                        'author' => 'Michael T.',
                                        'rating' => 4,
                                        'date' => 'May 28, 2023',
                                        'title' => 'Great shirt, slightly larger than expected',
                                        'content' => 'The quality of this shirt is excellent, and I love the color. My only issue is that it runs slightly larger than expected. I usually wear a medium, but this fits more like a large. Otherwise, very comfortable and well-made.'
                                    ],
                                    [
                                        'author' => 'Sarah K.',
                                        'rating' => 4.5,
                                        'date' => 'May 10, 2023',
                                        'title' => 'Bought for my husband, he loves it!',
                                        'content' => 'I purchased this shirt as a gift for my husband and he absolutely loves it. The fabric is high quality and the blue color is beautiful. It\'s become his go-to shirt for both casual and semi-formal occasions. Will be buying more in different colors.'
                                    ]
                                ];
                                
                                foreach($reviews as $review):
                                ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-author"><?php echo htmlspecialchars($review['author']); ?></div>
                                        <div class="review-rating">
                                            <?php
                                            for($i = 1; $i <= 5; $i++) {
                                                if($i <= floor($review['rating'])) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } elseif($i - 0.5 == floor($review['rating'])) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="review-date"><?php echo htmlspecialchars($review['date']); ?></div>
                                    </div>
                                    <div class="review-title"><?php echo htmlspecialchars($review['title']); ?></div>
                                    <div class="review-content">
                                        <p><?php echo htmlspecialchars($review['content']); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="pagination">
                                <button class="pagination-btn active">1</button>
                                <button class="pagination-btn">2</button>
                                <button class="pagination-btn">3</button>
                                <button class="pagination-btn">4</button>
                                <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            
                            <div class="write-review">
                                <h3>Write a Review</h3>
                                <form id="review-form" action="submit_review.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="form-group">
                                        <label for="review-name">Your Name</label>
                                        <input type="text" id="review-name" name="name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="review-email">Your Email</label>
                                        <input type="email" id="review-email" name="email" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Your Rating</label>
                                        <div class="rating-select">
                                            <i class="far fa-star" data-rating="1"></i>
                                            <i class="far fa-star" data-rating="2"></i>
                                            <i class="far fa-star" data-rating="3"></i>
                                            <i class="far fa-star" data-rating="4"></i>
                                            <i class="far fa-star" data-rating="5"></i>
                                            <input type="hidden" name="rating" id="rating-value" value="0">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="review-title">Review Title</label>
                                        <input type="text" id="review-title" name="title" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="review-content">Your Review</label>
                                        <textarea id="review-content" name="content" rows="5" required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn-primary">Submit Review</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping & Returns Tab -->
                    <div class="tab-panel" id="shipping-panel">
                        <div class="shipping-returns">
                            <h3>Shipping Information</h3>
                            <p>We offer the following shipping options for all orders:</p>
                            
                            <table class="shipping-table">
                                <tr>
                                    <th>Shipping Method</th>
                                    <th>Estimated Delivery</th>
                                    <th>Cost</th>
                                </tr>
                                <tr>
                                    <td>Standard Shipping</td>
                                    <td>3-5 Business Days</td>
                                    <td>Free on orders over $50<br>$4.99 for orders under $50</td>
                                </tr>
                                <tr>
                                    <td>Express Shipping</td>
                                    <td>2 Business Days</td>
                                    <td>$9.99</td>
                                </tr>
                                <tr>
                                    <td>Next Day Delivery</td>
                                    <td>Next Business Day</td>
                                    <td>$14.99</td>
                                </tr>
                            </table>
                            
                            <p>Please note that business days are Monday through Friday, excluding holidays. Orders placed after 1 PM EST will be processed the following business day.</p>
                            
                            <h3>Return Policy</h3>
                            <p>We want you to be completely satisfied with your purchase. If for any reason you're not happy with your order, we accept returns within 30 days of delivery.</p>
                            
                            <h4>Return Process:</h4>
                            <ol>
                                <li>Log in to your account and navigate to your order history.</li>
                                <li>Select the order and items you wish to return.</li>
                                <li>Print the prepaid return shipping label.</li>
                                <li>Pack the item(s) in their original packaging if possible.</li>
                                <li>Drop off the package at your nearest postal service location.</li>
                            </ol>
                            
                            <h4>Return Conditions:</h4>
                            <ul>
                                <li>Items must be unworn, unwashed, and with all original tags attached.</li>
                                <li>Returned items must be in their original condition and packaging.</li>
                                <li>Sale items can only be returned for store credit.</li>
                                <li>Refunds will be issued to the original payment method within 5-7 business days after we receive your return.</li>
                            </ul>
                            
                            <p>For any questions about shipping or returns, please contact our customer service team at <a href="mailto:support@velvetvogue.com">support@velvetvogue.com</a> or call us at +1 234 567 8900.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Products Section -->
            <section class="related-products">
                <div class="section-title">
                    <h2>You May Also Like</h2>
                    <p>Products similar to what you're viewing</p>
                </div>
                <div class="product-grid" id="related-products">
                    <?php
                    // Sample related products - in a real application, this would come from database
                    $related_products = [
                        [
                            'id' => 2,
                            'name' => 'Premium Cotton Casual Shirt',
                            'price' => 49.99,
                            'sale_price' => null,
                            'image' => 'PNG/0022.jpeg'
                        ],
                        [
                            'id' => 3,
                            'name' => 'Linen Summer Shirt',
                            'price' => 69.99,
                            'sale_price' => 54.99,
                            'image' => 'PNG/0023.jpg'
                        ],
                        [
                            'id' => 4,
                            'name' => 'Slim Fit Button-Down Shirt',
                            'price' => 64.99,
                            'sale_price' => null,
                            'image' => 'PNG/0024.jpeg'
                        ],
                        [
                            'id' => 5,
                            'name' => 'Check Pattern Casual Shirt',
                            'price' => 59.99,
                            'sale_price' => null,
                            'image' => 'PNG/0025.jpeg',
                            'is_new' => true
                        ]
                    ];
                    
                    foreach($related_products as $related_product):
                    ?>
                    <div class="product-card">
                        <?php if(isset($related_product['sale_price']) && $related_product['sale_price']): ?>
                            <div class="product-badge sale">Sale</div>
                        <?php elseif(isset($related_product['is_new']) && $related_product['is_new']): ?>
                            <div class="product-badge new">New</div>
                        <?php endif; ?>
                        <div class="product-image">
                            <a href="product.php?id=<?php echo $related_product['id']; ?>">
                                <img src="images/products/<?php echo htmlspecialchars($related_product['image']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>">
                            </a>
                            <div class="product-actions">
                                <button class="add-to-cart" data-id="<?php echo $related_product['id']; ?>"><i class="fas fa-shopping-cart"></i></button>
                                <button class="add-to-wishlist" data-id="<?php echo $related_product['id']; ?>"><i class="far fa-heart"></i></button>
                                <button class="quick-view" data-id="<?php echo $related_product['id']; ?>"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product.php?id=<?php echo $related_product['id']; ?>"><?php echo htmlspecialchars($related_product['name']); ?></a></h3>
                            <div class="product-price">
                                <?php if(isset($related_product['sale_price']) && $related_product['sale_price']): ?>
                                    <span class="current-price">$<?php echo number_format($related_product['sale_price'], 2); ?></span>
                                    <span class="old-price">$<?php echo number_format($related_product['price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="current-price">$<?php echo number_format($related_product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
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
                        <li><a href="shipping.php">Shipping & Returns</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Fashion Street, New York, NY 10001</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-envelope"></i> info@velvetvogue.com</li>
                        <li><i class="fas fa-clock"></i> Mon-Fri: 9:00 AM - 8:00 PM</li>
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
    <script src="js/product.js"></script>
</body>
</html>