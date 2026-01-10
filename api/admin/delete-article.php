<?php
/**
 * Delete Article API (Admin Only)
 * POST /api/admin/delete-article.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    jsonError('غير مصرح لك بالوصول', 403);
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if (empty($id)) {
        jsonError('معرف المقال مطلوب');
    }
    
    $db = getDB();
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Delete associated comments
        $stmt = $db->prepare("DELETE FROM comments WHERE content_id = ?");
        $stmt->execute([$id]);
        
        // Delete associated likes
        $stmt = $db->prepare("DELETE FROM likes WHERE content_id = ?");
        $stmt->execute([$id]);
        
        // Delete the article
        $stmt = $db->prepare("DELETE FROM health_content WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('المقال غير موجود');
        }
        
        $db->commit();
        jsonSuccess(null, 'تم حذف المقال بنجاح');
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Delete article error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    jsonError($e->getMessage());
}
?>
