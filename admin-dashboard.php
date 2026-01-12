<?php
/**
 * Admin Dashboard - WellCare
 * Article, Comment, and Contact Inquiry Management
 */

require_once 'config/session.php';
require_once 'config/database.php';
require_once 'utils/security.php';

// Require admin access
requireAdmin();

// Get database
$db = getDB();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as count FROM health_content");
$articleCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
$userCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM comments");
$commentCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM likes");
$likeCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'pending'");
$inquiryCount = $stmt->fetch()['count'];

// Get all articles
$stmt = $db->query("SELECT * FROM health_content ORDER BY created_at DESC");
$articles = $stmt->fetchAll();

// Get recent comments
$stmt = $db->query("
    SELECT c.*, u.username, h.title as content_title 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    JOIN health_content h ON c.content_id = h.id 
    ORDER BY c.created_at DESC 
    LIMIT 10
");
$recentComments = $stmt->fetchAll();

// Get contact inquiries
$stmt = $db->query("
    SELECT * FROM contact_inquiries 
    ORDER BY 
        CASE status 
            WHEN 'pending' THEN 1 
            WHEN 'read' THEN 2 
            WHEN 'resolved' THEN 3 
        END,
        created_at DESC
");
$inquiries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - WellCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        
        :root {
            --primary: #2E8B57;
            --primary-light: #3CB371;
            --secondary: #4A90E2;
            --accent: #FF6B6B;
            --light: #F8F9FA;
            --dark: #343A40;
            --gray: #6C757D;
            --white: #FFFFFF;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo h1 {
            font-size: 1.8rem;
        }
        
        .logo span {
            color: #FFD700;
        }
        
        .admin-badge {
            background: #FFD700;
            color: var(--dark);
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .btn-logout {
            background: var(--accent);
        }
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-title {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .stat-icon i {
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: var(--light);
            font-weight: 600;
            color: var(--dark);
        }
        
        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-disease {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-diet {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-read {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-resolved {
            background: #d4edda;
            color: #155724;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: #4A90E2;
            color: white;
        }
        
        .btn-delete {
            background: var(--accent);
            color: white;
        }
        
        .comment-text, .inquiry-message {
            max-width: 300px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h1>Well<span>Care</span></h1>
                <span class="admin-badge"><i class="fas fa-shield-alt"></i> لوحة التحكم</span>
            </div>
            
            <div class="header-actions">
                <a href="index.php" class="btn">
                    <i class="fas fa-home"></i> الصفحة الرئيسية
                </a>
                <button class="btn btn-logout" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </button>
            </div>
        </div>
    </header>
    
    <main>
        <h1 class="dashboard-title">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
        </h1>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="stat-number"><?php echo $articleCount; ?></div>
                <div class="stat-label">المقالات</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $userCount; ?></div>
                <div class="stat-label">المستخدمين</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-number"><?php echo $commentCount; ?></div>
                <div class="stat-label">التعليقات</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-number"><?php echo $likeCount; ?></div>
                <div class="stat-label">الإعجابات</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div class="stat-number"><?php echo $inquiryCount; ?></div>
                <div class="stat-label">الاستفسارات المعلقة</div>
            </div>
        </div>
        
        <!-- Contact Inquiries Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-envelope-open-text"></i> استفسارات العملاء</h2>
            </div>
            
            <?php if (empty($inquiries)): ?>
                <p style="text-align: center; color: var(--gray); padding: 2rem;">لا توجد استفسارات حالياً</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الموضوع</th>
                        <th>الرسالة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>"><?php echo htmlspecialchars($inquiry['email']); ?></a></td>
                        <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                        <td class="inquiry-message" title="<?php echo htmlspecialchars($inquiry['message']); ?>">
                            <?php echo htmlspecialchars($inquiry['message']); ?>
                        </td>
                        <td>
                            <select class="badge badge-<?php echo $inquiry['status']; ?>" 
                                    onchange="updateInquiryStatus(<?php echo $inquiry['id']; ?>, this.value)"
                                    style="border: none; cursor: pointer;">
                                <option value="pending" <?php echo $inquiry['status'] === 'pending' ? 'selected' : ''; ?>>معلق</option>
                                <option value="read" <?php echo $inquiry['status'] === 'read' ? 'selected' : ''; ?>>مقروء</option>
                                <option value="resolved" <?php echo $inquiry['status'] === 'resolved' ? 'selected' : ''; ?>>تم الحل</option>
                            </select>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($inquiry['created_at'])); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>?subject=Re: <?php echo urlencode($inquiry['subject']); ?>" 
                               class="btn-sm btn-edit">
                                <i class="fas fa-reply"></i> رد
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        
        <!-- Articles Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-file-alt"></i> إدارة المقالات</h2>
                <a href="admin-article-edit.php" class="btn-primary">
                    <i class="fas fa-plus"></i> إضافة مقال جديد
                </a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>الرمز</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $article['type']; ?>">
                                <?php echo $article['type'] === 'disease' ? 'مرض' : 'نظام غذائي'; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($article['slug']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin-article-edit.php?id=<?php echo $article['id']; ?>" class="btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <button class="btn-sm btn-delete" onclick="deleteArticle(<?php echo $article['id']; ?>, '<?php echo htmlspecialchars(addslashes($article['title'])); ?>')">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Comments Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-comments"></i> التعليقات الأخيرة</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>المستخدم</th>
                        <th>المقال</th>
                        <th>التعليق</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentComments as $comment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['content_title']); ?></td>
                        <td class="comment-text" title="<?php echo htmlspecialchars($comment['comment_text']); ?>">
                            <?php echo htmlspecialchars($comment['comment_text']); ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></td>
                        <td>
                            <button class="btn-sm btn-delete" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <script src="assets/js/app.js"></script>
    <script>
        async function logout() {
            const result = await logoutUser();
            if (result.success) {
                window.location.href = 'index.php';
            }
        }
        
        async function deleteArticle(id, title) {
            if (!confirm(`هل أنت متأكد من حذف المقال "${title}"؟\nسيتم حذف جميع التعليقات والإعجابات المرتبطة به.`)) {
                return;
            }
            
            try {
                const response = await fetch('/dania-project/api/admin/delete-article.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id})
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('حدث خطأ أثناء الحذف');
            }
        }
        
        async function deleteComment(id) {
            if (!confirm('هل أنت متأكد من حذف هذا التعليق؟')) {
                return;
            }
            
            try {
                const response = await fetch('/dania-project/api/admin/delete-comment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id})
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('حدث خطأ أثناء الحذف');
            }
        }
        
        async function updateInquiryStatus(id, status) {
            try {
                const response = await fetch('/dania-project/api/admin/update-inquiry-status.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id, status})
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('حدث خطأ أثناء تحديث الحالة');
            }
        }
    </script>
</body>
</html>
