#!/bin/bash
# WellCare Project - Quick Setup Script
# This script will set up the database for your healthcare project

echo "================================="
echo "WellCare Project Setup"
echo "================================="
echo ""

# Check if XAMPP is running
echo "Checking XAMPP status..."
if pgrep -x "mysqld" > /dev/null
then
    echo "✓ MySQL is running"
else
    echo "✗ MySQL is not running"
    echo "Starting MySQL..."
    sudo /opt/lampp/lampp startmysql
fi

if pgrep -x "httpd" > /dev/null
then
    echo "✓ Apache is running"
else
    echo "✗ Apache is not running"
    echo "Starting Apache..."
    sudo /opt/lampp/lampp startapache
fi

echo ""
echo "================================="
echo "Importing Database..."
echo "================================="

# Import schema
echo "Creating database structure..."
/opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/dania-project/database/schema.sql

if [ $? -eq 0 ]; then
    echo "✓ Schema imported successfully"
else
    echo "✗ Failed to import schema"
    exit 1
fi

# Import seed data
echo "Importing health content data..."
/opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/dania-project/database/seed-data.sql

if [ $? -eq 0 ]; then
    echo "✓ Seed data imported successfully"
else
    echo "✗ Failed to import seed data"
    exit 1
fi

# Verify tables
echo ""
echo "Verifying database tables..."
/opt/lampp/bin/mysql -u root -e "USE wellcare; SHOW TABLES;"

echo ""
echo "================================="
echo "Setup Complete! 🎉"
echo "================================="
echo ""
echo "Next steps:"
echo "1. Open your browser"
echo "2. Go to: http://localhost/dania-project/test-connection.php"
echo "3. You should see a success message"
echo ""
echo "To login:"
echo "- Go to: http://localhost/dania-project/login.php"
echo "- Username: demo_user"
echo "- Password: demo123"
echo ""
echo "Or register a new account at:"
echo "http://localhost/dania-project/register.php"
echo ""
