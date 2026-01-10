<?php
/**
 * Get Health Content API
 * GET /api/content/get-content.php?type=disease|diet&slug=slug-name
 * GET /api/content/get-content.php (get all)
 */

header('Content-Type: application/json; charset=utf-8');

// Include required files
require_once '../../config/database.php';
require_once '../../utils/security.php';

try {
    $db = getDB();
    
    $type = getQuery('type'); // disease or diet
    $slug = getQuery('slug'); // specific content slug
    
    // If slug is provided, get specific content
    if ($slug) {
        $stmt = $db->prepare("
            SELECT id, type, title, slug, icon, short_description, content_sections
            FROM health_content 
            WHERE slug = ?
        ");
        $stmt->execute([$slug]);
        $content = $stmt->fetch();
        
        if (!$content) {
            jsonError('المحتوى غير موجود', 404);
        }
        
        // Parse JSON content
        $content['content_sections'] = json_decode($content['content_sections'], true);
        
        // Get like count
        $stmt = $db->prepare("SELECT COUNT(*) as like_count FROM likes WHERE content_id = ?");
        $stmt->execute([$content['id']]);
        $likeData = $stmt->fetch();
        $content['like_count'] = $likeData['like_count'];
        
        // Check if current user liked this content
        $content['user_liked'] = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $db->prepare("SELECT id FROM likes WHERE content_id = ? AND user_id = ?");
            $stmt->execute([$content['id'], $_SESSION['user_id']]);
            $content['user_liked'] = (bool)$stmt->fetch();
        }
        
        jsonSuccess($content);
        
    } else {
        // Get all content or filtered by type
        $sql = "SELECT id, type, title, slug, icon, short_description FROM health_content";
        $params = [];
        
        if ($type && in_array($type, ['disease', 'diet'])) {
            $sql .= " WHERE type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY type, title";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $contents = $stmt->fetchAll();
        
        jsonSuccess([
            'count' => count($contents),
            'items' => $contents
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Get content error: " . $e->getMessage());
    jsonError('حدث خطأ في قاعدة البيانات', 500);
} catch (Exception $e) {
    error_log("Get content error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
