# WellCare - Windows Installation Guide

Complete guide for setting up the WellCare healthcare platform on Windows.

---

## Prerequisites

- Windows 10 or 11
- XAMPP for Windows (Apache + MySQL + PHP)
- Git for Windows
- Web browser (Chrome, Firefox, or Edge)

---

## Step 1: Install XAMPP

1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Run the installer (xampp-windows-x64-installer.exe)
3. Install to default location: `C:\xampp`
4. During installation, select:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin

---

## Step 2: Download Project from GitHub

### Method 1: Using Git (Recommended)

1. Install Git for Windows: https://git-scm.com/download/win
2. Open Command Prompt or PowerShell
3. Navigate to XAMPP htdocs folder:
   ```cmd
   cd C:\xampp\htdocs
   ```
4. Clone the repository:
   ```cmd
   git clone https://github.com/benkhadra-mk/dania-project.git
   ```

### Method 2: Download ZIP

1. Go to: https://github.com/benkhadra-mk/dania-project
2. Click green "Code" button → "Download ZIP"
3. Extract the ZIP file
4. Move the extracted folder to `C:\xampp\htdocs\dania-project`

---

## Step 3: Start XAMPP Services

1. Open XAMPP Control Panel (from Start menu)
2. Start **Apache** - Click "Start" button
3. Start **MySQL** - Click "Start" button
4. Both should show green "Running" status

**Troubleshooting:**
- If Apache fails to start, port 80 might be in use
  - Click "Config" → "Service and Port Settings"
  - Change Main Port to 8080
  - Access site using `http://localhost:8080/dania-project/`

---

## Step 4: Import Database

### Using Command Line:

1. Open Command Prompt as Administrator
2. Navigate to project folder:
   ```cmd
   cd C:\xampp\htdocs\dania-project
   ```
3. Run these commands:
   ```cmd
   C:\xampp\mysql\bin\mysql -u root -e "DROP DATABASE IF EXISTS wellcare; CREATE DATABASE wellcare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

   C:\xampp\mysql\bin\mysql -u root wellcare < database\schema.sql

   C:\xampp\mysql\bin\mysql -u root wellcare < database\seed-data.sql
   ```

### Using phpMyAdmin (Alternative):

1. Open browser and go to: http://localhost/phpmyadmin
2. Click "New" to create database
3. Database name: `wellcare`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Import" tab
6. Choose `database/schema.sql` → Click "Go"
7. Choose `database/seed-data.sql` → Click "Go"

---

## Step 5: Access the Application

1. Open your browser
2. Go to: http://localhost/dania-project/
3. You should see the WellCare homepage

**Test Database Connection:**
- Visit: http://localhost/dania-project/test-connection.php
- Should show "Database connection successful"

---

## Step 6: Login Accounts

### Admin Account
- Email: `admin@wellcare.com`
- Password: `123456`
- Access admin dashboard after login

### Demo User Account
- Email: `demo@wellcare.com`
- Password: `demo123`
- Regular user access

---

## Project URLs

After installation, access these pages:

- Homepage: `http://localhost/dania-project/`
- Login: `http://localhost/dania-project/login.php`
- Register: `http://localhost/dania-project/register.php`
- Health Journey: `http://localhost/dania-project/health-journey.html`
- Admin Dashboard: `http://localhost/dania-project/admin-dashboard.php` (admin only)
- Database Test: `http://localhost/dania-project/test-connection.php`

---

## Common Issues & Solutions

### Apache won't start
**Problem:** Port 80 already in use (Skype, IIS, etc.)

**Solution:**
1. XAMPP Control Panel → Apache "Config" → httpd.conf
2. Find line: `Listen 80`
3. Change to: `Listen 8080`
4. Save and restart Apache
5. Access site at: `http://localhost:8080/dania-project/`

### MySQL won't start
**Problem:** Port 3306 in use or service conflict

**Solution:**
1. Check if MySQL service is running in Windows Services
2. Stop any existing MySQL services
3. Restart XAMPP MySQL

### Database import error
**Problem:** Character encoding issues

**Solution:**
1. Make sure database is created with utf8mb4
2. Import files in order: schema.sql first, then seed-data.sql

### Page shows blank/white screen
**Problem:** PHP errors not displayed

**Solution:**
1. Check Apache error logs: `C:\xampp\apache\logs\error.log`
2. Check PHP errors: Enable display_errors in php.ini

---

## File Permissions (Usually not needed on Windows)

Windows usually doesn't have permission issues like Linux, but if you encounter problems:

1. Right-click `C:\xampp\htdocs\dania-project` folder
2. Properties → Security tab
3. Make sure your user account has "Full Control"

---

## Development Tools (Optional)

### Recommended Code Editor
- Visual Studio Code: https://code.visualstudio.com/
- Extensions: PHP Intelephense, MySQL

### Database Management
- phpMyAdmin: Included with XAMPP at `http://localhost/phpmyadmin`
- HeidiSQL: Free MySQL client for Windows

---

## Updating the Project

To get latest changes from GitHub:

```cmd
cd C:\xampp\htdocs\dania-project
git pull origin main
```

If you made local changes:
```cmd
git stash
git pull origin main
git stash pop
```

---

## Uninstalling

1. Stop Apache and MySQL in XAMPP Control Panel
2. Delete project folder: `C:\xampp\htdocs\dania-project`
3. Drop database in phpMyAdmin or via command:
   ```cmd
   C:\xampp\mysql\bin\mysql -u root -e "DROP DATABASE wellcare;"
   ```

---

## Support

For issues or questions:
- Check PROJECT-STRUCTURE.md for project organization
- Review database/schema.sql for database structure
- Check Apache/PHP error logs in `C:\xampp\apache\logs\`

---

## Quick Reference Commands

```cmd
# Start XAMPP services (from XAMPP Control Panel GUI)

# Access MySQL command line
C:\xampp\mysql\bin\mysql -u root

# Import database
C:\xampp\mysql\bin\mysql -u root wellcare < database\schema.sql

# Check PHP version
C:\xampp\php\php -v

# View Apache error log
type C:\xampp\apache\logs\error.log
```

---

**That's it! Your WellCare platform should now be running on Windows.**
