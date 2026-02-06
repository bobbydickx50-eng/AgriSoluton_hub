<?php
// register.php - User Registration Handler

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
$country = isset($_POST['country']) ? sanitizeInput($_POST['country']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$terms = isset($_POST['terms']) ? true : false;

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
} elseif (!validatePhone($phone)) {
    $errors[] = 'Invalid phone number format';
}

if (empty($country)) {
    $errors[] = 'Country is required';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

if (!$terms) {
    $errors[] = 'You must agree to the terms and conditions';
}

// Return errors if any
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Database connection
$conn = getDBConnection();

// Check if user already exists
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Determine user type based on email domain (simple logic)
$user_type = 'farmer';
if (strpos($email, '.edu') !== false || strpos($email, '.ac.') !== false) {
    $user_type = 'student';
} elseif (strpos($email, '.co.') !== false || strpos($email, '.com') !== false) {
    $user_type = 'business';
}

// Insert new user
$sql = "INSERT INTO users (name, email, phone, country, password, user_type, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $email, $phone, $country, $hashed_password, $user_type);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    
    // Start session
    session_start();
    
    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_type'] = $user_type;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    // Log activity
    logActivity($user_id, 'registration', 'New user registered');
    
    // Send welcome email (simulated)
    $subject = "Welcome to AgriSolutions Hub";
    $message = "Dear $name,<br><br>";
    $message .= "Thank you for registering with AgriSolutions Hub!<br>";
    $message .= "Your account has been successfully created.<br><br>";
    $message .= "You can now access all features of our platform.<br><br>";
    $message .= "Best regards,<br>AgriSolutions Hub Team";
    
    // In production, uncomment this line:
    // sendEmail($email, $subject, $message);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'type' => $user_type
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}

$conn->close();
?>