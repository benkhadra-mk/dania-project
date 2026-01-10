<?php
/**
 * Get Comments API
 * GET /api/comments/get-comments.php?content_id=123
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../utils/security.php';

try {
    $contentId = getQuery('content_id');
    
    if (empty($contentId)) {
        jsonError('معرف المحتوى مطلوب');
    }
    
    $db = getDB();
    
    // Get comments with user information
    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.comment_text,
            c.created_at,
            u.id as user_id,
            u.username
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.content_id = ?
        ORDER BY c.created_at DESC
    ");
    
    $stmt->execute([$contentId]);
    $comments = $stmt->fetchAll();
    
    jsonSuccess([
        'count' => count($comments),
        'comments' => $comments
    ]);
    
} catch (PDOException $e) {
    error_log("Get comments error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Get comments error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
