<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'mixlar_marketplace');

// JWT Configuration
define('JWT_SECRET', 'your_secret_key_change_this_in_production');
define('JWT_EXPIRY', 604800); // 7 days in seconds

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('EMAIL_FROM', 'noreply@mixlarlabs.com');
define('EMAIL_FROM_NAME', 'Mixlar Marketplace');

// Application Settings
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@mixlarlabs.com');
define('ADMIN_PASSWORD', 'admin123'); // Default admin password

// Security
define('ENABLE_CORS', true);
define('ALLOWED_ORIGINS', '*'); // Change to specific domain in production

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
