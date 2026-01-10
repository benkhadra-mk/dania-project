#!/bin/bash
# Fix phpMyAdmin Session Permission Error
# This script fixes the common XAMPP session directory permission issue

echo "=========================================="
echo "Fixing phpMyAdmin Session Error"
echo "=========================================="
echo ""

echo "Setting permissions on /opt/lampp/temp/ directory..."
sudo chmod 777 /opt/lampp/temp/

echo "Setting ownership to Apache user (daemon)..."
sudo chown -R daemon:daemon /opt/lampp/temp/

echo ""
echo "✓ Permissions fixed!"
echo ""
echo "Now restart Apache:"
echo "sudo /opt/lampp/lampp restartapache"
echo ""
echo "Then refresh phpMyAdmin in your browser:"
echo "http://localhost/phpmyadmin"
echo ""
