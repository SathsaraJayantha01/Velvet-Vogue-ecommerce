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

// Last updated date for shipping policy
$lastUpdated = "April 15, 2025";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping & Returns - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .shipping-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        
        .shipping-content h2 {
            color: var(--primary-color);
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .shipping-content h3 {
            color: var(--dark-color);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        
        .shipping-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .shipping-content ul, .shipping-content ol {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .shipping-content ul li, .shipping-content ol li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .shipping-content .updated-date {
            font-style: italic;
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        
        .shipping-content .highlight {
            background-color: rgba(106, 27, 154, 0.1);
            padding: 1.5rem;
            border-radius: 5px;
            margin: 1.5rem 0;
        }
        
        .shipping-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .shipping-table th,
        .shipping-table td {
            padding: 0.75rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .shipping-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        .shipping-method {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .shipping-method:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }
        
        .method-icon {
            font-size: 2rem;
            margin-right: 1.5rem;
            color: var(--primary-color);
            min-width: 50px;
            text-align: center;
        }
        
        .method-details {
            flex-grow: 1;
        }
        
        .method-details h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        
        .method-price {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
            margin-left: 1.5rem;
            white-space: nowrap;
        }
        
        .steps-container {
            margin: 2rem 0;
        }
        
        .steps-list {
            counter-reset: step-counter;
            list-style-type: none;
            padding-left: 0;
        }
        
        .steps-list li {
            position: relative;
            padding-left: 3.5rem;
            margin-bottom: 1.5rem;
            counter-increment: step-counter;
        }
        
        .steps-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background-color: var(--primary-color);
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .question-box {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 5px;
            margin: 1.5rem 0;
            border-left: 4px solid var(--primary-color);
        }
        
        .question-box h3 {
            margin-top: 0;
        }
        
        @media (max-width: 768px) {
            .shipping-method {
                flex-direction: column;
                text-align: center;
            }
            
            .method-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .method-price {
                margin-left: 0;
                margin-top: 1rem;
            }
            
            .shipping-table {
                font-size: 0.9rem;
            }
        }
    </style>
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
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>Shipping & Returns</li>
            </ul>
        </div>
    </div>

    <!-- Shipping & Returns Section -->
    <section class="shipping-section">
        <div class="container">
            <div class="section-title">
                <h1>Shipping & Returns</h1>
                <p>Information about our shipping methods, delivery times, and return policy</p>
            </div>
            
            <div class="shipping-content">
                <p class="updated-date">Last Updated: <?php echo $lastUpdated; ?></p>
                
                <div class="highlight">
                    <p>At Velvet Vogue, we want to make your shopping experience as smooth as possible. That's why we offer fast, reliable shipping options and a hassle-free return policy. Please read below for detailed information on our shipping methods, delivery times, and return procedures.</p>
                </div>
                
                <h2>Shipping Information</h2>
                
                <h3>Shipping Methods</h3>
                <p>We offer the following shipping methods to ensure your order reaches you in a timely manner:</p>
                
                <div class="shipping-method">
                    <div class="method-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="method-details">
                        <h3>Standard Shipping</h3>
                        <p>Delivery within 3-5 business days (Monday-Friday) within the continental United States.</p>
                    </div>
                    <div class="method-price">
                        FREE on orders $50+<br>
                        $5.99 on orders under $50
                    </div>
                </div>
                
                <div class="shipping-method">
                    <div class="method-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="method-details">
                        <h3>Express Shipping</h3>
                        <p>Delivery within 1-2 business days (Monday-Friday) within the continental United States.</p>
                    </div>
                    <div class="method-price">
                        $12.99
                    </div>
                </div>
                
                <div class="shipping-method">
                    <div class="method-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <div class="method-details">
                        <h3>International Shipping</h3>
                        <p>Delivery within 7-14 business days to most countries worldwide. Please note that international orders may be subject to customs duties and taxes imposed by the destination country.</p>
                    </div>
                    <div class="method-price">
                        Starting at $19.99
                    </div>
                </div>
                
                <h3>Shipping Restrictions</h3>
                <p>We currently ship to most countries worldwide. However, there may be restrictions for certain locations. If you're unsure whether we ship to your country, please contact our customer service team at <a href="mailto:shipping@velvetvogue.com">shipping@velvetvogue.com</a>.</p>
                
                <h3>Shipping Timeframes</h3>
                <p>Below are the estimated delivery timeframes for different regions:</p>
                
                <table class="shipping-table">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Standard Shipping</th>
                            <th>Express Shipping</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Continental United States</td>
                            <td>3-5 business days</td>
                            <td>1-2 business days</td>
                        </tr>
                        <tr>
                            <td>Alaska and Hawaii</td>
                            <td>5-7 business days</td>
                            <td>2-3 business days</td>
                        </tr>
                        <tr>
                            <td>Canada</td>
                            <td>7-10 business days</td>
                            <td>3-5 business days</td>
                        </tr>
                        <tr>
                            <td>Europe</td>
                            <td>10-14 business days</td>
                            <td>5-7 business days</td>
                        </tr>
                        <tr>
                            <td>Asia Pacific</td>
                            <td>12-16 business days</td>
                            <td>7-10 business days</td>
                        </tr>
                        <tr>
                            <td>Rest of World</td>
                            <td>14-21 business days</td>
                            <td>10-14 business days</td>
                        </tr>
                    </tbody>
                </table>
                
                <p>Please note that these timeframes are estimates and may vary due to customs processing, weather conditions, or other unforeseen circumstances. Business days do not include weekends or holidays.</p>
                
                <h3>Order Processing</h3>
                <p>All orders are processed within 1-2 business days after payment confirmation. Orders placed after 2:00 PM EST will be processed the following business day. Once your order has been processed and shipped, you will receive a shipping confirmation email with tracking information.</p>
                
                <h3>Tracking Your Order</h3>
                <p>You can track your order by:</p>
                <ol>
                    <li>Clicking on the tracking link in your shipping confirmation email</li>
                    <li>Logging into your account and viewing your order history</li>
                    <li>Contacting our customer service team with your order number</li>
                </ol>
                
                <div class="question-box">
                    <h3>Where is my order?</h3>
                    <p>If you have concerns about your order status, please follow these steps:</p>
                    <ol>
                        <li>Check your tracking information using the methods mentioned above</li>
                        <li>Verify that your shipping address is correct</li>
                        <li>Wait until after the estimated delivery date before reporting a missing package</li>
                        <li>If your package shows as delivered but you haven't received it, check with neighbors and your local post office</li>
                        <li>Contact our customer service if you still can't locate your package</li>
                    </ol>
                </div>
                
                <h2>Returns Policy</h2>
                
                <h3>Return Eligibility</h3>
                <p>We want you to love your Velvet Vogue purchases. If you're not completely satisfied, we accept returns under the following conditions:</p>
                <ul>
                    <li>Items must be returned within 30 days of delivery</li>
                    <li>Products must be unworn, unwashed, and have all original tags attached</li>
                    <li>Items must be in their original packaging</li>
                    <li>Final sale items (marked as such on the product page) are not eligible for return</li>
                    <li>Swimwear, underwear, and accessories like jewelry and hair accessories cannot be returned for hygiene reasons, unless defective</li>
                </ul>
                
                <h3>Return Process</h3>
                <p>To return an item, please follow these steps:</p>
                
                <div class="steps-container">
                    <ol class="steps-list">
                        <li>
                            <strong>Initiate your return</strong>
                            <p>Log into your account, go to "My Orders," find the order containing the item(s) you wish to return, and click on "Return Items." If you checked out as a guest, use our <a href="contact.php">contact form</a> to request a return authorization.</p>
                        </li>
                        <li>
                            <strong>Print your return label</strong>
                            <p>Once your return is approved, you'll receive an email with a pre-paid return shipping label (for domestic returns). International customers are responsible for return shipping costs.</p>
                        </li>
                        <li>
                            <strong>Package your items</strong>
                            <p>Place the items in their original packaging if possible, or in a secure package. Include all tags and accessories that came with the product.</p>
                        </li>
                        <li>
                            <strong>Ship your return</strong>
                            <p>Attach the return label to your package and drop it off at the designated carrier location. Keep your tracking number for reference.</p>
                        </li>
                        <li>
                            <strong>Refund processing</strong>
                            <p>Once we receive and inspect your return (usually within 2-3 business days), we'll process your refund. The refund will be issued to your original payment method and may take an additional 3-5 business days to appear on your statement.</p>
                        </li>
                    </ol>
                </div>
                
                <h3>Exchanges</h3>
                <p>We do offer exchanges for different sizes or colors of the same item. To request an exchange, follow the same process as returns but select "Exchange" instead of "Return" and specify the new size or color you want. Please note that exchanges are subject to item availability.</p>
                
                <h3>Return Shipping Costs</h3>
                <p>For customers in the United States, return shipping is free. For international returns, customers are responsible for return shipping costs and any applicable duties or taxes.</p>
                
                <h3>Damaged or Defective Items</h3>
                <p>If you receive a damaged or defective item, please contact our customer service team immediately. We'll provide a pre-paid return label and process a replacement or refund once we receive the damaged item. Please include photos of the damage when you contact us to expedite the process.</p>
                
                <div class="question-box">
                    <h3>Need Help With Your Return?</h3>
                    <p>If you have any questions about returns or exchanges, our customer service team is ready to help. You can contact us by:</p>
                    <ul>
                        <li>Email: <a href="mailto:returns@velvetvogue.com">returns@velvetvogue.com</a></li>
                        <li>Phone: +1 234 567 8900 (Monday-Friday, 9AM-5PM EST)</li>
                        <li>Live Chat: Available on our website during business hours</li>
                    </ul>
                </div>
                
                <h2>Shipping FAQs</h2>
                
                <h3>Do you offer free shipping?</h3>
                <p>Yes, we offer free standard shipping on all orders over $50 within the continental United States.</p>
                
                <h3>How long will it take to receive my order?</h3>
                <p>Standard shipping typically takes 3-5 business days within the continental United States. Express shipping is 1-2 business days. International shipping varies by location but generally takes 7-14 business days.</p>
                
                <h3>Do you ship internationally?</h3>
                <p>Yes, we ship to most countries worldwide. International shipping costs are calculated at checkout based on destination and package weight.</p>
                
                <h3>Can I change my shipping address after placing an order?</h3>
                <p>If your order hasn't been processed yet, we may be able to change the shipping address. Please contact our customer service team immediately with your order number to request this change.</p>
                
                <h3>What happens if my package is lost or damaged during shipping?</h3>
                <p>If your package is lost or damaged during transit, please contact our customer service team within 48 hours of the delivery date. We'll work with the shipping carrier to locate your package or process a replacement or refund as appropriate.</p>
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
                        <li><a href="shipping.php" class="active">Shipping & Returns</a></li>
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