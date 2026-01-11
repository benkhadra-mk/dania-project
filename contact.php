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
    <title>تواصل معنا - WellCare</title>
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
        
        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .contact-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .contact-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .contact-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .contact-card h3 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .contact-card p {
            font-size: 1.2rem;
            color: var(--dark);
        }
        
        .contact-card a {
            color: var(--secondary);
            text-decoration: none;
        }
        
        .cta-section {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--gray);
        }
        
        .cta-btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        
        .cta-btn:hover {
            background: var(--primary-light);
            transform: translateY(-3px);
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
            <h1><i class="fas fa-envelope"></i> تواصل معنا</h1>
            <p style="font-size: 1.3rem; color: var(--gray);">نحن هنا لمساعدتك، لا تتردد في التواصل معنا</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>البريد الإلكتروني</h3>
                <p><a href="mailto:info@wellcare.com">info@wellcare.com</a></p>
                <p><a href="mailto:support@wellcare.com">support@wellcare.com</a></p>
            </div>
            
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>الهاتف</h3>
                <p>+123 456 7890</p>
                <p>السبت - الخميس: 9:00 ص - 6:00 م</p>
            </div>
            
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>العنوان</h3>
                <p>شارع الصحة</p>
                <p>المدينة الطبية، المبنى رقم 123</p>
            </div>
        </div>
        
        <div class="cta-section">
            <h2>أرسل لنا رسالة</h2>
            <p>هل لديك استفسار أو اقتراح؟ نحن نحب أن نسمع منك!</p>
            <a href="mailto:info@wellcare.com?subject=استفسار من موقع WellCare" class="cta-btn">
                <i class="fas fa-paper-plane"></i> راسلنا الآن
            </a>
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
