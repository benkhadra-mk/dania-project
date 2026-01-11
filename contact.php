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
        
        /* Enhanced Contact Form */
        .contact-form {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            border: 1px solid rgba(46, 139, 87, 0.1);
        }
        
        .contact-form h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .contact-form > p {
            text-align: center;
            color: var(--gray);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }
        
        .form-group label {
            position: absolute;
            right: 3rem;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 0.5rem;
        }
        
        .form-group.textarea-group label {
            top: 1.2rem;
            transform: none;
        }
        
        .form-group input:focus ~ label,
        .form-group input:not(:placeholder-shown) ~ label,
        .form-group textarea:focus ~ label,
        .form-group textarea:not(:placeholder-shown) ~ label {
            top: -0.5rem;
            font-size: 0.85rem;
            color: var(--primary);
            font-weight: 700;
        }
        
        .form-group i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .form-group.textarea-group i {
            top: 1.2rem;
            transform: none;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem 1rem 1rem 3.5rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(46, 139, 87, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: transparent;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            padding-top: 1.2rem;
        }
        
        .char-counter {
            text-align: left;
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 0.3rem;
        }
        
        .char-counter.warning {
            color: #ff9800;
        }
        
        .char-counter.danger {
            color: var(--accent);
        }
        
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.2rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(46, 139, 87, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(-1px);
        }
        
        .submit-btn:disabled {
            background: linear-gradient(135deg, var(--gray), #888);
            cursor: not-allowed;
            transform: none;
        }
        
        .submit-btn:disabled::before {
            display: none;
        }
        
        .alert {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: slideDown 0.3s ease;
            font-weight: 600;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .form-group input.error,
        .form-group textarea.error {
            border-color: var(--accent);
        }
        
        .form-group input.success,
        .form-group textarea.success {
            border-color: #28a745;
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
        
        <!-- Contact Form -->
        <div class="contact-form">
            <h2><i class="fas fa-paper-plane"></i> أرسل استفسارك</h2>
            <p>نحن هنا للإجابة على جميع أسئلتك واستفساراتك</p>
            <div id="formMessage"></div>
            <form id="contactForm">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" required placeholder=" ">
                    <label for="name">الاسم الكامل *</label>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder=" ">
                    <label for="email">البريد الإلكتروني *</label>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-tag"></i>
                    <input type="text" id="subject" name="subject" required placeholder=" ">
                    <label for="subject">موضوع الرسالة *</label>
                </div>
                
                <div class="form-group textarea-group">
                    <i class="fas fa-comment-dots"></i>
                    <textarea id="message" name="message" required placeholder=" " maxlength="1000"></textarea>
                    <label for="message">نص الرسالة *</label>
                    <div class="char-counter" id="charCounter">0 / 1000</div>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> إرسال الاستفسار
                </button>
            </form>
        </div>
        
        <div class="cta-section">
            <h2>تواصل مباشر عبر البريد الإلكتروني</h2>
            <p>يمكنك أيضاً التواصل معنا مباشرة</p>
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
        
        // Character counter
        const messageField = document.getElementById('message');
        const charCounter = document.getElementById('charCounter');
        
        messageField.addEventListener('input', () => {
            const length = messageField.value.length;
            charCounter.textContent = `${length} / 1000`;
            
            charCounter.classList.remove('warning', 'danger');
            if (length > 900) {
                charCounter.classList.add('danger');
            } else if (length > 700) {
                charCounter.classList.add('warning');
            }
        });
        
        // Real-time validation
        const inputs = document.querySelectorAll('#contactForm input, #contactForm textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                if (input.value.trim() !== '' && input.validity.valid) {
                    input.classList.add('success');
                    input.classList.remove('error');
                } else if (input.value.trim() !== '' && !input.validity.valid) {
                    input.classList.add('error');
                    input.classList.remove('success');
                } else {
                    input.classList.remove('success', 'error');
                }
            });
        });
        
        // Form submission
        document.getElementById('contactForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const formMessage = document.getElementById('formMessage');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value
            };
            
            try {
                const response = await fetch('/dania-project/api/contact/send-inquiry.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    formMessage.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + result.message + '</div>';
                    document.getElementById('contactForm').reset();
                    charCounter.textContent = '0 / 1000';
                    inputs.forEach(input => input.classList.remove('success', 'error'));
                } else {
                    formMessage.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' + result.message + '</div>';
                }
            } catch (error) {
                formMessage.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> حدث خطأ أثناء إرسال الرسالة</div>';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الاستفسار';
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
