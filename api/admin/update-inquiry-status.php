<?php
/**
 * Update Inquiry Status API
 * POST /api/admin/update-inquiry-status.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../utils/security.php';

// Require admin access
if (!isAdmin()) {
    jsonError('غير مصرح لك بالوصول', 403);
}

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['id'] ?? 0);
    $status = trim($data['status'] ?? '');
    
    if ($id <= 0) {
        jsonError('معرف الاستفسار غير صحيح');
    }
    
    if (!in_array($status, ['pending', 'read', 'resolved'])) {
        jsonError('حالة غير صحيحة');
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE contact_inquiries SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    if ($stmt->rowCount() === 0) {
        jsonError('الاستفسار غير موجود');
    }
    
    jsonSuccess([], 'تم تحديث حالة الاستفسار بنجاح');
    
} catch (Exception $e) {
    error_log("Update inquiry status error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
