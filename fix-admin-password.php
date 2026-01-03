<?php
/**
 * Fix Admin Password
 * This script resets the admin password to 'admin123'
 *
 * Access via browser: https://your-domain.com/fix-admin-password.php
 *
 * ⚠️ Delete this file after use!
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Admin Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 20px; font-size: 24px; }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success { background: #d4edda; border-left-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
        .log-output {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
            margin: 20px 0;
        }
        .log-success { color: #4ec9b0; }
        .log-error { color: #f48771; }
        .log-info { color: #9cdcfe; }
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
            margin-top: 20px;
        }
        .btn:hover { background: #5568d3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Admin Password</h1>

<?php if (!isset($_GET['confirm'])): ?>

        <div class="info-box warning">
            <strong>⚠️ What This Does:</strong><br>
            This will reset the admin password to <strong>admin123</strong>
        </div>

        <p>The test showed that your admin password hash is incorrect. This tool will fix it.</p>

        <a href="?confirm=yes" class="btn">Reset Admin Password Now</a>

<?php else: ?>

        <div class="log-output"><?php
ob_start();

echo "<span class='log-info'>===================================</span>\n";
echo "<span class='log-info'>Fixing Admin Password</span>\n";
echo "<span class='log-info'>===================================</span>\n\n";

try {
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/Database.php';
    require_once __DIR__ . '/includes/Auth.php';

    $db = new Database();
    $auth = new Auth();

    echo "<span class='log-info'>Database connected</span>\n\n";

    // Find admin user
    $email = ADMIN_EMAIL;
    $stmt = $db->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("Admin user not found! Email: " . ADMIN_EMAIL);
    }

    echo "<span class='log-success'>✓ Found admin user (ID: " . $user['id'] . ")</span>\n";
    echo "  Email: " . $user['email'] . "\n\n";

    // Generate correct hash for 'admin123'
    echo "<span class='log-info'>Generating new password hash for 'admin123'...</span>\n";
    $newPassword = 'admin123';
    $newHash = $auth->hashPassword($newPassword);

    echo "<span class='log-success'>✓ New hash generated</span>\n";
    echo "  Hash: " . substr($newHash, 0, 30) . "...\n\n";

    // Verify the new hash works
    if (!$auth->verifyPassword($newPassword, $newHash)) {
        throw new Exception("Hash verification failed! This shouldn't happen.");
    }
    echo "<span class='log-success'>✓ Hash verified successfully</span>\n\n";

    // Update the password in database
    echo "<span class='log-info'>Updating password in database...</span>\n";
    $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newHash, $user['id']);

    if (!$updateStmt->execute()) {
        throw new Exception("Failed to update password: " . $db->error);
    }

    echo "<span class='log-success'>✓ Password updated successfully!</span>\n\n";

    // Verify the fix by reading back
    echo "<span class='log-info'>Verifying the fix...</span>\n";
    $verifyStmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $verifyStmt->bind_param("i", $user['id']);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    $verifyUser = $verifyResult->fetch_assoc();

    if ($auth->verifyPassword($newPassword, $verifyUser['password'])) {
        echo "<span class='log-success'>✓ Verification successful!</span>\n";
        echo "<span class='log-success'>✓ Password is now: admin123</span>\n\n";
    } else {
        throw new Exception("Verification failed! Password still doesn't work.");
    }

    echo "<span class='log-info'>===================================</span>\n";
    echo "<span class='log-success'>🎉 Password Fixed Successfully!</span>\n";
    echo "<span class='log-info'>===================================</span>\n\n";
    echo "<span class='log-info'>You can now log in with:</span>\n";
    echo "  Email: <span class='log-success'>" . ADMIN_EMAIL . "</span>\n";
    echo "  Password: <span class='log-success'>admin123</span>\n\n";
    echo "<span class='log-error'>⚠️ IMPORTANT: Delete this file after use!</span>\n";

    $success = true;

} catch (Exception $e) {
    echo "\n<span class='log-error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    $success = false;
}

$output = ob_get_clean();
echo $output;
?></div>

<?php if (isset($success) && $success): ?>
        <div class="info-box success">
            <strong>✓ Password Fixed!</strong><br>
            You can now log in with the credentials shown above.
        </div>

        <a href="/frontend/public/login.html" class="btn">Go to Login Page</a>

        <div style="margin-top: 10px;">
            <a href="?confirm=yes&delete=yes" class="btn btn-danger"
               onclick="return confirm('Delete this file for security?')">
                Delete fix-admin-password.php
            </a>
        </div>
<?php else: ?>
        <div class="info-box error">
            <strong>Failed to fix password</strong><br>
            Check the error message above.
        </div>
<?php endif; ?>

<?php endif; ?>

    </div>
</body>
</html>

<?php
// Self-delete if requested
if (isset($_GET['delete']) && $_GET['delete'] === 'yes') {
    if (unlink(__FILE__)) {
        echo '<script>alert("File deleted!"); window.location.href="/frontend/public/login.html";</script>';
    }
}
?>
