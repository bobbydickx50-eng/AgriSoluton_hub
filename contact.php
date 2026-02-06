<?php
// contact.php - Contact Form Handler

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

// Get POST data
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
$subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
$newsletter = isset($_POST['newsletter']) ? true : false;

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

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
} elseif (strlen($message) < 10) {
    $errors[] = 'Message is too short';
}

// Return errors if any
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Database connection
$conn = getDBConnection();

// Determine category based on subject
$category = 'general';
$subject_lower = strtolower($subject);

if (strpos($subject_lower, 'market') !== false) {
    $category = 'market';
} elseif (strpos($subject_lower, 'technical') !== false || strpos($subject_lower, 'support') !== false) {
    $category = 'technical';
} elseif (strpos($subject_lower, 'export') !== false) {
    $category = 'export';
} elseif (strpos($subject_lower, 'order') !== false) {
    $category = 'order';
}

// Insert contact message
$sql = "INSERT INTO contact_messages (name, email, phone, subject, message, category, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $email, $phone, $subject, $message, $category);

if ($stmt->execute()) {
    $message_id = $conn->insert_id;
    
    // Handle newsletter subscription
    if ($newsletter) {
        // Check if already subscribed
        $check_sql = "SELECT id FROM newsletter_subscriptions WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            // Insert new subscription
            $sub_sql = "INSERT INTO newsletter_subscriptions (email, name, subscribed_at) 
                       VALUES (?, ?, NOW())";
            $sub_stmt = $conn->prepare($sub_sql);
            $sub_stmt->bind_param("ss", $email, $name);
            $sub_stmt->execute();
        }
    }
    
    // Send confirmation email to user
    $user_subject = "Thank you for contacting AgriSolutions Hub";
    $user_message = "Dear $name,<br><br>";
    $user_message .= "Thank you for contacting AgriSolutions Hub. We have received your message and our team will respond within 24 hours.<br><br>";
    $user_message .= "<strong>Message Details:</strong><br>";
    $user_message .= "Subject: $subject<br>";
    $user_message .= "Message: $message<br><br>";
    $user_message .= "If you have any urgent questions, please call us at +255 748 550 225.<br><br>";
    $user_message .= "Best regards,<br>AgriSolutions Hub Team";
    
    // Send notification email to admin
    $admin_subject = "New Contact Message: $subject";
    $admin_message = "A new contact message has been received:<br><br>";
    $admin_message .= "<strong>From:</strong> $name<br>";
    $admin_message .= "<strong>Email:</strong> $email<br>";
    $admin_message .= "<strong>Phone:</strong> $phone<br>";
    $admin_message .= "<strong>Subject:</strong> $subject<br>";
    $admin_message .= "<strong>Category:</strong> $category<br>";
    $admin_message .= "<strong>Message:</strong><br>$message<br><br>";
    $admin_message .= "Message ID: $message_id<br>";
    $admin_message .= "Received: " . date('F j, Y g:i A');
    
    // Log activity if user is logged in
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'contact_form', "Submitted contact form: $subject");
    }
    
    // In production, uncomment these lines:
    // sendEmail($email, $user_subject, $user_message);
    // sendEmail(ADMIN_EMAIL, $admin_subject, $admin_message);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully. We will respond within 24 hours.',
        'message_id' => $message_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}

$conn->close();
?>