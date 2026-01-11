<?php
require_once 'config/session.php';
require_once 'utils/security.php';

$isLoggedIn = isLoggedIn();
$username = $isLoggedIn ? getUsername() : 'user';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>من نحن - WellCare</title>
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
            --transition: all 0.3s ease;
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
        
        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        
        nav a:hover {
            color: #FFD700;
        }
        
        .user-area {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .auth-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .auth-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .logout-btn {
            background: #FF6B6B;
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #ff5252;
            transform: scale(1.05);
        }
        
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .content-section {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .content-section h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .content-section p {
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: var(--light);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .feature-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        footer {
            background-color: var(--dark);
            color: var(--white);
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h1>Well<span>Care</span></h1>
            </div>
            
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> الصفحة الرئيسية</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> من نحن</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> تواصل معنا</a>
            </nav>
            
            <div class="user-area">
                <?php if ($isLoggedIn): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin-dashboard.php" class="auth-btn" style="background: #FFD700; color: var(--dark);">
                            <i class="fas fa-shield-alt"></i> لوحة التحكم
                        </a>
                    <?php endif; ?>
                    <span>مرحباً، <?php echo htmlspecialchars($username); ?>!</span>
                    <button class="logout-btn" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                <?php else: ?>
                    <a href="login.php" class="auth-btn">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </a>
                    <a href="register.php" class="auth-btn">
                        <i class="fas fa-user-plus"></i> إنشاء حساب
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main>
        <div class="page-header">
            <h1><i class="fas fa-info-circle"></i> من نحن</h1>
        </div>
        
        <div class="content-section">
            <h2>مرحباً بك في WellCare</h2>
            <p>
                WellCare هي منصة رعاية صحية متكاملة تهدف إلى توفير معلومات صحية موثوقة وشاملة للجميع.
                نؤمن بأن المعرفة الصحية هي حق للجميع، ولذلك نعمل على تقديم محتوى طبي دقيق ومفهوم بلغة عربية واضحة.
            </p>
            
            <h2>رؤيتنا</h2>
            <p>
                أن نكون المرجع الأول للمعلومات الصحية الموثوقة في العالم العربي، ونساعد الملايين على اتخاذ قرارات صحية مستنيرة لحياة أفضل وأكثر صحة.
            </p>
            
            <h2>مهمتنا</h2>
            <p>
                نقدم محتوى طبي دقيق عن الأمراض الشائعة وطرق الوقاية منها، بالإضافة إلى أنظمة غذائية صحية متوازنة.
                نساعدك على فهم صحتك بشكل أفضل ونمكنك من اتخاذ خطوات إيجابية نحو حياة صحية.
            </p>
        </div>
        
        <div class="content-section">
            <h2>ما نقدمه</h2>
            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-book-medical"></i>
                    <h3>معلومات طبية دقيقة</h3>
                    <p>محتوى علمي موثوق عن الأمراض وطرق الوقاية</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-apple-alt"></i>
                    <h3>أنظمة غذائية صحية</h3>
                    <p>دليل شامل للأنظمة الغذائية المتوازنة</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>مجتمع صحي</h3>
                    <p>مشاركة التجارب والتعليقات مع الآخرين</p>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 WellCare. جميع الحقوق محفوظة</p>
    </footer>
    
    <script src="assets/js/app.js"></script>
    <script>
        async function logout() {
            const result = await logoutUser();
            if (result.success) {
                window.location.href = 'index.php';
            }
        }
    </script>
</body>
</html>
