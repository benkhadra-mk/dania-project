<?php
/**
 * Delete Comment API (Admin Only)
 * POST /api/admin/delete-comment.php
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
        jsonError('معرف التعليق مطلوب');
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        jsonError('التعليق غير موجود');
    }
    
    jsonSuccess(null, 'تم حذف التعليق بنجاح');
    
} catch (PDOException $e) {
    error_log("Delete comment error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
