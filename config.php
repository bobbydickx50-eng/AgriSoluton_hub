<?php
// AgriSolutions Hub - Configuration File

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'agrisolutions_db');

// Site Configuration
define('SITE_NAME', 'AgriSolutions Hub');
define('SITE_URL', 'http://localhost/agrisolutions');
define('SITE_EMAIL', 'info@agrisolutionshub.com');
define('ADMIN_EMAIL', 'admin@agrisolutionshub.com');

// Project Information
define('PROJECT_STUDENT', 'David Bablo Mushii');
define('PROJECT_REG_NO', 'DIT/E/2024/0021');
define('PROJECT_INSTITUTION', 'Sokoine University of Agriculture');
define('PROJECT_DEPARTMENT', 'Informatics and Information Technology');
define('PROJECT_COURSE', 'Second Year Diploma in Information Technology');

// Location Information
define('LOCATION_NAME', 'SUA Mazimbu Campus');
define('LOCATION_CITY', 'Morogoro');
define('LOCATION_COUNTRY', 'Tanzania');
define('LOCATION_PHONE', '+255 748 550 225');
define('LOCATION_EMAIL', 'informatics@sua.ac.tz');

// Timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
session_start();

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number (Tanzania format)
function validatePhone($phone) {
    return preg_match('/^\+255[0-9]{9}$/', $phone) || preg_match('/^0[0-9]{9}$/', $phone);
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Get current date time
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

// Format date for display
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Format date time for display
function formatDateTime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

// Redirect to URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDBConnection();
    $user_id = getCurrentUserId();
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Logout user
function logout() {
    session_destroy();
    redirect('index.html');
}

// Send email
function sendEmail($to, $subject, $message, $headers = '') {
    if (empty($headers)) {
        $headers = "From: " . SITE_EMAIL . "\r\n" .
                   "Reply-To: " . SITE_EMAIL . "\r\n" .
                   "X-Mailer: PHP/" . phpversion() .
                   "MIME-Version: 1.0\r\n" .
                   "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $message, $headers);
}

// Get IP address
function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    $conn = getDBConnection();
    $ip_address = getIPAddress();
    $timestamp = getCurrentDateTime();
    
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $action, $details, $ip_address, $timestamp);
    
    return $stmt->execute();
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Validate file upload
function validateFileUpload($file, $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf']) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error';
        return $errors;
    }
    
    $file_extension = getFileExtension($file['name']);
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_extensions);
    }
    
    if ($file['size'] > 5000000) { // 5MB limit
        $errors[] = 'File size too large. Maximum size: 5MB';
    }
    
    return $errors;
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get pagination data
function getPaginationData($page, $per_page = 10) {
    $page = max(1, intval($page));
    $offset = ($page - 1) * $per_page;
    
    return [
        'page' => $page,
        'per_page' => $per_page,
        'offset' => $offset
    ];
}

// Calculate total pages
function calculateTotalPages($total_items, $per_page = 10) {
    return ceil($total_items / $per_page);
}

// Generate pagination links
function generatePaginationLinks($current_page, $total_pages, $base_url) {
    $links = [];
    
    // Previous link
    if ($current_page > 1) {
        $links[] = [
            'url' => $base_url . '?page=' . ($current_page - 1),
            'label' => '&laquo; Previous',
            'active' => false
        ];
    }
    
    // Page links
    for ($i = 1; $i <= $total_pages; $i++) {
        $links[] = [
            'url' => $base_url . '?page=' . $i,
            'label' => $i,
            'active' => ($i == $current_page)
        ];
    }
    
    // Next link
    if ($current_page < $total_pages) {
        $links[] = [
            'url' => $base_url . '?page=' . ($current_page + 1),
            'label' => 'Next &raquo;',
            'active' => false
        ];
    }
    
    return $links;
}

// Get weather data for location
function getWeatherData($location = 'morogoro') {
    $locations = [
        'morogoro' => [
            'temperature' => rand(25, 32),
            'condition' => 'Partly Cloudy',
            'humidity' => rand(60, 80),
            'wind_speed' => rand(5, 15)
        ],
        'dar' => [
            'temperature' => rand(28, 35),
            'condition' => 'Sunny',
            'humidity' => rand(70, 90),
            'wind_speed' => rand(10, 20)
        ],
        'arusha' => [
            'temperature' => rand(18, 25),
            'condition' => 'Cloudy',
            'humidity' => rand(50, 70),
            'wind_speed' => rand(5, 12)
        ]
    ];
    
    return $locations[$location] ?? $locations['morogoro'];
}

// Get market prices
function getMarketPrices() {
    $conn = getDBConnection();
    
    $sql = "SELECT * FROM market_prices ORDER BY updated_at DESC LIMIT 10";
    $result = $conn->query($sql);
    
    $prices = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
    }
    
    return $prices;
}

// Get latest resources
function getLatestResources($limit = 6) {
    $conn = getDBConnection();
    
    $sql = "SELECT * FROM resources ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $resources = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
    }
    
    return $resources;
}

// Get statistics
function getStatistics() {
    $conn = getDBConnection();
    
    $stats = [
        'total_users' => 0,
        'total_orders' => 0,
        'total_products' => 0,
        'total_resources' => 0
    ];
    
    // Get total users
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['total_users'] = $row['count'];
    }
    
    // Get total orders
    $sql = "SELECT COUNT(*) as count FROM orders";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['count'];
    }
    
    // Get total products (from market prices)
    $sql = "SELECT COUNT(DISTINCT commodity) as count FROM market_prices";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['total_products'] = $row['count'];
    }
    
    // Get total resources
    $sql = "SELECT COUNT(*) as count FROM resources";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['total_resources'] = $row['count'];
    }
    
    return $stats;
}

// Get global business opportunities
function getGlobalOpportunities() {
    return [
        [
            'region' => 'European Union',
            'products' => ['Organic Coffee', 'Tea', 'Spices', 'Cashew Nuts'],
            'requirements' => 'Organic certification, EU standards'
        ],
        [
            'region' => 'Asian Markets',
            'products' => ['Sesame Seeds', 'Cashew Nuts', 'Beans', 'Seaweed'],
            'requirements' => 'Quality certification, Proper packaging'
        ],
        [
            'region' => 'American Markets',
            'products' => ['Specialty Coffee', 'Cocoa', 'Vanilla', 'Shea Butter'],
            'requirements' => 'FDA approval, Fair trade certification'
        ]
    ];
}

// Include this in your pages:
// require_once 'config.php';
?>