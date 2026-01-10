<?php
/**
 * Toggle Like API
 * POST /api/likes/toggle-like.php
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Require authentication
if (!isLoggedIn()) {
    jsonError('يجب تسجيل الدخول للإعجاب', 401);
}

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

try {
    $contentId = getPost('content_id');
    
    if (empty($contentId)) {
        jsonError('معرف المحتوى مطلوب');
    }
    
    $db = getDB();
    $userId = getUserId();
    
    // Verify content exists
    $stmt = $db->prepare("SELECT id FROM health_content WHERE id = ?");
    $stmt->execute([$contentId]);
    if (!$stmt->fetch()) {
        jsonError('المحتوى غير موجود', 404);
    }
    
    // Check if user already liked this content
    $stmt = $db->prepare("SELECT id FROM likes WHERE user_id = ? AND content_id = ?");
    $stmt->execute([$userId, $contentId]);
    $existingLike = $stmt->fetch();
    
    if ($existingLike) {
        // Unlike - remove the like
        $stmt = $db->prepare("DELETE FROM likes WHERE user_id = ? AND content_id = ?");
        $stmt->execute([$userId, $contentId]);
        $action = 'unliked';
    } else {
        // Like - add the like
        $stmt = $db->prepare("
            INSERT INTO likes (user_id, content_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$userId, $contentId]);
        $action = 'liked';
    }
    
    // Get updated like count
    $stmt = $db->prepare("SELECT COUNT(*) as like_count FROM likes WHERE content_id = ?");
    $stmt->execute([$contentId]);
    $result = $stmt->fetch();
    
    jsonSuccess([
        'action' => $action,
        'like_count' => $result['like_count'],
        'user_liked' => $action === 'liked'
    ], $action === 'liked' ? 'تم الإعجاب' : 'تم إلغاء الإعجاب');
    
} catch (PDOException $e) {
    error_log("Toggle like error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Toggle like error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
