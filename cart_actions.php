<?php
// Include database connection
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers for JSON response
header('Content-Type: application/json');

// Get request data
$requestData = json_decode(file_get_contents('php://input'), true);

// Initialize cart in session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to calculate cart totals
function calculateCartTotals() {
    $subtotal = 0;
    $shipping = 0; // Free shipping for now
    $discount = 0; // No discount by default
    
    // Calculate subtotal
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += ($item['price'] * $item['quantity']);
    }
    
    // Apply discounts if any
    if (isset($_SESSION['coupon'])) {
        // Example discount calculation - 10% off
        if ($_SESSION['coupon']['type'] === 'percentage') {
            $discount = $subtotal * ($_SESSION['coupon']['value'] / 100);
        } elseif ($_SESSION['coupon']['type'] === 'fixed') {
            $discount = $_SESSION['coupon']['value'];
        }
    }
    
    // Calculate total
    $total = $subtotal - $discount + $shipping;
    
    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'discount' => $discount,
        'total' => $total,
        'cart_count' => count($_SESSION['cart'])
    ];
}

// Check what action was requested
if (isset($requestData['action'])) {
    $action = $requestData['action'];
    
    // Add item to cart
    if ($action === 'add') {
        $product_id = $requestData['product_id'];
        $quantity = isset($requestData['quantity']) ? (int)$requestData['quantity'] : 1;
        $options = isset($requestData['options']) ? $requestData['options'] : [];
        
        // Get product details from database
        $stmt = $conn->prepare("SELECT product_id, name, price, sale_price, image FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $product = $result->fetch_assoc();
            
            // Use sale price if available
            $price = ($product['sale_price'] && $product['sale_price'] < $product['price']) ? $product['sale_price'] : $product['price'];
            
            // Create a unique cart item ID that includes selected options
            $cart_item_id = $product_id;
            if (!empty($options)) {
                $option_string = '';
                foreach ($options as $key => $value) {
                    $option_string .= "_" . $key . "-" . $value;
                }
                $cart_item_id .= $option_string;
            }
            
            // Check if product already exists in cart
            if (isset($_SESSION['cart'][$cart_item_id])) {
                // If so, just update the quantity
                $_SESSION['cart'][$cart_item_id]['quantity'] += $quantity;
            } else {
                // Otherwise add as new item
                $_SESSION['cart'][$cart_item_id] = [
                    'product_id' => $product_id,
                    'name' => $product['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $product['image']
                ];
                
                // Add options if any
                foreach ($options as $key => $value) {
                    $_SESSION['cart'][$cart_item_id][$key] = $value;
                }
            }
            
            // Calculate cart totals
            $cartTotals = calculateCartTotals();
            
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart.',
                'cart_count' => count($_SESSION['cart']),
                'cart_item' => $_SESSION['cart'][$cart_item_id],
                'subtotal' => $cartTotals['subtotal'],
                'shipping' => $cartTotals['shipping'],
                'discount' => $cartTotals['discount'],
                'total' => $cartTotals['total']
            ]);
        } else {
            // Product not found
            echo json_encode([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }
        
        $stmt->close();
    }
    
    // Update cart item quantity
    elseif ($action === 'update') {
        $product_id = $requestData['product_id'];
        $quantity = (int)$requestData['quantity'];
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            
            // Calculate item subtotal
            $item_subtotal = $_SESSION['cart'][$product_id]['price'] * $quantity;
            
            // Calculate cart totals
            $cartTotals = calculateCartTotals();
            
            echo json_encode([
                'success' => true,
                'cart_count' => count($_SESSION['cart']),
                'item_subtotal' => $item_subtotal,
                'subtotal' => $cartTotals['subtotal'],
                'shipping' => $cartTotals['shipping'],
                'discount' => $cartTotals['discount'],
                'total' => $cartTotals['total']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found in cart.'
            ]);
        }
    }
    
    // Remove item from cart
    elseif ($action === 'remove') {
        $product_id = $requestData['product_id'];
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            
            // Calculate cart totals
            $cartTotals = calculateCartTotals();
            
            echo json_encode([
                'success' => true,
                'cart_count' => count($_SESSION['cart']),
                'subtotal' => $cartTotals['subtotal'],
                'shipping' => $cartTotals['shipping'],
                'discount' => $cartTotals['discount'],
                'total' => $cartTotals['total']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found in cart.'
            ]);
        }
    }
    
    // Apply coupon code
    elseif ($action === 'apply_coupon') {
        $coupon_code = $requestData['coupon_code'];
        
        // Look up coupon in database
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $coupon = $result->fetch_assoc();
            
            // Store coupon in session
            $_SESSION['coupon'] = [
                'code' => $coupon['code'],
                'type' => $coupon['discount_type'],
                'value' => $coupon['discount_value']
            ];
            
            // Calculate cart totals
            $cartTotals = calculateCartTotals();
            
            echo json_encode([
                'success' => true,
                'message' => 'Coupon applied successfully.',
                'subtotal' => $cartTotals['subtotal'],
                'shipping' => $cartTotals['shipping'],
                'discount' => $cartTotals['discount'],
                'total' => $cartTotals['total']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ]);
        }
        
        $stmt->close();
    }
    
    // Clear cart
    elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        unset($_SESSION['coupon']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared.',
            'cart_count' => 0,
            'subtotal' => 0,
            'shipping' => 0,
            'discount' => 0,
            'total' => 0
        ]);
    }
    
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No action specified.'
    ]);
}
?>