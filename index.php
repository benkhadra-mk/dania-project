<?php
/**
 * Home Page (Index) - WellCare
 * Main landing page with authentication check
 */

require_once 'config/session.php';
require_once 'utils/security.php';

// Get user info if logged in
$isLoggedIn = isLoggedIn();
$username = $isLoggedIn ? getUsername() : 'user';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellCare - موقع الرعاية الصحية</title>
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
        
        /* Logo Center Section */
        .logo-center {
            text-align: center;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .logo-center i {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .logo-center h1 {
            font-size: 3.5rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .logo-center h1 span {
            color: var(--secondary);
        }
        
        .logo-center p {
            font-size: 1.3rem;
            color: var(--gray);
        }
        
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .hero {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
            border-radius: 20px;
            padding: 4rem 3rem;
            text-align: center;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
        }
        
        .hero h2 {
            font-size: 2.8rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: var(--white);
            font-size: 1.3rem;
            font-weight: 700;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(46, 139, 87, 0.4);
        }
        
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background-color: var(--white);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .service-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .service-icon i {
            font-size: 2.5rem;
            color: var(--white);
        }
        
        .service-card h3 {
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
        <!-- Centered Logo Section -->
        <div class="logo-center">
            <i class="fas fa-heartbeat"></i>
            <h1>Well<span>Care</span></h1>
            <p>موقع الرعاية الصحية</p>
        </div>
        
        <section class="hero">
            <h2>ابدأ رحلتك الصحية مع WellCare</h2>
            <p>نقدم لك أفضل خدمات الرعاية الصحية والاستشارات الطبية عبر الإنترنت</p>
            <?php if ($isLoggedIn): ?>
                <a href="health-journey.php" class="cta-button">
                    <i class="fas fa-play-circle"></i> ابدأ رحلتك الصحية
                </a>
            <?php else: ?>
                <a href="login.php" class="cta-button">
                    <i class="fas fa-sign-in-alt"></i> سجل الدخول للبدء
                </a>
            <?php endif; ?>
        </section>
        
        <div class="services">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3>استشارات طبية</h3>
                <p>تواصل مع أفضل الأطباء المتخصصين</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <h3>إدارة الأدوية</h3>
                <p>نظام متكامل لإدارة أدويتك</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3>تتبع الصحة</h3>
                <p>سجل وتتبع مؤشراتك الصحية</p>
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