<?php
/**
 * User Login API
 * POST /api/auth/login.php
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

// Check rate limiting
if (!checkRateLimit('login', 5, 300)) { // 5 attempts per 5 minutes
    jsonError('عدد كبير جداً من محاولات تسجيل الدخول. الرجاء المحاولة لاحقاً', 429);
}

try {
    // Get input
    $login = getPost('login'); // Can be username or email
    $password = getPost('password');
    
    // Validation
    if (empty($login) || empty($password)) {
        jsonError('الرجاء إدخال اسم المستخدم/البريد الإلكتروني وكلمة المرور');
    }
    
    // Get database connection
    $db = getDB();
    
    // Check if login is email or username
    $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
    
    if ($isEmail) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    }
    
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    // Check if user exists and password is correct
    if (!$user || !verifyPassword($password, $user['password'])) {
        jsonError('اسم المستخدم/البريد الإلكتروني أو كلمة المرور غير صحيحة');
    }
    
    // Get user preferences
    $stmt = $db->prepare("SELECT theme FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $preferences = $stmt->fetch();
    
    // Set session data
    setUserSession([
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'is_admin' => $user['is_admin']
    ]);
    
    // Store theme preference
    if ($preferences) {
        $_SESSION['theme'] = $preferences['theme'];
    }
    
    jsonSuccess([
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'is_admin' => (bool)$user['is_admin'],
            'theme' => $preferences['theme'] ?? 'light'
        ]
    ], 'تم تسجيل الدخول بنجاح');
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
