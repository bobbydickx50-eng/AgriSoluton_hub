<?php
// order.php - Order Processing Handler

require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in (optional, as guest checkout is allowed)
$user_id = null;
$customer_name = '';
$customer_email = '';
$customer_phone = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $current_user = getCurrentUser();
    if ($current_user) {
        $customer_name = $current_user['name'];
        $customer_email = $current_user['email'];
        $customer_phone = $current_user['phone'];
    }
}

// Get POST data (override with session data if available)
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : $customer_name;
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : $customer_email;
$phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : $customer_phone;
$address = isset($_POST['address']) ? sanitizeInput($_POST['address']) : '';
$city = isset($_POST['city']) ? sanitizeInput($_POST['city']) : '';
$region = isset($_POST['region']) ? sanitizeInput($_POST['region']) : '';
$country = isset($_POST['country']) ? sanitizeInput($_POST['country']) : 'Tanzania';
$postal = isset($_POST['postal']) ? sanitizeInput($_POST['postal']) : '';
$payment = isset($_POST['payment']) ? sanitizeInput($_POST['payment']) : 'mpesa';
$notes = isset($_POST['notes']) ? sanitizeInput($_POST['notes']) : '';

// Get cart data (in real implementation, this would come from session or database)
$cart_json = isset($_POST['cart']) ? $_POST['cart'] : '[]';
$cart = json_decode($cart_json, true);

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email address';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

if (empty($address)) {
    $errors[] = 'Shipping address is required';
}

if (empty($city)) {
    $errors[] = 'City is required';
}

if (empty($region)) {
    $errors[] = 'Region is required';
}

if (empty($cart) || count($cart) === 0) {
    $errors[] = 'Cart is empty';
}

// Return errors if any
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Database connection
$conn = getDBConnection();

// Begin transaction
$conn->begin_transaction();

try {
    // Calculate order totals
    $subtotal = 0;
    $items = [];
    
    foreach ($cart as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $subtotal += $item_total;
        
        $items[] = [
            'product_type' => $item['name'],
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit'],
            'unit_price' => $item['price'],
            'total_price' => $item_total
        ];
    }
    
    // Calculate taxes and fees
    $tax_amount = $subtotal * 0.18; // 18% VAT
    $shipping_amount = 15000; // Fixed shipping cost
    $discount_amount = 0;
    $final_amount = $subtotal + $tax_amount + $shipping_amount - $discount_amount;
    
    // Create billing address (same as shipping for now)
    $billing_address = $address;
    
    // Insert order
    $order_sql = "INSERT INTO orders (
        user_id, customer_name, customer_email, customer_phone,
        shipping_address, billing_address, city, region, country, postal_code,
        total_amount, tax_amount, shipping_amount, discount_amount, final_amount,
        payment_method, payment_status, order_status, notes, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, NOW())";
    
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param(
        "isssssssssdddddss",
        $user_id, $name, $email, $phone,
        $address, $billing_address, $city, $region, $country, $postal,
        $subtotal, $tax_amount, $shipping_amount, $discount_amount, $final_amount,
        $payment, $notes
    );
    
    if (!$order_stmt->execute()) {
        throw new Exception('Failed to create order');
    }
    
    $order_id = $conn->insert_id;
    
    // Update order number (trigger will handle this, but we can also do it here)
    $order_number = 'AG-' . date('Y') . '-' . str_pad($order_id, 4, '0', STR_PAD_LEFT);
    $update_sql = "UPDATE orders SET order_number = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $order_number, $order_id);
    $update_stmt->execute();
    
    // Insert order items
    foreach ($items as $item) {
        $item_sql = "INSERT INTO order_items (
            order_id, product_type, product_name, quantity, unit,
            unit_price, total_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $item_stmt = $conn->prepare($item_sql);
        $item_stmt->bind_param(
            "issdsdd",
            $order_id,
            $item['product_type'],
            $item['product_name'],
            $item['quantity'],
            $item['unit'],
            $item['unit_price'],
            $item['total_price']
        );
        
        if (!$item_stmt->execute()) {
            throw new Exception('Failed to add order items');
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Send confirmation emails
    $user_subject = "Order Confirmation - $order_number";
    $user_message = "Dear $name,<br><br>";
    $user_message .= "Thank you for your order! We have received your order and will process it shortly.<br><br>";
    $user_message .= "<strong>Order Details:</strong><br>";
    $user_message .= "Order Number: $order_number<br>";
    $user_message .= "Order Date: " . date('F j, Y') . "<br>";
    $user_message .= "Payment Method: " . strtoupper($payment) . "<br>";
    $user_message .= "Shipping Address: $address, $city, $region, $country<br><br>";
    
    $user_message .= "<strong>Order Items:</strong><br>";
    foreach ($items as $item) {
        $user_message .= "- {$item['product_name']}: {$item['quantity']} {$item['unit']} @ TZS " . 
                        number_format($item['unit_price']) . " = TZS " . 
                        number_format($item['total_price']) . "<br>";
    }
    
    $user_message .= "<br><strong>Order Summary:</strong><br>";
    $user_message .= "Subtotal: TZS " . number_format($subtotal) . "<br>";
    $user_message .= "Tax (18%): TZS " . number_format($tax_amount) . "<br>";
    $user_message .= "Shipping: TZS " . number_format($shipping_amount) . "<br>";
    $user_message .= "Discount: TZS " . number_format($discount_amount) . "<br>";
    $user_message .= "<strong>Total: TZS " . number_format($final_amount) . "</strong><br><br>";
    
    $user_message .= "We will notify you once your order is shipped.<br>";
    $user_message .= "You can track your order using order number: $order_number<br><br>";
    $user_message .= "Best regards,<br>AgriSolutions Hub Team";
    
    // Send admin notification
    $admin_subject = "New Order Received - $order_number";
    $admin_message = "A new order has been placed:<br><br>";
    $admin_message .= "<strong>Order Details:</strong><br>";
    $admin_message .= "Order Number: $order_number<br>";
    $admin_message .= "Customer: $name<br>";
    $admin_message .= "Email: $email<br>";
    $admin_message .= "Phone: $phone<br>";
    $admin_message .= "Total Amount: TZS " . number_format($final_amount) . "<br>";
    $admin_message .= "Payment Method: " . strtoupper($payment) . "<br><br>";
    $admin_message .= "Please process this order in the admin panel.";
    
    // Log activity if user is logged in
    if ($user_id) {
        logActivity($user_id, 'order_placed', "Placed order #$order_number");
    }
    
    // In production, uncomment these lines:
    // sendEmail($email, $user_subject, $user_message);
    // sendEmail(ADMIN_EMAIL, $admin_subject, $admin_message);
    
    // Clear cart session
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order' => [
            'id' => $order_id,
            'number' => $order_number,
            'total' => $final_amount,
            'items' => count($items)
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => 'Order failed: ' . $e->getMessage()
    ]);
}

$conn->close();
?>