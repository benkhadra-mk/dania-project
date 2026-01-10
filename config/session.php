<?php
/**
 * Session Configuration for WellCare Project
 * 
 * Configures secure session handling
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    
    // Session timeout (30 minutes)
    ini_set('session.gc_maxlifetime', 1800);
    
    session_start();
}

// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Session timeout check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Last request was more than 30 minutes ago
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 * @return string|null
 */
function getUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Set user session data
 * @param array $user User data
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['is_admin'] = isset($user['is_admin']) ? (bool)$user['is_admin'] : false;
}

/**
 * Check if current user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Require admin access - redirect if not admin
 * @param string $redirect_to Where to redirect if not admin
 */
function requireAdmin($redirect_to = '/dania-project/index.php') {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: ' . $redirect_to);
        exit();
    }
}

/**
 * Destroy user session
 */
function destroyUserSession() {
    session_unset();
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require authentication - redirect if not logged in
 * @param string $redirect_to Where to redirect if not logged in
 */
function requireAuth($redirect_to = '/dania-project/login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect_to);
        exit();
    }
}
?>
