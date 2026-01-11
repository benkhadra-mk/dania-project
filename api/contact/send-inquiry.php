<?php
/**
 * Send Contact Inquiry API
 * POST /api/contact/send-inquiry.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../../utils/security.php';

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
    
    // In a real application, you would:
    // 1. Store in database
    // 2. Send email to admin
    // 3. Send confirmation email to user
    
    // For now, we'll just log it and return success
    $inquiry = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'date' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // Log to file (in production, store in database)
    $logFile = '../../logs/inquiries.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents(
        $logFile,
        json_encode($inquiry, JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND
    );
    
    jsonSuccess(
        ['inquiry_id' => uniqid()],
        'تم إرسال استفسارك بنجاح! سنتواصل معك قريباً عبر البريد الإلكتروني.'
    );
    
} catch (Exception $e) {
    error_log("Contact inquiry error: " . $e->getMessage());
    jsonError('حدث خطأ غير متوقع', 500);
}
?>
