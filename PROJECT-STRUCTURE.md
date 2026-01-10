# WellCare Project Structure

Healthcare platform built with PHP, MySQL, and JavaScript. Includes admin panel for content management and user authentication system.

---

## Directory Structure

```
dania-project/
├── api/
│   ├── admin/              # Admin APIs (delete articles/comments, save articles)
│   ├── auth/               # Login, logout, register, check-auth
│   ├── comments/           # Add/get comments
│   ├── content/            # Get health content
│   └── likes/              # Toggle likes
├── assets/js/
│   └── app.js              # Frontend utilities
├── config/
│   ├── database.php        # DB connection
│   └── session.php         # Session handling
├── database/
│   ├── schema.sql          # Tables structure
│   └── seed-data.sql       # Admin account + initial content
├── utils/
│   └── security.php        # Input validation, password hashing, etc.
├── admin-article-edit.php   # Create/edit articles
├── admin-dashboard.php      # Admin panel
├── content.php              # Article viewer (dynamic)
├── health-journey.html      # Browse diseases & diets
├── index.php                # Homepage
├── login.php
├── register.php
├── test-connection.php
├── fix-phpmyadmin.sh
└── setup.sh
```

## Quick Start

```bash
# Import database
sudo /opt/lampp/bin/mysql -u root -e "DROP DATABASE IF EXISTS wellcare; CREATE DATABASE wellcare;"
sudo /opt/lampp/bin/mysql -u root wellcare < database/schema.sql
sudo /opt/lampp/bin/mysql -u root wellcare < database/seed-data.sql

# Access
http://localhost/dania-project/
```

## Accounts

- Admin: `admin@wellcare.com` / `123456`
- Demo: `demo@wellcare.com` / `demo123`

## Access Levels

**Public:** Homepage, login, register, browse articles, view content

**Logged-in Users:** Comment, like articles

**Admin Only:** Dashboard, create/edit/delete articles, delete comments

## API Structure

- `/api/auth/*` - Public
- `/api/content/*` - Public (read-only)
- `/api/comments/*` - Requires login
- `/api/likes/*` - Requires login  
- `/api/admin/*` - Admin only

## Notes

All articles load from database via `content.php?slug=article-name`. No static HTML files for content anymore.

---

# هيكل مشروع WellCare

منصة رعاية صحية مبنية بـ PHP و MySQL و JavaScript. تتضمن لوحة تحكم للإدارة ونظام مصادقة مستخدمين.

---

## هيكل المجلدات

```
dania-project/
├── api/
│   ├── admin/              # واجهات الإدارة (حذف المقالات/التعليقات، حفظ المقالات)
│   ├── auth/               # تسجيل دخول، خروج، إنشاء حساب
│   ├── comments/           # إضافة/عرض التعليقات
│   ├── content/            # عرض المحتوى الصحي
│   └── likes/              # الإعجابات
├── assets/js/
│   └── app.js              # أدوات واجهة المستخدم
├── config/
│   ├── database.php        # الاتصال بقاعدة البيانات
│   └── session.php         # إدارة الجلسات
├── database/
│   ├── schema.sql          # هيكل الجداول
│   └── seed-data.sql       # حساب الإدارة + المحتوى الأولي
├── utils/
│   └── security.php        # التحقق من المدخلات، تشفير كلمات المرور
├── admin-article-edit.php   # إنشاء/تعديل المقالات
├── admin-dashboard.php      # لوحة التحكم
├── content.php              # عرض المقالات (ديناميكي)
├── health-journey.html      # تصفح الأمراض والأنظمة الغذائية
├── index.php                # الصفحة الرئيسية
├── login.php
├── register.php
├── test-connection.php
├── fix-phpmyadmin.sh
└── setup.sh
```

## البدء السريع

```bash
# استيراد قاعدة البيانات
sudo /opt/lampp/bin/mysql -u root -e "DROP DATABASE IF EXISTS wellcare; CREATE DATABASE wellcare;"
sudo /opt/lampp/bin/mysql -u root wellcare < database/schema.sql
sudo /opt/lampp/bin/mysql -u root wellcare < database/seed-data.sql

# الوصول
http://localhost/dania-project/
```

## الحسابات

- الإدارة: `admin@wellcare.com` / `123456`
- تجريبي: `demo@wellcare.com` / `demo123`

## مستويات الوصول

**عام:** الصفحة الرئيسية، تسجيل الدخول، التسجيل، تصفح المقالات

**مستخدمين مسجلين:** التعليق والإعجاب

**الإدارة فقط:** لوحة التحكم، إنشاء/تعديل/حذف المقالات، حذف التعليقات

## هيكل الواجهات البرمجية

- `/api/auth/*` - عام
- `/api/content/*` - عام (قراءة فقط)
- `/api/comments/*` - يتطلب تسجيل دخول
- `/api/likes/*` - يتطلب تسجيل دخول
- `/api/admin/*` - الإدارة فقط

## ملاحظات

جميع المقالات تُحمّل من قاعدة البيانات عبر `content.php?slug=article-name`. لم تعد هناك ملفات HTML ثابتة للمحتوى.
