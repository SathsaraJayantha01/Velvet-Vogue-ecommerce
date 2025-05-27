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

// Last updated date for privacy policy
$lastUpdated = "April 15, 2025";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .privacy-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        
        .privacy-content h2 {
            color: var(--primary-color);
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .privacy-content h3 {
            color: var(--dark-color);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        
        .privacy-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .privacy-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .privacy-content ul li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .privacy-content .updated-date {
            font-style: italic;
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        
        .privacy-content .highlight {
            background-color: rgba(106, 27, 154, 0.1);
            padding: 1.5rem;
            border-radius: 5px;
            margin: 1.5rem 0;
        }
        
        .privacy-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .privacy-content table th,
        .privacy-content table td {
            padding: 0.75rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .privacy-content table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        .privacy-content .contact-info-block {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 5px;
            margin: 1.5rem 0;
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
                <li>Privacy Policy</li>
            </ul>
        </div>
    </div>

    <!-- Privacy Policy Section -->
    <section class="privacy-section">
        <div class="container">
            <div class="section-title">
                <h1>Privacy Policy</h1>
                <p>How we collect, use, and protect your personal information</p>
            </div>
            
            <div class="privacy-content">
                <p class="updated-date">Last Updated: <?php echo $lastUpdated; ?></p>
                
                <div class="highlight">
                    <p>At Velvet Vogue, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a purchase.</p>
                    <p>Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>
                </div>
                
                <h2>Information We Collect</h2>
                <p>We collect information about you in various ways when you use our website. This information may include:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, postal address, phone number, and other information you provide when creating an account, making a purchase, or contacting customer service.</li>
                    <li><strong>Transaction Information:</strong> Purchase history, payment details, shipping information, and product preferences.</li>
                    <li><strong>Log Data:</strong> IP address, browser type, pages visited, time spent on pages, access times, and referring website addresses.</li>
                    <li><strong>Device Information:</strong> Information about your device, including type, operating system, and unique device identifiers.</li>
                    <li><strong>Cookies and Tracking Technologies:</strong> Information collected through cookies, web beacons, and other tracking technologies. Learn more in our Cookie Policy section.</li>
                </ul>
                
                <h2>How We Use Your Information</h2>
                <p>We may use the information we collect for various purposes, including to:</p>
                <ul>
                    <li>Process and fulfill your orders</li>
                    <li>Communicate with you about your orders, products, services, and promotional offers</li>
                    <li>Create and maintain your account</li>
                    <li>Provide customer support</li>
                    <li>Improve our website, products, and services</li>
                    <li>Personalize your shopping experience</li>
                    <li>Administer promotions, surveys, or contests</li>
                    <li>Detect, investigate, and prevent fraudulent transactions and other illegal activities</li>
                    <li>Comply with our legal obligations</li>
                </ul>
                
                <h2>How We Share Your Information</h2>
                <p>We may share your information with third parties in certain circumstances:</p>
                <ul>
                    <li><strong>Service Providers:</strong> We share information with third-party vendors and service providers who perform services on our behalf, such as payment processing, shipping, data analysis, email delivery, and customer service.</li>
                    <li><strong>Business Partners:</strong> We may share information with business partners to offer you certain products, services, or promotions.</li>
                    <li><strong>Legal Requirements:</strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities.</li>
                    <li><strong>Business Transfers:</strong> If we're involved in a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</li>
                    <li><strong>With Your Consent:</strong> We may share your information with third parties when you have given us your consent to do so.</li>
                </ul>
                
                <h3>Third-Party Sharing</h3>
                <p>We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties except as described in this policy.</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Category of Third Party</th>
                            <th>Purpose</th>
                            <th>Information Shared</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Payment Processors</td>
                            <td>Process transactions</td>
                            <td>Name, credit card information, billing address</td>
                        </tr>
                        <tr>
                            <td>Shipping Companies</td>
                            <td>Deliver orders</td>
                            <td>Name, shipping address, phone number</td>
                        </tr>
                        <tr>
                            <td>Email Marketing Services</td>
                            <td>Send newsletters and promotional offers</td>
                            <td>Name, email address</td>
                        </tr>
                        <tr>
                            <td>Analytics Providers</td>
                            <td>Analyze website traffic and usage</td>
                            <td>IP address, browsing behavior, device information</td>
                        </tr>
                    </tbody>
                </table>
                
                <h2>Your Choices</h2>
                <p>You have certain choices about how we use your information:</p>
                <ul>
                    <li><strong>Account Information:</strong> You can review and update your account information by logging into your account settings.</li>
                    <li><strong>Marketing Communications:</strong> You can opt out of receiving promotional emails by following the unsubscribe instructions in the emails or by contacting us directly.</li>
                    <li><strong>Cookies:</strong> Most web browsers are set to accept cookies by default. You can set your browser to reject cookies or to alert you when cookies are being sent.</li>
                    <li><strong>Do Not Track:</strong> Some browsers have a "Do Not Track" feature that lets you tell websites you do not want to have your online activities tracked. These features are not yet uniform, so we do not currently respond to such signals.</li>
                </ul>
                
                <h2>Data Security</h2>
                <p>We have implemented appropriate technical and organizational security measures to protect the security of your personal information. However, please be aware that no security measures are perfect or impenetrable. We cannot guarantee the absolute security of your information.</p>
                
                <h2>Data Retention</h2>
                <p>We will retain your personal information only for as long as is necessary for the purposes set out in this privacy policy, unless a longer retention period is required or permitted by law.</p>
                
                <h2>Children's Privacy</h2>
                <p>Our website is not intended for individuals under the age of 16. We do not knowingly collect personal information from children under 16. If we learn we have collected or received personal information from a child under 16, we will delete that information.</p>
                
                <h2>International Data Transfers</h2>
                <p>We may transfer, store, and process your information in countries other than your own. When we do, we take steps to ensure your information receives adequate security protection.</p>
                
                <h2>Your Rights</h2>
                <p>Depending on your location, you may have certain rights regarding your personal information, such as:</p>
                <ul>
                    <li>The right to access your personal information</li>
                    <li>The right to correct inaccurate or incomplete information</li>
                    <li>The right to delete your personal information</li>
                    <li>The right to restrict processing of your personal information</li>
                    <li>The right to data portability</li>
                    <li>The right to object to processing of your personal information</li>
                    <li>The right to withdraw consent</li>
                </ul>
                
                <h2>Cookie Policy</h2>
                <p>We use cookies and similar tracking technologies to track the activity on our website and hold certain information. Cookies are files with a small amount of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                
                <h3>Types of Cookies We Use</h3>
                <ul>
                    <li><strong>Essential Cookies:</strong> Necessary for the website to function properly.</li>
                    <li><strong>Preference Cookies:</strong> Remember your preferences and settings.</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website.</li>
                    <li><strong>Marketing Cookies:</strong> Track visitors across websites to display relevant advertisements.</li>
                </ul>
                
                <h2>Changes to this Privacy Policy</h2>
                <p>We may update our privacy policy from time to time. We will notify you of any changes by posting the new privacy policy on this page and updating the "Last Updated" date at the top of this policy. You are advised to review this privacy policy periodically for any changes.</p>
                
                <h2>Contact Us</h2>
                <div class="contact-info-block">
                    <p>If you have any questions about this privacy policy or our data practices, please contact us at:</p>
                    <p><strong>Velvet Vogue Data Protection Team</strong><br>
                    123 Fashion Street<br>
                    New York, NY 10001<br>
                    Email: privacy@velvetvogue.com<br>
                    Phone: +1 234 567 8900</p>
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
                        <li><a href="privacy.php" class="active">Privacy Policy</a></li>
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