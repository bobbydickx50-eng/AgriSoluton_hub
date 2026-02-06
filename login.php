<?php
// login.php - User Login Handler

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
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember = isset($_POST['remember']) ? true : false;

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit;
}

if (!validateEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Database connection
$conn = getDBConnection();

// Check if user exists
$sql = "SELECT id, name, email, password, user_type, status FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

// Check account status
if ($user['status'] !== 'active') {
    echo json_encode(['success' => false, 'message' => 'Account is not active']);
    exit;
}

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
    exit;
}

// Start session
session_start();

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

// Set remember me cookie
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
    
    // Store token in database
    $sql = "UPDATE users SET remember_token = ?, token_expiry = FROM_UNIXTIME(?) WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $token, $expiry, $user['id']);
    $stmt->execute();
    
    // Set cookie
    setcookie('remember_token', $token, $expiry, '/', '', false, true);
}

// Update last login
$sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();

// Log activity
logActivity($user['id'], 'login', 'User logged in successfully');

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'type' => $user['user_type']
    ]
]);

$conn->close();
?>