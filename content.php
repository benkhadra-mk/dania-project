<?php
/**
 * Dynamic Content Page - WellCare
 * Displays diseases and diets with comments and likes from database
 */

require_once 'config/session.php';
require_once 'config/database.php';
require_once 'utils/security.php';

// Get content slug from URL
$slug = getQuery('slug', '');

if (empty($slug)) {
    header('Location: health-journey.html');
    exit();
}

// Get database
$db = getDB();

// Fetch content from database
$stmt = $db->prepare("SELECT * FROM health_content WHERE slug = ?");
$stmt->execute([$slug]);
$content = $stmt->fetch();

if (!$content) {
    header('Location: health-journey.html');
    exit();
}

// Parse JSON content
$sections = json_decode($content['content_sections'], true);

// Get like count
$stmt = $db->prepare("SELECT COUNT(*) as count FROM likes WHERE content_id = ?");
$stmt->execute([$content['id']]);
$likeData = $stmt->fetch();
$likeCount = $likeData['count'];

// Check if user liked this
$userLiked = false;
if (isLoggedIn()) {
    $stmt = $db->prepare("SELECT id FROM likes WHERE content_id = ? AND user_id = ?");
    $stmt->execute([$content['id'], getUserId()]);
    $userLiked = (bool)$stmt->fetch();
}

// Get comments
$stmt = $db->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.content_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$content['id']]);
$comments = $stmt->fetchAll();

