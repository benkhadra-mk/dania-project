<?php
/**
 * Database Connection Test
 * This file tests if the database connection works properly
 */

// Include database configuration
require_once 'config/database.php';

try {
    $db = getDB();
    
    // Try a simple query
    $stmt = $db->query("SELECT DATABASE() as db_name, VERSION() as db_version");
    $result = $stmt->fetch();
    
    echo "<!DOCTYPE html>";
    echo "<html lang='ar' dir='rtl'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>اختبار الاتصال بقاعدة البيانات</title>";
    echo "<style>";
    echo "body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: center; padding: 50px; background: linear-gradient(135deg, #2E8B57, #4A90E2); color: white; }";
    echo ".success { background: white; color: #2E8B57; padding: 30px; border-radius: 15px; max-width: 600px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }";
    echo "h1 { font-size: 2.5rem; margin-bottom: 20px; }";
    echo ".icon { font-size: 4rem; margin-bottom: 20px; }";
    echo "p { font-size: 1.2rem; margin: 10px 0; }";
    echo ".info { background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 20px 0; color: #343a40; }";
    echo "a { display: inline-block; background: #2E8B57; color: white; padding: 12px 30px; border-radius: 50px; text-decoration: none; margin-top: 20px; }";
    echo "a:hover { background: #3CB371; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    echo "<div class='success'>";
    echo "<div class='icon'>✅</div>";
    echo "<h1>نجح الاتصال بقاعدة البيانات!</h1>";
    echo "<p>تم الاتصال بقاعدة البيانات بنجاح</p>";
    echo "<div class='info'>";
    echo "<p><strong>اسم قاعدة البيانات:</strong> " . htmlspecialchars($result['db_name']) . "</p>";
    echo "<p><strong>إصدار MySQL:</strong> " . htmlspecialchars($result['db_version']) . "</p>";
    echo "</div>";
    echo "<a href='index.php'>الذهاب إلى الصفحة الرئيسية</a>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>";
    echo "<html lang='ar' dir='rtl'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>خطأ في الاتصال</title>";
    echo "<style>";
    echo "body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: center; padding: 50px; background: linear-gradient(135deg, #FF6B6B, #4A90E2); color: white; }";
    echo ".error { background: white; color: #FF6B6B; padding: 30px; border-radius: 15px; max-width: 600px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }";
    echo "h1 { font-size: 2.5rem; margin-bottom: 20px; }";
    echo ".icon { font-size: 4rem; margin-bottom: 20px; }";
    echo "p { font-size: 1.2rem; margin: 10px 0; color: #343a40; }";
    echo ".error-msg { background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 20px 0; color: #FF6B6B; font-family: monospace; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    echo "<div class='error'>";
    echo "<div class='icon'>❌</div>";
    echo "<h1>فشل الاتصال بقاعدة البيانات</h1>";
    echo "<p>حدث خطأ أثناء محاولة الاتصال بقاعدة البيانات</p>";
    echo "<div class='error-msg'>" . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p style='margin-top: 20px;'><strong>الرجاء التأكد من:</strong></p>";
    echo "<ul style='text-align: right; display: inline-block; margin: 10px auto;'>";
    echo "<li>تشغيل خدمة MySQL في XAMPP</li>";
    echo "<li>إنشاء قاعدة البيانات 'wellcare'</li>";
    echo "<li>استيراد ملف schema.sql</li>";
    echo "</ul>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
}
?>
