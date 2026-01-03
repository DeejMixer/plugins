<?php
/**
 * Web-Based Setup Script for Hostinger
 * Access this file via browser to set up your database and admin user
 *
 * URL: https://yourdomain.com/setup.php
 *
 * ⚠️ IMPORTANT: Delete this file after setup is complete!
 */

// Prevent direct access without confirmation
$confirmSetup = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mixlar Marketplace - Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-weight: 600;
            color: #444;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        .log-output {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .log-success { color: #4ec9b0; }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
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
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .config-info {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .config-info code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .step {
            counter-increment: step;
            margin-bottom: 20px;
        }
        .step::before {
            content: counter(step);
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        .steps {
            counter-reset: step;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Mixlar Marketplace Setup</h1>
        <p class="subtitle">Set up your database and create the admin user</p>

<?php if (!$confirmSetup): ?>

        <div class="info-box warning">
            <strong>⚠️ Before You Start:</strong><br>
            Make sure you have updated <code>config/config.php</code> with your actual database password!
        </div>

        <div class="section">
            <div class="section-title">What This Setup Will Do:</div>
            <ul style="margin-left: 20px; color: #666; line-height: 1.8;">
                <li>Test database connection</li>
                <li>Create admin user (email: admin@mixlarlabs.com, password: admin123)</li>
                <li>Import all plugins from list.json into the database</li>
            </ul>
        </div>

        <div class="info-box">
            <strong>📋 Checklist:</strong><br>
            <div style="margin-top: 10px;">
                <input type="checkbox" id="check1"> I have updated <code>config/config.php</code> with my database password<br>
                <input type="checkbox" id="check2"> My database exists and is accessible<br>
                <input type="checkbox" id="check3"> I have run <code>sql/schema.sql</code> to create the tables<br>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="?confirm=yes" class="btn">Run Setup Now</a>
        </div>

<?php else: ?>

        <div class="log-output">
<?php
ob_start();

echo "<span class='log-info'>===================================</span>\n";
echo "<span class='log-info'>Mixlar Marketplace - Setup</span>\n";
echo "<span class='log-info'>===================================</span>\n\n";

try {
    // Load configuration
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/Database.php';

    echo "<span class='log-info'>Configuration loaded</span>\n";
    echo "  DB Host: " . DB_HOST . "\n";
    echo "  DB User: " . DB_USER . "\n";
    echo "  DB Name: " . DB_NAME . "\n";

    // Check if password is still placeholder
    if (DB_PASS === 'YOUR_PASSWORD_HERE') {
        throw new Exception("Database password is still set to placeholder! Please update config/config.php");
    }
    echo "  DB Pass: <span class='log-success'>✓ Set</span>\n\n";

    // Test database connection
    echo "<span class='log-info'>Testing database connection...</span>\n";
    $db = new Database();
    echo "<span class='log-success'>✓ Database connected successfully!</span>\n\n";

    // Check if tables exist
    echo "<span class='log-info'>Checking database tables...</span>\n";
    $tables = ['users', 'plugins', 'plugin_devices'];
    $missingTables = [];

    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows === 0) {
            $missingTables[] = $table;
        }
    }

    if (!empty($missingTables)) {
        echo "<span class='log-error'>✗ Missing tables: " . implode(', ', $missingTables) . "</span>\n";
        echo "<span class='log-warning'>⚠️  You need to import sql/schema.sql first!</span>\n";
        echo "\nHow to import schema.sql:\n";
        echo "1. Go to Hostinger → Databases → phpMyAdmin\n";
        echo "2. Select database: u677493242_plugins\n";
        echo "3. Click 'Import' tab\n";
        echo "4. Upload sql/schema.sql file\n";
        echo "5. Click 'Go' to import\n";
        throw new Exception("Database tables not found");
    }
    echo "<span class='log-success'>✓ All required tables exist</span>\n\n";

    // Check for admin user
    echo "<span class='log-info'>Checking for admin user...</span>\n";
    $result = $db->query("SELECT id FROM users WHERE email = '" . $db->escapeString(ADMIN_EMAIL) . "'");

    if ($result->num_rows === 0) {
        echo "<span class='log-info'>Creating admin user...</span>\n";
        $adminPassword = password_hash(ADMIN_PASSWORD, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $username = 'admin';
        $email = ADMIN_EMAIL;
        $stmt->bind_param("sss", $username, $email, $adminPassword);
        $stmt->execute();
        $adminId = $db->lastInsertId();
        echo "<span class='log-success'>✓ Admin user created (ID: $adminId)</span>\n";
        echo "  Email: <span class='log-warning'>" . ADMIN_EMAIL . "</span>\n";
        echo "  Password: <span class='log-warning'>" . ADMIN_PASSWORD . "</span>\n\n";
    } else {
        $adminId = $result->fetch_assoc()['id'];
        echo "<span class='log-success'>✓ Admin user already exists (ID: $adminId)</span>\n\n";
    }

    // Load plugins from list.json
    $jsonFile = __DIR__ . '/list.json';
    if (!file_exists($jsonFile)) {
        throw new Exception("list.json not found");
    }

    $pluginsData = json_decode(file_get_contents($jsonFile), true);
    if (!$pluginsData) {
        throw new Exception("Could not parse list.json");
    }

    echo "<span class='log-info'>Found " . count($pluginsData) . " plugins in list.json</span>\n\n";

    // Clear existing plugins (optional)
    echo "<span class='log-info'>Clearing existing plugins...</span>\n";
    $db->query("DELETE FROM plugin_devices");
    $db->query("DELETE FROM plugins");
    echo "<span class='log-success'>✓ Existing plugins cleared</span>\n\n";

    // Import plugins
    echo "<span class='log-info'>Importing plugins...</span>\n";
    $imported = 0;

    foreach ($pluginsData as $plugin) {
        $name = $plugin['name'];
        $category = $plugin['category'];
        $tag = $plugin['tag'];
        $status = $plugin['status'];
        $author = $plugin['author'];
        $social_url = $plugin['socialUrl'] ?? null;
        $description = $plugin['description'];
        $image_color = $plugin['imageColor'] ?? 'from-blue-600 to-indigo-600';
        $icon = $plugin['icon'] ?? 'fa-puzzle-piece';
        $download_url = $plugin['downloadUrl'] ?? null;
        $instruction_url = $plugin['instructionUrl'] ?? null;
        $version = $plugin['version'] ?? '1.0.0';

        $stmt = $db->prepare("
            INSERT INTO plugins (
                name, category, tag, status, author, author_id, social_url,
                description, image_color, icon, download_url, instruction_url, version
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssssssss",
            $name, $category, $tag, $status, $author, $adminId, $social_url,
            $description, $image_color, $icon, $download_url, $instruction_url, $version
        );

        if ($stmt->execute()) {
            $pluginId = $db->lastInsertId();

            // Insert devices
            $devices = $plugin['devices'] ?? ['Mixlar Mix'];
            $deviceStmt = $db->prepare("INSERT INTO plugin_devices (plugin_id, device_name) VALUES (?, ?)");

            foreach ($devices as $device) {
                $deviceStmt->bind_param("is", $pluginId, $device);
                $deviceStmt->execute();
            }

            $imported++;
            echo "  <span class='log-success'>✓</span> Imported: $name (ID: $pluginId)\n";
        } else {
            echo "  <span class='log-error'>✗</span> Failed to import: $name\n";
        }
    }

    echo "\n<span class='log-info'>===================================</span>\n";
    echo "<span class='log-success'>🎉 Setup completed successfully!</span>\n";
    echo "<span class='log-info'>===================================</span>\n\n";
    echo "Imported <span class='log-success'>$imported</span> plugins\n\n";
    echo "<span class='log-warning'>Admin credentials:</span>\n";
    echo "  Email: <span class='log-success'>" . ADMIN_EMAIL . "</span>\n";
    echo "  Password: <span class='log-success'>" . ADMIN_PASSWORD . "</span>\n\n";
    echo "<span class='log-error'>⚠️  IMPORTANT: Change the admin password after first login!</span>\n";
    echo "<span class='log-error'>⚠️  IMPORTANT: Delete setup.php file for security!</span>\n";

    $setupSuccess = true;

} catch (Exception $e) {
    echo "\n<span class='log-error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    $setupSuccess = false;
}

$output = ob_get_clean();
echo $output;
?>
        </div>

        <div style="margin-top: 30px;">
<?php if (isset($setupSuccess) && $setupSuccess): ?>
            <div class="info-box success">
                <strong>✓ Setup Complete!</strong><br>
                You can now log in to your marketplace.
            </div>

            <a href="/frontend/public/login.html" class="btn">Go to Login Page</a>

            <div style="margin-top: 20px;">
                <a href="?confirm=yes&delete=confirm" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete setup.php? You can always upload it again if needed.')">
                    Delete setup.php (Recommended)
                </a>
            </div>
<?php else: ?>
            <div class="info-box error">
                <strong>Setup Failed</strong><br>
                Please check the error messages above and try again.
            </div>

            <a href="/" class="btn">Go Back</a>
<?php endif; ?>
        </div>

<?php endif; ?>

    </div>
</body>
</html>

<?php
// Delete setup.php if requested
if (isset($_GET['delete']) && $_GET['delete'] === 'confirm') {
    if (unlink(__FILE__)) {
        echo '<script>alert("setup.php has been deleted successfully!"); window.location.href = "/frontend/public/login.html";</script>';
    } else {
        echo '<script>alert("Could not delete setup.php. Please delete it manually via File Manager.");</script>';
    }
}
?>
