<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u677493242_plugins');  // Your Hostinger database username
define('DB_PASS', 'YOUR_PASSWORD_HERE');  // ⚠️ REPLACE WITH YOUR ACTUAL DATABASE PASSWORD
define('DB_NAME', 'u677493242_plugins');  // Your Hostinger database name

// JWT Configuration
define('JWT_SECRET', 'mixlar_jwt_secret_' . md5('u677493242_plugins' . time()));  // Auto-generated secure secret
define('JWT_EXPIRY', 604800); // 7 days in seconds

// Email Configuration (Hostinger SMTP)
define('SMTP_HOST', 'smtp.hostinger.com');  // Hostinger SMTP server
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@yourdomain.com');  // ⚠️ REPLACE with your actual email
define('SMTP_PASS', 'your-email-password');     // ⚠️ REPLACE with your email password
define('EMAIL_FROM', 'noreply@yourdomain.com'); // ⚠️ REPLACE with your domain
define('EMAIL_FROM_NAME', 'Mixlar Marketplace');

// Application Settings
define('SITE_URL', 'https://yourdomain.com');  // ⚠️ REPLACE with your actual domain URL
define('ADMIN_EMAIL', 'admin@mixlarlabs.com');
define('ADMIN_PASSWORD', 'admin123'); // Default admin password

// Security
define('ENABLE_CORS', true);
define('ALLOWED_ORIGINS', '*'); // Change to specific domain in production

// Error Reporting (disabled for production)
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Errors logged, not displayed
ini_set('log_errors', 1);
