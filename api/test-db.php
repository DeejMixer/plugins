<?php
/**
 * Database Connection Test Script
 * This will help diagnose login issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "===================================\n";
echo "Database Connection Test\n";
echo "===================================\n\n";

// Load config
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

echo "Configuration:\n";
echo "  DB_HOST: " . DB_HOST . "\n";
echo "  DB_USER: " . DB_USER . "\n";
echo "  DB_NAME: " . DB_NAME . "\n";
echo "  DB_PASS: " . (DB_PASS === 'YOUR_PASSWORD_HERE' ? '❌ PLACEHOLDER - NOT SET!' : '✓ Set') . "\n\n";

// Test database connection
echo "Testing database connection...\n";
try {
    $db = new Database();
    echo "✓ Database connected successfully!\n\n";

    // Check if admin user exists
    echo "Checking for admin user...\n";
    $email = ADMIN_EMAIL;
    $stmt = $db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "✓ Admin user found!\n";
        echo "  ID: " . $user['id'] . "\n";
        echo "  Username: " . $user['username'] . "\n";
        echo "  Email: " . $user['email'] . "\n";
        echo "  Role: " . $user['role'] . "\n";
        echo "  Password hash: " . substr($user['password'], 0, 20) . "...\n\n";

        // Test password verification
        echo "Testing password verification with 'admin123'...\n";
        $auth = new Auth();
        $testPassword = 'admin123';

        if ($auth->verifyPassword($testPassword, $user['password'])) {
            echo "✓ Password verification SUCCESS! The password 'admin123' matches.\n\n";
        } else {
            echo "✗ Password verification FAILED! The password does not match.\n";
            echo "  Expected password: " . ADMIN_PASSWORD . "\n";
            echo "  Test password: admin123\n\n";

            // Test if password is stored as plain text
            if ($user['password'] === 'admin123') {
                echo "⚠️  WARNING: Password is stored as PLAIN TEXT!\n";
                echo "  This is a security issue. Run sql/seed.php to fix.\n\n";
            }
        }

    } else {
        echo "✗ Admin user NOT FOUND!\n";
        echo "  Expected email: " . ADMIN_EMAIL . "\n";
        echo "  Run: php sql/seed.php to create the admin user\n\n";
    }

} catch (Exception $e) {
    echo "✗ Database connection FAILED!\n";
    echo "  Error: " . $e->getMessage() . "\n\n";

    if (DB_PASS === 'YOUR_PASSWORD_HERE') {
        echo "❌ ACTION REQUIRED:\n";
        echo "  1. Edit config/config.php\n";
        echo "  2. Replace 'YOUR_PASSWORD_HERE' with your actual database password\n";
        echo "  3. Save the file and try again\n\n";
    }
}

echo "===================================\n";
echo "Test Complete\n";
echo "===================================\n";
