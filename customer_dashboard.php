<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Not logged in, redirect to account page
    $_SESSION['auth_message'] = "Please login to access your dashboard";
    $_SESSION['auth_message_type'] = "error";
    header("Location: account.php");
    exit;
}

// Check if user is admin, redirect to admin panel
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user orders
$orders = [];
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
if ($orderStmt) {
    $orderStmt->bind_param("i", $user_id);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    
    while ($order = $orderResult->fetch_assoc()) {
        $orders[] = $order;
    }
    
    $orderStmt->close();
}

// Get cart count from session
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Velvet Vogue</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-section {
            padding: 3rem 0;
        }
        .dashboard-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .dashboard-header {
            padding: 2rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .dashboard-header h1 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        .dashboard-content {
            padding: 2rem;
        }
        .dashboard-welcome {
            margin-bottom: 2rem;
        }
        .dashboard-welcome h2 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .profile-details {
            margin-bottom: 3rem;
        }
        .profile-details h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-item label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }
        .info-item span {
            color: var(--text-color);
        }
        .order-history h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th, .order-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .order-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-processing {
            background-color: #b8daff;
            color: #004085;
        }
        .status-shipped {
            background-color: #c3e6cb;
            color: #155724;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f5c6cb;
            color: #721c24;
        }
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }
        .no-orders {
            padding: 2rem;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-top: 1rem;
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
                    <a href="customer_dashboard.php" class="active"><i class="fas fa-user"></i> My Account</a>
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
                <li>My Dashboard</li>
            </ul>
        </div>
    </div>

    <!-- Dashboard Section -->
    <section class="dashboard-section">
        <div class="container">
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h1>My Dashboard</h1>
                    <p>Manage your account information and track your orders</p>
                </div>
                
                <div class="dashboard-content">
                    <div class="dashboard-welcome">
                        <h2>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>
                        <p>Here's an overview of your account activity and information.</p>
                    </div>
                    
                    <div class="profile-details">
                        <h3>Account Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>First Name</label>
                                <span><?php echo htmlspecialchars($user['first_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Last Name</label>
                                <span><?php echo htmlspecialchars($user['last_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Email</label>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Username</label>
                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            <?php if(!empty($user['phone'])): ?>
                            <div class="info-item">
                                <label>Phone</label>
                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($user['address'])): ?>
                            <div class="info-item">
                                <label>Address</label>
                                <span><?php echo htmlspecialchars($user['address']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($user['city'])): ?>
                            <div class="info-item">
                                <label>City</label>
                                <span><?php echo htmlspecialchars($user['city']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($user['postal_code'])): ?>
                            <div class="info-item">
                                <label>Postal Code</label>
                                <span><?php echo htmlspecialchars($user['postal_code']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($user['country'])): ?>
                            <div class="info-item">
                                <label>Country</label>
                                <span><?php echo htmlspecialchars($user['country']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="order-history">
                        <h3>Order History</h3>
                        
                        <?php if(count($orders) > 0): ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo isset($order['item_count']) ? $order['item_count'] : '—'; ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="btn-outline">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="no-orders">
                            <p>You haven't placed any orders yet.</p>
                            <a href="categories.php" class="btn-primary">Start Shopping</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="edit_profile.php" class="btn-primary">Edit Profile</a>
                        <a href="logout.php" class="btn-outline">Logout</a>
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
                        <li><a href="customer_dashboard.php">My Account</a></li>
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