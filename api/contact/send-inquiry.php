<?php
/**
 * Send Contact Inquiry API
 * POST /api/contact/send-inquiry.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../../utils/security.php';
require_once '../../config/database.php';

// Only allow POST requests
if (!isPostRequest()) {
    jsonError('طريقة الطلب غير صحيحة', 405);
}

try {
    // Get input
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $message = trim($data['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        jsonError('الرجاء ملء جميع الحقول المطلوبة');
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('البريد الإلكتروني غير صحيح');
    }
    
    // Validate name length
    if (strlen($name) < 2 || strlen($name) > 100) {
        jsonError('الاسم يجب أن يكون بين 2 و 100 حرف');
    }
    
    // Validate subject length
    if (strlen($subject) < 3 || strlen($subject) > 200) {
        jsonError('الموضوع يجب أن يكون بين 3 و 200 حرف');
    }
    
    // Validate message length
    if (strlen($message) < 10 || strlen($message) > 1000) {
        jsonError('الرسالة يجب أن تكون بين 10 و 1000 حرف');
    }
    
    // Save to database
    $db = getDB();
    
    $stmt = $db->prepare("
        INSERT INTO contact_inquiries (name, email, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([$name, $email, $subject, $message]);
    
    $inquiryId = $db->lastInsertId();
    
    jsonSuccess(
        ['inquiry_id' => $inquiryId],
        'تم إرسال استفسارك بنجاح! سنتواصل معك قريباً عبر البريد الإلكتروني.'
    );
    
} catch (PDOException $e) {
    error_log("Database error in contact inquiry: " . $e->getMessage());
    jsonError('حدث خطأ أثناء حفظ الاستفسار', 500);
} catch (Exception $e) {
    error_log("Contact inquiry error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
