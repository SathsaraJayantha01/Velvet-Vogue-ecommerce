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

// FAQ categories and questions
$faqCategories = [
    [
        'title' => 'Shopping & Orders',
        'icon' => 'fa-shopping-bag',
        'faqs' => [
            [
                'question' => 'How do I place an order?',
                'answer' => 'Placing an order on Velvet Vogue is easy! Simply browse our products, select the items you want, choose your size and color options, and click "Add to Cart". When you\'re ready to checkout, click on the cart icon in the top right corner, review your items, and proceed to checkout. Follow the instructions to enter your shipping and payment details, and confirm your order.'
            ],
            [
                'question' => 'Can I modify or cancel my order after it\'s placed?',
                'answer' => 'You can modify or cancel your order within 1 hour of placing it. Please contact our customer service team immediately at support@velvetvogue.com or call us at +1 234 567 8900. Once your order enters the processing stage, we may not be able to make changes or cancel it.'
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'Once your order ships, you\'ll receive a shipping confirmation email with a tracking number. You can use this tracking number to monitor your package\'s status on our website under "Order Tracking" in your account dashboard, or directly on the carrier\'s website.'
            ],
            [
                'question' => 'Do you offer gift wrapping?',
                'answer' => 'Yes, we offer gift wrapping for a small additional fee of $5 per item. During checkout, you\'ll have the option to add gift wrapping and include a personalized message for your recipient.'
            ]
        ]
    ],
    [
        'title' => 'Shipping & Delivery',
        'icon' => 'fa-truck',
        'faqs' => [
            [
                'question' => 'What shipping methods do you offer?',
                'answer' => 'We offer several shipping methods:<br>
                <ul>
                <li><strong>Standard Shipping:</strong> 3-5 business days</li>
                <li><strong>Express Shipping:</strong> 1-2 business days</li>
                <li><strong>International Shipping:</strong> 7-14 business days</li>
                </ul>
                Shipping times may vary depending on your location and customs processing for international orders.'
            ],
            [
                'question' => 'Do you offer free shipping?',
                'answer' => 'Yes! We offer free standard shipping on all orders over $50 within the United States. International orders and express shipping options have additional charges that will be calculated at checkout.'
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes, we ship to most countries worldwide. International shipping costs and delivery times vary by location. Please note that international customers may be responsible for customs duties and import taxes imposed by their country\'s government. These fees are not included in our shipping charges.'
            ],
            [
                'question' => 'What happens if my package is lost or damaged?',
                'answer' => 'If your package is lost or damaged during transit, please contact our customer service within 48 hours of the delivery date. We\'ll work with the shipping carrier to locate your package or process a replacement or refund as appropriate. Please save all packaging materials and damaged items for potential inspection by the carrier.'
            ]
        ]
    ],
    [
        'title' => 'Returns & Refunds',
        'icon' => 'fa-exchange-alt',
        'faqs' => [
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for all unused and unworn items in their original condition with tags attached. Returns are free for customers in the United States. International returns are accepted, but the customer is responsible for return shipping costs. Once we receive your return, we\'ll process the refund within 5-7 business days.'
            ],
            [
                'question' => 'How do I start a return?',
                'answer' => 'To initiate a return, log into your account, go to "My Orders," select the order containing the item(s) you wish to return, and click on "Return Items." Follow the instructions to complete and submit your return request. You\'ll receive a confirmation email with a return shipping label (for domestic returns) and instructions on how to package and send your items back.'
            ],
            [
                'question' => 'Can I exchange an item instead of returning it?',
                'answer' => 'Yes, we do offer exchanges for different sizes or colors of the same item. To request an exchange, follow the same process as a return but select "Exchange" instead of "Return" and specify the new size or color you want. Please note that exchanges are subject to item availability.'
            ],
            [
                'question' => 'How long does it take to process my refund?',
                'answer' => 'Once we receive your return, it typically takes 2-3 business days to inspect and process it. After approval, the refund will be issued to your original payment method, which may take an additional 3-5 business days to appear on your statement, depending on your financial institution.'
            ]
        ]
    ],
    [
        'title' => 'Products & Sizing',
        'icon' => 'fa-tshirt',
        'faqs' => [
            [
                'question' => 'How do I find my correct size?',
                'answer' => 'We provide detailed size guides for all our products. On each product page, click on the "Size Guide" link near the size selection options. This will open a comprehensive chart with measurements in both inches and centimeters. If you\'re between sizes, we generally recommend sizing up for a more comfortable fit. If you\'re still unsure, contact our customer service for personalized advice.'
            ],
            [
                'question' => 'Are your products true to size?',
                'answer' => 'Our products are designed to fit true to standard US sizing. However, fit can vary slightly by style and fabric. We recommend checking the specific size guide on each product page and reading customer reviews which often contain helpful information about fit.'
            ],
            [
                'question' => 'What materials do you use in your clothing?',
                'answer' => 'We use a variety of high-quality materials in our products. The specific fabric composition is listed on each product page under "Product Details." We prioritize both comfort and durability, using premium materials such as organic cotton, sustainable blends, genuine leather, and eco-friendly alternatives.'
            ],
            [
                'question' => 'How should I care for my Velvet Vogue items?',
                'answer' => 'Care instructions are provided on the product label and on each product page under "Care Instructions." Generally, we recommend washing our garments in cold water, using a gentle cycle, and hanging or laying flat to dry to preserve the quality and extend the life of your items. For accessories, we provide specific care guidance based on the materials used.'
            ]
        ]
    ],
    [
        'title' => 'Payment & Security',
        'icon' => 'fa-credit-card',
        'faqs' => [
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept a variety of payment methods including:<br>
                <ul>
                <li>Credit/debit cards (Visa, MasterCard, American Express, Discover)</li>
                <li>PayPal</li>
                <li>Apple Pay</li>
                <li>Google Pay</li>
                <li>Shop Pay</li>
                <li>Afterpay (for orders between $35-$1,000)</li>
                </ul>
                All transactions are securely processed and encrypted to protect your information.'
            ],
            [
                'question' => 'Is my payment information secure?',
                'answer' => 'Absolutely. We use industry-standard SSL encryption to protect your personal and payment information. Our payment processing systems comply with PCI DSS (Payment Card Industry Data Security Standard) to ensure the highest level of security. We never store your complete credit card information on our servers.'
            ],
            [
                'question' => 'Do you charge sales tax?',
                'answer' => 'Yes, we collect sales tax in states where we have a physical presence or where required by law. The applicable tax rate is determined by the shipping address and will be calculated and displayed during checkout before you complete your purchase.'
            ],
            [
                'question' => 'When will my credit card be charged?',
                'answer' => 'Your credit card will be authorized when you place your order, but it won\'t be charged until your order ships. If you\'re using PayPal or other payment services, you may be charged immediately at the time of purchase.'
            ]
        ]
    ],
    [
        'title' => 'Account & Privacy',
        'icon' => 'fa-user-shield',
        'faqs' => [
            [
                'question' => 'Do I need to create an account to shop?',
                'answer' => 'No, you can check out as a guest without creating an account. However, creating an account offers several benefits, including the ability to track orders, save your shipping information, maintain a wishlist, and receive personalized recommendations.'
            ],
            [
                'question' => 'How do you protect my personal information?',
                'answer' => 'We take data privacy very seriously. Our detailed Privacy Policy outlines how we collect, use, and protect your personal information. We use the latest security technologies and follow strict privacy practices. We never sell your personal information to third parties, and we only share necessary information with shipping carriers and payment processors to fulfill your orders.'
            ],
            [
                'question' => 'Can I update my account information?',
                'answer' => 'Yes, you can update your account information at any time. Simply log into your account, go to "Account Settings," and you can modify your personal information, change your password, update your shipping addresses, and manage your communication preferences.'
            ],
            [
                'question' => 'How can I subscribe or unsubscribe from your newsletter?',
                'answer' => 'You can subscribe to our newsletter by entering your email address in the subscription box at the bottom of our homepage or during account creation. To unsubscribe, click the "Unsubscribe" link at the bottom of any newsletter email, or log into your account, go to "Communication Preferences," and update your email subscription settings.'
            ]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .faq-section {
            padding: 3rem 0;
        }
        
        .faq-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .faq-category-tab {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 170px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .faq-category-tab:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .faq-category-tab.active {
            border-color: var(--primary-color);
            background-color: rgba(106, 27, 154, 0.05);
            color: var(--primary-color);
        }
        
        .faq-category-tab i {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        
        .faq-category-tab h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .faq-category-content {
            display: none;
        }
        
        .faq-category-content.active {
            display: block;
        }
        
        .faq-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            padding: 1.25rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid transparent;
            transition: all 0.3s ease;
        }
        
        .faq-question h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .faq-toggle {
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question {
            border-bottom-color: #eee;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 1.25rem;
            height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            height: auto;
            padding: 1.25rem;
        }
        
        .faq-answer p {
            margin: 0;
            line-height: 1.6;
            color: var(--text-color);
        }
        
        .faq-search {
            position: relative;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        .faq-search input {
            width: 100%;
            padding: 1rem 1.25rem;
            padding-right: 3rem;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .faq-search input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.1);
            outline: none;
        }
        
        .faq-search button {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .no-results {
            text-align: center;
            padding: 2rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .still-have-questions {
            text-align: center;
            background-color: rgba(106, 27, 154, 0.05);
            padding: 3rem 2rem;
            border-radius: 8px;
            margin-top: 3rem;
        }
        
        .still-have-questions h2 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .still-have-questions p {
            margin-bottom: 1.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
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
                <li>Frequently Asked Questions</li>
            </ul>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-title">
                <h1>Frequently Asked Questions</h1>
                <p>Find answers to the most common questions about our products and services</p>
            </div>
            
            <div class="faq-search">
                <input type="text" id="faq-search-input" placeholder="Search for questions...">
                <button id="faq-search-btn"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="faq-categories">
                <?php foreach($faqCategories as $index => $category): ?>
                    <div class="faq-category-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-category="<?php echo $index; ?>">
                        <i class="fas <?php echo $category['icon']; ?>"></i>
                        <h3><?php echo $category['title']; ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="faq-content">
                <?php foreach($faqCategories as $index => $category): ?>
                    <div class="faq-category-content <?php echo $index === 0 ? 'active' : ''; ?>" id="category-<?php echo $index; ?>">
                        <?php foreach($category['faqs'] as $faqIndex => $faq): ?>
                            <div class="faq-item" data-question="<?php echo htmlspecialchars(strtolower($faq['question'])); ?>">
                                <div class="faq-question">
                                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                                    <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                                </div>
                                <div class="faq-answer">
                                    <p><?php echo $faq['answer']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="no-results" style="display: none;">
                    <h3>No matching questions found</h3>
                    <p>Try a different search term or browse the categories above.</p>
                </div>
            </div>
            
            <div class="still-have-questions">
                <h2>Still Have Questions?</h2>
                <p>If you couldn't find the answer to your question, our customer support team is here to help you.</p>
                <a href="contact.php" class="btn-primary">Contact Us</a>
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
                        <li><a href="faq.php" class="active">FAQ</a></li>
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
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ Category tabs
            const categoryTabs = document.querySelectorAll('.faq-category-tab');
            const categoryContents = document.querySelectorAll('.faq-category-content');
            
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const categoryIndex = this.getAttribute('data-category');
                    
                    // Remove active class from all tabs and contents
                    categoryTabs.forEach(tab => tab.classList.remove('active'));
                    categoryContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to selected tab and content
                    this.classList.add('active');
                    document.getElementById('category-' + categoryIndex).classList.add('active');
                });
            });
            
            // FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', function() {
                    // Toggle active class on item
                    item.classList.toggle('active');
                });
            });
            
            // FAQ Search
            const searchInput = document.getElementById('faq-search-input');
            const searchBtn = document.getElementById('faq-search-btn');
            const noResults = document.querySelector('.no-results');
            
            function searchFAQs() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let resultsFound = false;
                
                // Hide all FAQ items initially
                faqItems.forEach(item => {
                    item.style.display = 'none';
                });
                
                if (searchTerm === '') {
                    // If search is empty, show all items in the active category
                    const activeCategory = document.querySelector('.faq-category-content.active');
                    if (activeCategory) {
                        activeCategory.querySelectorAll('.faq-item').forEach(item => {
                            item.style.display = 'block';
                        });
                        resultsFound = true;
                    }
                } else {
                    // Show all category contents
                    categoryContents.forEach(content => {
                        content.classList.add('active');
                    });
                    
                    // Reset active tabs
                    categoryTabs.forEach(tab => {
                        tab.classList.remove('active');
                    });
                    
                    // Search through all FAQ items
                    faqItems.forEach(item => {
                        const question = item.getAttribute('data-question');
                        
                        if (question.includes(searchTerm)) {
                            item.style.display = 'block';
                            resultsFound = true;
                            
                            // Expand the item
                            item.classList.add('active');
                        }
                    });
                }
                
                // Show/hide no results message
                noResults.style.display = resultsFound ? 'none' : 'block';
                
                // If no search term, restore tabs and categories
                if (searchTerm === '') {
                    categoryTabs[0].classList.add('active');
                    categoryContents.forEach((content, index) => {
                        if (index !== 0) {
                            content.classList.remove('active');
                        }
                    });
                }
            }
            
            searchBtn.addEventListener('click', searchFAQs);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchFAQs();
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>