// Icon mapping
$iconMap = [
    'fa-syringe' => 'السكري',
    'fa-heartbeat' => 'القلب',
    'fa-heart' => 'القلب',
    'fa-weight' => 'السمنة',
    'fa-lungs' => 'الجهاز التنفسي',
    'fa-bacon' => 'الكيتو',
    'fa-fish' => 'المتوسطي',
    'fa-apple-alt' => 'داش',
    'fa-leaf' => 'نباتي'
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($content['title']); ?> - WellCare</title>
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
            display: flex;
            flex-direction: column;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            padding: 1rem 2rem;
            box-shadow: var(--shadow);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo span {
            color: #FFD700;
        }
        
        .header-back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.3rem;
        }
        
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }
        
        .article-header {
            margin-bottom: 2.5rem;
        }
        
        .article-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        
        .article-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .article-title {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .article-content {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow);
            margin-bottom: 2.5rem;
        }
        
        .section {
            margin-bottom: 2.5rem;
        }
        
        .section-title {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .highlight-box {
            background: rgba(46, 139, 87, 0.1);
            border-right: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }
        
        .list {
            padding-right: 1.5rem;
        }
        
        .list li {
            margin-bottom: 0.8rem;
        }
        
        /* Comments and Likes */
        .interaction-section {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }
        
        .like-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        
        .like-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .like-btn.liked {
            background: var(--accent);
        }
        
        .like-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .like-count {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .comment-input {
            width: 100%;
            padding: 1.2rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            margin-bottom: 1rem;
            min-height: 100px;
        }
        
        .comment-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
        }
        
        .comment-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .comments-list {
            margin-top: 2rem;
        }
        
        .comment {
            background: var(--light);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-right: 3px solid var(--secondary);
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .comment-user {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-name {
            font-weight: 600;
        }
        
        .comment-date {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .login-message {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .login-message a {
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <button class="header-back-btn" onclick="window.location.href='health-journey.html'">
                <i class="fas fa-arrow-right"></i>
            </button>
            
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h1>Well<span>Care</span></h1>
            </div>
            
            <div style="width: 50px;"></div>
        </div>
    </header>
    
    <main>
        <div class="article-header">
            <div class="article-icon">
                <i class="fas <?php echo htmlspecialchars($content['icon']); ?>"></i>
            </div>
            <h1 class="article-title"><?php echo htmlspecialchars($content['title']); ?></h1>
        </div>
        
        <div class="article-content">
            <?php if (isset($sections['definition'])): ?>
            <div class="section">
                <h2 class="section-title">التعريف</h2>
                <p><?php echo htmlspecialchars($sections['definition']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($sections['types']) && is_array($sections['types'])): ?>
            <div class="section">
                <h2 class="section-title">الأنواع</h2>
                <ul class="list">
                    <?php foreach ($sections['types'] as $type): ?>
                        <li><?php echo htmlspecialchars($type); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (isset($sections['symptoms']) && is_array($sections['symptoms'])): ?>
            <div class="section">
                <h2 class="section-title">الأعراض</h2>
                <ul class="list">
                    <?php foreach ($sections['symptoms'] as $symptom): ?>
                        <li><?php echo htmlspecialchars($symptom); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (isset($sections['prevention']) && is_array($sections['prevention'])): ?>
            <div class="section">
                <h2 class="section-title">طرق الوقاية</h2>
                <ul class="list">
                    <?php foreach ($sections['prevention'] as $preventionItem): ?>
                        <li><?php echo htmlspecialchars($preventionItem); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Comments and Likes Section -->
        <div class="interaction-section">
            <h2 class="section-title">التفاعل مع المحتوى</h2>
            
            <!-- Like Section -->
            <div class="like-section">
                <?php if (isLoggedIn()): ?>
                    <button class="like-btn <?php echo $userLiked ? 'liked' : ''; ?>" id="likeBtn" onclick="handleToggleLike()">
                        <i class="fas fa-heart"></i>
                        <span id="likeText"><?php echo $userLiked ? 'تم الإعجاب' : 'أعجبني'; ?></span>
                    </button>
                <?php else: ?>
                    <button class="like-btn" disabled title="سجل الدخول للإعجاب">
                        <i class="fas fa-heart"></i>
                        <span>أعجبني</span>
                    </button>
                <?php endif; ?>
                <span class="like-count" id="likeCount"><?php echo $likeCount; ?> إعجاب</span>
            </div>
            
            <!-- Comments Section -->
            <h3 class="section-title">التعليقات</h3>
            
            <?php if (isLoggedIn()): ?>
                <div class="add-comment">
                    <textarea 
                        class="comment-input" 
                        id="commentInput"
                        placeholder="اكتب تعليقك هنا..."
                    ></textarea>
                    <button class="comment-btn" onclick="handleAddComment()">
                        <i class="fas fa-paper-plane"></i> إرسال التعليق
                    </button>
                </div>
            <?php else: ?>
                <div class="login-message">
                    <i class="fas fa-info-circle"></i>
                    <a href="login.php">سجل الدخول</a> لإضافة تعليق
                </div>
            <?php endif; ?>
            
            <div class="comments-list" id="commentsList">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <div class="comment-user">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                                    </div>
                                    <span class="user-name"><?php echo htmlspecialchars($comment['username']); ?></span>
                                </div>
                                <span class="comment-date"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <div class="comment-text"><?php echo htmlspecialchars($comment['comment_text']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray);">لا توجد تعليقات بعد. كن أول من يعلق!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script src="assets/js/app.js"></script>
    <script>
        const contentId = <?php echo $content['id']; ?>;
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        
        // Toggle Like
        async function handleToggleLike() {
            if (!isLoggedIn) {
                alert('الرجاء تسجيل الدخول أولاً');
                return;
            }
            
            const btn = document.getElementById('likeBtn');
            btn.disabled = true;
            
            const result = await toggleLike(contentId);
            
            if (result.success) {
                // Update UI
                const likeBtn = document.getElementById('likeBtn');
                const likeText = document.getElementById('likeText');
                const likeCount = document.getElementById('likeCount');
                
                if (result.data.user_liked) {
                    likeBtn.classList.add('liked');
                    likeText.textContent = 'تم الإعجاب';
                } else {
                    likeBtn.classList.remove('liked');
                    likeText.textContent = 'أعجبني';
                }
                
                likeCount.textContent = result.data.like_count + ' إعجاب';
            } else {
                alert(result.message);
            }
            
            btn.disabled = false;
        }
        
        // Add Comment
        async function handleAddComment() {
            if (!isLoggedIn) {
                alert('الرجاء تسجيل الدخول أولاً');
                return;
            }
            
            const input = document.getElementById('commentInput');
            const text = input.value.trim();
            
            if (!text) {
                alert('الرجاء كتابة تعليق');
                return;
            }
            
            const result = await addComment(contentId, text);
            
            if (result.success) {
                // Clear input
                input.value = '';
                
                // Add comment to list
                const commentsList = document.getElementById('commentsList');
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `
                    <div class="comment-header">
                        <div class="comment-user">
                            <div class="user-avatar">${result.data.username.charAt(0).toUpperCase()}</div>
                            <span class="user-name">${result.data.username}</span>
                        </div>
                        <span class="comment-date">الآن</span>
                    </div>
                    <div class="comment-text">${text}</div>
                `;
                commentsList.insertBefore(newComment, commentsList.firstChild);
                
                alert(result.message);
            } else {
                alert(result.message);
            }
        }
    </script>
</body>
</html>
