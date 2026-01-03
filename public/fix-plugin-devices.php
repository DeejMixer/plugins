<?php
/**
 * Fix Missing Plugin Devices Table and Data
 * Run this once to create the table and populate device data
 */

require_once __DIR__ . '/../includes/Database.php';

echo "<h1>Fixing Plugin Devices</h1>";
echo "<pre>";

try {
    $db = new Database();

    // Check if plugin_devices table exists
    $result = $db->query("SHOW TABLES LIKE 'plugin_devices'");

    if ($result->num_rows === 0) {
        echo "Creating plugin_devices table...\n";

        $createTable = "CREATE TABLE plugin_devices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            plugin_id INT NOT NULL,
            device_name VARCHAR(100) NOT NULL,
            FOREIGN KEY (plugin_id) REFERENCES plugins(id) ON DELETE CASCADE,
            INDEX idx_plugin_id (plugin_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->query($createTable);
        echo "✓ Table created!\n\n";
    } else {
        echo "✓ plugin_devices table already exists\n\n";
    }

    // Check if we have data
    $count = $db->query("SELECT COUNT(*) as count FROM plugin_devices")->fetch_assoc();
    echo "Current device entries: {$count['count']}\n\n";

    // Get all plugins without devices
    echo "Adding default device 'Mixlar Mix' to all plugins...\n";

    $plugins = $db->query("SELECT id, name FROM plugins");
    $added = 0;

    while ($plugin = $plugins->fetch_assoc()) {
        // Check if this plugin already has devices
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM plugin_devices WHERE plugin_id = ?");
        $stmt->bind_param("i", $plugin['id']);
        $stmt->execute();
        $deviceCount = $stmt->get_result()->fetch_assoc()['count'];

        if ($deviceCount == 0) {
            // Add default device
            $insertStmt = $db->prepare("INSERT INTO plugin_devices (plugin_id, device_name) VALUES (?, 'Mixlar Mix')");
            $insertStmt->bind_param("i", $plugin['id']);
            $insertStmt->execute();
            echo "  ✓ Added device for: {$plugin['name']}\n";
            $added++;
        }
    }

    echo "\n✓ Added devices for $added plugins\n\n";

    // Test the API query
    echo "Testing API query...\n";
    $testQuery = "SELECT p.*, GROUP_CONCAT(pd.device_name) as devices
                  FROM plugins p
                  LEFT JOIN plugin_devices pd ON p.id = pd.plugin_id
                  WHERE p.status IN ('approved', 'instruction', 'download', 'installed')
                  GROUP BY p.id
                  LIMIT 3";

    $result = $db->query($testQuery);
    echo "Found " . $result->num_rows . " plugins\n\n";

    while ($row = $result->fetch_assoc()) {
        echo "  - {$row['name']} (devices: {$row['devices']})\n";
    }

    echo "\n✅ All fixed! Plugins should now load on the website.\n";
    echo "\n⚠️ Delete this file after running for security!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
