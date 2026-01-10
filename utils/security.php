<?php
/**
 * Security Utilities for WellCare Project
 * 
 * Contains functions for input sanitization, validation, and security
 */

/**
 * Sanitize string input
 * @param string $input
 * @return string
 */
function sanitizeString($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Sanitize email
 * @param string $email
 * @return string|false
 */
function sanitizeEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

/**
 * Validate username
 * @param string $username
 * @return bool
 */
function validateUsername($username) {
    // Username must be 3-50 characters, alphanumeric and underscores only
    return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
}

/**
 * Validate password strength
 * @param string $password
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePassword($password) {
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'];
    }
    
    if (strlen($password) > 100) {
        return ['valid' => false, 'message' => 'كلمة المرور طويلة جداً'];
    }
    
    // You can add more password strength requirements here
    // Example: require uppercase, lowercase, numbers, special characters
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Hash password using bcrypt
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password against hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 * @param int $length
 * @return string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Prevent SQL injection by using prepared statements
 * This is a reminder function - always use PDO prepared statements
 */
function usePreparedStatements() {
    // Always use prepared statements like:
    // $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    // $stmt->execute([$email]);
    return true;
}

/**
 * Sanitize array of inputs
 * @param array $inputs
 * @return array
 */
function sanitizeArray($inputs) {
    $sanitized = [];
    foreach ($inputs as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = sanitizeArray($value);
        } else {
            $sanitized[$key] = sanitizeString($value);
        }
    }
    return $sanitized;
}

/**
 * Check if request is POST
 * @return bool
 */
function isPostRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * @return bool
 */
function isGetRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST data securely
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getPost($key, $default = null) {
    return isset($_POST[$key]) ? sanitizeString($_POST[$key]) : $default;
}

/**
 * Get GET data securely
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getQuery($key, $default = null) {
    return isset($_GET[$key]) ? sanitizeString($_GET[$key]) : $default;
}

/**
 * Send JSON response
 * @param array $data
 * @param int $status_code
 */
function jsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Send success JSON response
 * @param mixed $data
 * @param string $message
 */
function jsonSuccess($data = null, $message = 'نجحت العملية') {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], 200);
}

/**
 * Send error JSON response
 * @param string $message
 * @param int $status_code
 */
function jsonError($message = 'حدث خطأ', $status_code = 400) {
    jsonResponse([
        'success' => false,
        'message' => $message
    ], $status_code);
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Get client IP address
 * @return string
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }
}

/**
 * Rate limiting check (simple implementation)
 * @param string $action
 * @param int $max_attempts
 * @param int $time_window (in seconds)
 * @return bool
 */
function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $key = $action . '_' . getClientIP();
    $now = time();
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    $data = $_SESSION['rate_limit'][$key];
    
    // Reset if time window passed
    if ($now - $data['start'] > $time_window) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['count'] >= $max_attempts) {
        return false;
    }
    
    // Increment counter
    $_SESSION['rate_limit'][$key]['count']++;
    return true;
}
?>
