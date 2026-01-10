<?php
/**
 * Check Authentication Status API
 * GET /api/auth/check-auth.php
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

try {
    if (isLoggedIn()) {
        $db = getDB();
        
        // Get user preferences
        $stmt = $db->prepare("SELECT theme FROM user_preferences WHERE user_id = ?");
        $stmt->execute([getUserId()]);
        $preferences = $stmt->fetch();
        
        jsonSuccess([
            'logged_in' => true,
            'user' => [
                'id' => getUserId(),
                'username' => getUsername(),
                'email' => $_SESSION['email'] ?? '',
                'theme' => $preferences['theme'] ?? 'light'
            ]
        ]);
    } else {
        jsonSuccess([
            'logged_in' => false
        ]);
    }
    
} catch (Exception $e) {
    error_log("Check auth error: " . $e->getMessage());
    jsonError('حدث خطأ في التحقق من حالة الدخول', 500);
}
?>
