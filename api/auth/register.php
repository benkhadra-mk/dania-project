<?php
/**
 * User Registration API
 * POST /api/auth/register.php
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
if (!checkRateLimit('register', 3, 600)) { // 3 attempts per 10 minutes
    jsonError('عدد كبير جداً من محاولات التسجيل. الرجاء المحاولة لاحقاً', 429);
}

try {
    // Get and validate input
    $username = getPost('username');
    $email = getPost('email');
    $password = getPost('password');
    $confirm_password = getPost('confirm_password');
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        jsonError('الرجاء ملء جميع الحقول المطلوبة');
    }
    
    // Validate username
    if (!validateUsername($username)) {
        jsonError('اسم المستخدم يجب أن يكون 3-50 حرف ويحتوي على أحرف وأرقام فقط');
    }
    
    // Validate email
    $email = sanitizeEmail($email);
    if (!$email) {
        jsonError('البريد الإلكتروني غير صالح');
    }
    
    // Validate password
    $passwordCheck = validatePassword($password);
    if (!$passwordCheck['valid']) {
        jsonError($passwordCheck['message']);
    }
    
    // Check password confirmation
    if ($password !== $confirm_password) {
        jsonError('كلمات المرور غير متطابقة');
    }
    
    // Get database connection
    $db = getDB();
    
    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        jsonError('اسم المستخدم موجود بالفعل');
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonError('البريد الإلكتروني مسجل بالفعل');
    }
    
    // Hash password
    $passwordHash = hashPassword($password);
    
    // Insert new user
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$username, $email, $passwordHash])) {
        $userId = $db->lastInsertId();
        
        // Create default user preferences
        $stmt = $db->prepare("
            INSERT INTO user_preferences (user_id, theme) 
            VALUES (?, 'light')
        ");
        $stmt->execute([$userId]);
        
        jsonSuccess([
            'user_id' => $userId,
            'username' => $username,
            'email' => $email
        ], 'تم إنشاء الحساب بنجاح');
    } else {
        jsonError('فشل إنشاء الحساب. الرجاء المحاولة مرة أخرى');
    }
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
