<?php
// Start session
session_start();

// Include database connection
require_once 'config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Redirect to dashboard if already logged in
if($isLoggedIn) {
    // Check role and redirect appropriately
    if($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: customer_dashboard.php");
    }
    exit;
}

// Get any auth messages from session
$authMessage = isset($_SESSION['auth_message']) ? $_SESSION['auth_message'] : '';
$authMessageType = isset($_SESSION['auth_message_type']) ? $_SESSION['auth_message_type'] : '';

// Clear messages after retrieving them
if(isset($_SESSION['auth_message'])) {
    unset($_SESSION['auth_message']);
    unset($_SESSION['auth_message_type']);
}

// Initialize variables to prevent errors
$user = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'username' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal_code' => '',
    'country' => ''
];
$orders = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for the auth tabs */
        .auth-tabs {
            display: flex;
            background-color: var(--light-color);
            margin-bottom: 0;
        }
        
        .auth-tab {
            flex: 1;
            padding: var(--spacing-md);
            text-align: center;
            background: none;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
            border-bottom: 2px solid transparent;
            font-family: var(--body-font);
            font-size: 1rem;
        }
        
        .auth-tab.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .auth-form {
            display: none;
            padding: var(--spacing-lg);
        }
        
        .auth-form.active {
            display: block;
        }
        
        /* Message styles */
        .auth-message {
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
            display: none;
        }
        
        .auth-message.success {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border: 1px solid #4caf50;
            display: block;
        }
        
        .auth-message.error {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border: 1px solid #f44336;
            display: block;
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
                    <a href="account.php" class="active"><i class="fas fa-user"></i> My Account</a>
                    <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> Cart (<span id="cart-count">0</span>)</a>
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
                <li>My Account</li>
            </ul>
        </div>
    </div>

    <!-- Account Section -->
    <section class="account-section">
        <div class="container">
            <div class="section-title">
                <h1>My Account</h1>
            </div>
            
            <!-- Login and Register Section (shown when user is not logged in) -->
            <div class="auth-container" id="auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" id="login-tab">Login</button>
                    <button class="auth-tab" id="register-tab">Register</button>
                </div>
                
                <div class="auth-content">
                    <div class="auth-form active" id="login-form-container">
                        <form id="login-form" action="login.php" method="post">
                            <div class="form-group">
                                <label for="login-email">Email Address</label>
                                <input type="email" id="login-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="login-password">Password</label>
                                <input type="password" id="login-password" name="password" required>
                            </div>
                            <div class="form-group remember-me">
                                <label>
                                    <input type="checkbox" id="remember-me" name="remember"> Remember Me
                                </label>
                                <a href="#" class="forgot-password">Forgot Password?</a>
                            </div>
                            <button type="submit" class="btn-primary btn-block">Login</button>
                            
                            <?php if(!empty($authMessage)): ?>
                                <div class="auth-message <?php echo $authMessageType == 'success' ? 'success' : 'error'; ?>">
                                    <?php echo $authMessage; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <div class="auth-form" id="register-form-container">
                        <form id="register-form" action="register.php" method="post">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" id="first-name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" id="last-name" name="last_name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="register-email">Email Address</label>
                                <input type="email" id="register-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="register-password">Password</label>
                                <input type="password" id="register-password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm-password">Confirm Password</label>
                                <input type="password" id="confirm-password" name="confirm_password" required>
                            </div>
                            <div class="form-group terms">
                                <label>
                                    <input type="checkbox" id="terms" name="terms" required> I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                            </div>
                            <button type="submit" class="btn-primary btn-block">Register</button>
                        </form>
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

    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
    <script>
        // JavaScript for tab switching
        document.addEventListener('DOMContentLoaded', function() {
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');
            const loginForm = document.getElementById('login-form-container');
            const registerForm = document.getElementById('register-form-container');
            
            loginTab.addEventListener('click', function() {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
            });
            
            registerTab.addEventListener('click', function() {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
            });
        });
    </script>
</body>
</html>