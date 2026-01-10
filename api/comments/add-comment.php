<?php
/**
 * Add Comment API
 * POST /api/comments/add-comment.php
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Require authentication
if (!isLoggedIn()) {
    jsonError('يجب تسجيل الدخول لإضافة تعليق', 401);
}

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

// Check rate limiting
if (!checkRateLimit('comment', 10, 300)) { // 10 comments per 5 minutes
    jsonError('عدد كبير جداً من التعليقات. الرجاء الانتظار قليلاً', 429);
}

try {
    $contentId = getPost('content_id');
    $commentText = getPost('comment_text');
    
    // Validation
    if (empty($contentId) || empty($commentText)) {
        jsonError('الرجاء إدخال التعليق');
    }
    
    // Validate comment length
    if (strlen($commentText) < 3) {
        jsonError('التعليق قصير جداً');
    }
    
    if (strlen($commentText) > 1000) {
        jsonError('التعليق طويل جداً (الحد الأقصى 1000 حرف)');
    }
    
    $db = getDB();
    
    // Verify content exists
    $stmt = $db->prepare("SELECT id FROM health_content WHERE id = ?");
    $stmt->execute([$contentId]);
    if (!$stmt->fetch()) {
        jsonError('المحتوى غير موجود', 404);
    }
    
    // Insert comment
    $stmt = $db->prepare("
        INSERT INTO comments (user_id, content_id, comment_text, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([getUserId(), $contentId, $commentText])) {
        $commentId = $db->lastInsertId();
        
        // Get the newly created comment with user info
        $stmt = $db->prepare("
            SELECT 
                c.id,
                c.comment_text,
                c.created_at,
                u.username
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch();
        
        jsonSuccess($comment, 'تم إضافة التعليق بنجاح');
    } else {
        jsonError('فشل إضافة التعليق');
    }
    
} catch (PDOException $e) {
    error_log("Add comment error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Add comment error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
