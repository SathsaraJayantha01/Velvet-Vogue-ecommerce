<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    // Not logged in or not admin, redirect to login page
    header("Location: account.html");
    exit;
}

// Check if the action is to update status
if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $inquiry_id = intval($_POST['inquiry_id']);
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'in_progress', 'resolved'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['admin_message'] = "Invalid status value";
        $_SESSION['admin_message_type'] = "error";
        header("Location: admin_inquiries.php");
        exit;
    }
    
    // Update the status
    $stmt = $conn->prepare("UPDATE contact_inquiries SET status = ? WHERE inquiry_id = ?");
    $stmt->bind_param("si", $status, $inquiry_id);
    
    if ($stmt->execute()) {
        $_SESSION['admin_message'] = "Inquiry status updated successfully";
        $_SESSION['admin_message_type'] = "success";
    } else {
        $_SESSION['admin_message'] = "Error updating inquiry status: " . $stmt->error;
        $_SESSION['admin_message_type'] = "error";
    }
    
    $stmt->close();
    header("Location: admin_inquiries.php");
    exit;
}

// Function to get status badge
function getStatusBadge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge badge-warning">Pending</span>';
        case 'in_progress':
            return '<span class="badge badge-info">In Progress</span>';
        case 'resolved':
            return '<span class="badge badge-success">Resolved</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

// Get inquiries with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total count of inquiries
$count_result = $conn->query("SELECT COUNT(*) as total FROM contact_inquiries");
$count_row = $count_result->fetch_assoc();
$total_inquiries = $count_row['total'];
$total_pages = ceil($total_inquiries / $per_page);

// Get inquiries for current page
$result = $conn->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC LIMIT $offset, $per_page");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Inquiries - Velvet Vogue Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        
        .inquiry-message {
            max-height: 100px;
            overflow-y: auto;
        }
        
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            margin: 0 4px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .pagination a:hover {
            background-color: #f0f0f0;
        }
        
        .pagination .active {
            background-color: #333;
            color: white;
        }
        
        .pagination .disabled {
            color: #aaa;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <div class="admin-header-content">
                <div class="admin-logo">
                    <h1>Velvet Vogue Admin</h1>
                </div>
                <div class="admin-nav">
                    <a href="admin.html">Dashboard</a>
                    <a href="admin_products.php">Products</a>
                    <a href="admin_orders.php">Orders</a>
                    <a href="admin_users.php">Users</a>
                    <a href="admin_inquiries.php" class="active">Inquiries</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <div class="admin-content">
                <div class="admin-section-header">
                    <h2>Manage Contact Inquiries</h2>
                </div>
                
                <!-- Display messages if any -->
                <?php if (isset($_SESSION['admin_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['admin_message_type']; ?>">
                        <?php echo $_SESSION['admin_message']; ?>
                    </div>
                    <?php 
                    // Clear message after displaying
                    unset($_SESSION['admin_message']);
                    unset($_SESSION['admin_message_type']);
                    ?>
                <?php endif; ?>
                
                <!-- Inquiries List -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Inquiries</h3>
                        <div class="admin-card-actions">
                            <span>Total: <?php echo $total_inquiries; ?> inquiries</span>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['inquiry_id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                            <td>
                                                <div class="inquiry-message">
                                                    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                                </div>
                                            </td>
                                            <td><?php echo getStatusBadge($row['status']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <form method="post" action="admin_inquiries.php" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="inquiry_id" value="<?php echo $row['inquiry_id']; ?>">
                                                    <select name="status" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="in_progress" <?php echo $row['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                        <option value="resolved" <?php echo $row['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                                    <?php else: ?>
                                        <span class="disabled">&laquo; Previous</span>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <?php if ($i == $page): ?>
                                            <span class="active"><?php echo $i; ?></span>
                                        <?php else: ?>
                                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                                    <?php else: ?>
                                        <span class="disabled">Next &raquo;</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>No inquiries found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="container">
            <p>&copy; 2025 Velvet Vogue Admin Panel. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/admin.js"></script>
</body>
</html> 