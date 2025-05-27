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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/contact.css">
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
                    <li><a href="contact.php" class="active">Contact Us</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </div>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="section-title">
                <h1>Contact Us</h1>
                <p>We'd love to hear from you! Feel free to reach out with any questions, feedback, or inquiries.</p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Our Store</h3>
                            <p>123 Fashion Street</p>
                            <p>New York, NY 10001</p>
                            <p>United States</p>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Phone</h3>
                            <p>Customer Service: +1 234 567 8900</p>
                            <p>Support: +1 234 567 8901</p>
                            <p>Sales: +1 234 567 8902</p>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p>Customer Service: info@velvetvogue.com</p>
                            <p>Support: support@velvetvogue.com</p>
                            <p>Business: business@velvetvogue.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Store Hours</h3>
                            <p>Monday - Friday: 9:00 AM - 8:00 PM</p>
                            <p>Saturday: 10:00 AM - 7:00 PM</p>
                            <p>Sunday: 11:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="social-media-contact">
                        <h3>Connect With Us</h3>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-pinterest"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-container">
                    <div class="contact-form-wrapper">
                        <h2>Send Us a Message</h2>
                        <form id="contact-form" method="post" action="contact_process.php" novalidate>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone">
                                </div>
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" id="subject" name="subject" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="6" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-primary">Send Message</button>
                            </div>
                            <div id="form-message" class="form-message"></div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="location-map">
                <h2>Our Location</h2>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.9663095293367!2d-73.99387684847553!3d40.74076637932865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9aeb1c6b5%3A0x35b1cfbc89a6097f!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1650000000000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="faqs-section">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to commonly asked questions about our products, shipping, and policies.</p>
            </div>
            
            <div class="faqs-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What payment methods do you accept?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>We accept various payment methods including credit/debit cards (Visa, MasterCard, American Express, Discover), PayPal, and Apple Pay. All payments are securely processed to ensure your information is protected.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What is your shipping policy?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>We offer free standard shipping on all orders above $50 within the United States. Standard shipping typically takes 3-5 business days. Express shipping is available at an additional cost and typically takes 1-2 business days. International shipping is available to select countries.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What is your return policy?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>We offer a 30-day return policy for unworn items in their original packaging with tags attached. Returns are free for customers in the United States. For more details, please visit our Returns page or contact our customer service team.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How can I track my order?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>Once your order has been shipped, you will receive a shipping confirmation email with a tracking number. You can use this tracking number on our website under "Order Tracking" in your account dashboard, or directly on the carrier's website.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do you offer size exchanges?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we offer size exchanges for unworn items. To request an exchange, please follow the same process as a return but select "Exchange" instead of "Return" and specify the new size you want. Exchanges are subject to availability.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I care for my Velvet Vogue garments?</h3>
                        <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>Each garment comes with specific care instructions on the label. Generally, we recommend washing in cold water, hanging to dry, and avoiding bleach. For delicate items, dry cleaning is recommended. Always refer to the care label for the best results.</p>
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
    <script>
        // FAQ toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                
                question.addEventListener('click', function() {
                    // Toggle active class on the FAQ item
                    item.classList.toggle('active');
                    
                    // Toggle visibility of the answer
                    if (item.classList.contains('active')) {
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                    } else {
                        answer.style.maxHeight = '0';
                    }
                });
            });
            
            // Form validation
            const contactForm = document.getElementById('contact-form');
            const formMessage = document.getElementById('form-message');
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    let isValid = true;
                    const name = document.getElementById('name');
                    const email = document.getElementById('email');
                    const subject = document.getElementById('subject');
                    const message = document.getElementById('message');
                    
                    // Simple validation
                    if (!name.value.trim()) {
                        isValid = false;
                        name.classList.add('error');
                    } else {
                        name.classList.remove('error');
                    }
                    
                    if (!email.value.trim() || !email.value.includes('@')) {
                        isValid = false;
                        email.classList.add('error');
                    } else {
                        email.classList.remove('error');
                    }
                    
                    if (!subject.value.trim()) {
                        isValid = false;
                        subject.classList.add('error');
                    } else {
                        subject.classList.remove('error');
                    }
                    
                    if (!message.value.trim()) {
                        isValid = false;
                        message.classList.add('error');
                    } else {
                        message.classList.remove('error');
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        formMessage.textContent = 'Please fill in all required fields correctly.';
                        formMessage.classList.add('error');
                    }
                });
            }
        });
    </script>
</body>
</html>