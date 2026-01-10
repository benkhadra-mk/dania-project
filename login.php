<?php
/**
 * Login Page - WellCare
 */

require_once 'config/session.php';
require_once 'utils/security.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - WellCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #2E8B57, #4A90E2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 4rem;
            color: #2E8B57;
            margin-bottom: 10px;
        }

        h1 {
            color: #2E8B57;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        h1 span {
            color: #FFD700;
        }

        .subtitle {
            color: #6C757D;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #343A40;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #2E8B57;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #2E8B57,#4A90E2);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #2E8B57;
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #FFE5E5;
            color: #FF6B6B;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .success-message {
            background: #E5F7ED;
            color: #2E8B57;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1>Well<span>Care</span></h1>
            <p class="subtitle">تسجيل الدخول إلى حسابك</p>
        </div>

        <div id="error-message" class="error-message"></div>
        <div id="success-message" class="success-message"></div>

        <form id="login-form">
            <div class="form-group">
                <label for="login">اسم المستخدم أو البريد الإلكتروني</label>
                <input type="text" id="login" name="login" required>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </button>
        </form>

        <div class="links">
            <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
            <p><a href="index.php">العودة إلى الصفحة الرئيسية</a></p>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            const successDiv = document.getElementById('success-message');
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';

            const formData = {
                login: document.getElementById('login').value,
                password: document.getElementById('password').value
            };

            const result = await loginUser(formData);

            if (result.success) {
                successDiv.textContent = result.message;
                successDiv.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                errorDiv.textContent = result.message;
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>
