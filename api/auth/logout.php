<?php
/**
 * User Logout API
 * POST /api/auth/logout.php
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

try {
    // Destroy session
    destroyUserSession();
    
    jsonSuccess(null, 'تم تسجيل الخروج بنجاح');
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    jsonError('حدث خطأ أثناء تسجيل الخروج', 500);
}
?>
