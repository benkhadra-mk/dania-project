<?php
/**
 * Save/Update Article API (Admin Only)
 * POST /api/admin/save-article.php
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
    
    // Validation
    if (empty($data['title']) || empty($data['slug']) || empty($data['type']) || empty($data['icon'])) {
        jsonError('الرجاء ملء جميع الحقول المطلوبة');
    }
    
    // Validate type
    if (!in_array($data['type'], ['disease', 'diet'])) {
        jsonError('نوع المقال غير صحيح');
    }
    
    $db = getDB();
    
    // Prepare content sections as JSON
    $contentSections = json_encode($data['content_sections'], JSON_UNESCAPED_UNICODE);
    
    if (!empty($data['id'])) {
        // Update existing article
        $stmt = $db->prepare("
            UPDATE health_content 
            SET title = ?, 
                slug = ?, 
                type = ?, 
                icon = ?, 
                short_description = ?, 
                content_sections = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['type'],
            $data['icon'],
            $data['short_description'],
            $contentSections,
            $data['id']
        ]);
        
        if ($stmt->rowCount() === 0) {
            jsonError('المقال غير موجود أو لم يتم إجراء أي تغييرات');
        }
        
        jsonSuccess(['id' => $data['id']], 'تم تحديث المقال بنجاح');
        
    } else {
        // Create new article
        $stmt = $db->prepare("
            INSERT INTO health_content 
            (title, slug, type, icon, short_description, content_sections) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['type'],
            $data['icon'],
            $data['short_description'],
            $contentSections
        ]);
        
        jsonSuccess(['id' => $db->lastInsertId()], 'تم إضافة المقال بنجاح');
    }
    
} catch (PDOException $e) {
    // Check for duplicate slug
    if ($e->getCode() == 23000) {
        jsonError('الرمز (Slug) مستخدم بالفعل. الرجاء اختيار رمز آخر');
    }
    error_log("Save article error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Save article error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
