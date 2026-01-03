<?php
/**
 * Database Seeding Script
 * Imports plugins from list.json into the database
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../config/config.php';

echo "===================================\n";
echo "Mixlar Marketplace - Database Seeder\n";
echo "===================================\n\n";

try {
    $db = new Database();
    echo "✓ Database connected successfully\n\n";

    // Load plugins from list.json
    $jsonFile = __DIR__ . '/../list.json';
    if (!file_exists($jsonFile)) {
        die("✗ Error: list.json not found\n");
    }

    $pluginsData = json_decode(file_get_contents($jsonFile), true);
    if (!$pluginsData) {
        die("✗ Error: Could not parse list.json\n");
    }

    echo "Found " . count($pluginsData) . " plugins in list.json\n\n";

    // Get or create admin user
    echo "Checking for admin user...\n";
    $result = $db->query("SELECT id FROM users WHERE email = '" . $db->escapeString(ADMIN_EMAIL) . "'");

    if ($result->num_rows === 0) {
        echo "Creating admin user...\n";
        $adminPassword = password_hash(ADMIN_PASSWORD, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $username = 'admin';
        $email = ADMIN_EMAIL;
        $stmt->bind_param("sss", $username, $email, $adminPassword);
        $stmt->execute();
        $adminId = $db->lastInsertId();
        echo "✓ Admin user created (ID: $adminId)\n";
        echo "  Email: " . ADMIN_EMAIL . "\n";
        echo "  Password: " . ADMIN_PASSWORD . "\n\n";
    } else {
        $adminId = $result->fetch_assoc()['id'];
        echo "✓ Admin user already exists (ID: $adminId)\n\n";
    }

    // Clear existing plugins (optional - comment out if you want to keep existing data)
    echo "Clearing existing plugins...\n";
    $db->query("DELETE FROM plugin_devices");
    $db->query("DELETE FROM plugins");
    echo "✓ Existing plugins cleared\n\n";

    // Import plugins
    echo "Importing plugins...\n";
    $imported = 0;

    foreach ($pluginsData as $plugin) {
        // Convert camelCase to snake_case for database
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

        // Insert plugin
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
            echo "  ✓ Imported: $name (ID: $pluginId)\n";
        } else {
            echo "  ✗ Failed to import: $name\n";
        }
    }

    echo "\n===================================\n";
    echo "Seeding completed successfully!\n";
    echo "===================================\n\n";
    echo "Imported $imported plugins\n";
    echo "Admin credentials:\n";
    echo "  Email: " . ADMIN_EMAIL . "\n";
    echo "  Password: " . ADMIN_PASSWORD . "\n\n";
    echo "⚠️  IMPORTANT: Change the admin password after first login!\n\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
