<?php
/**
 * Plugin API Diagnostic Tool
 * Tests if plugins are being loaded correctly
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Plugin API Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .info { color: #0af; }
        pre { background: #000; padding: 15px; border: 1px solid #333; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>🔍 Plugin API Diagnostic Tool</h1>

    <?php
    require_once __DIR__ . '/../includes/Database.php';

    echo "<h2>1. Database Connection Test</h2>";
    try {
        $db = new Database();
        echo "<p class='success'>✓ Database connected successfully!</p>";

        echo "<h2>2. Checking Plugins Table</h2>";
        $result = $db->query("SELECT COUNT(*) as count FROM plugins");
        $row = $result->fetch_assoc();
        echo "<p class='success'>✓ Found {$row['count']} plugins in database</p>";

        echo "<h2>3. Sample Plugins Data</h2>";
        $plugins = $db->query("SELECT id, name, category, status FROM plugins LIMIT 5");
        echo "<pre>";
        while ($plugin = $plugins->fetch_assoc()) {
            echo "ID: {$plugin['id']}, Name: {$plugin['name']}, Category: {$plugin['category']}, Status: {$plugin['status']}\n";
        }
        echo "</pre>";

        echo "<h2>4. API Endpoint Test</h2>";
        echo "<p class='info'>Testing: /api/plugins/list.php</p>";

        // Test API endpoint
        $apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                  "://{$_SERVER['HTTP_HOST']}/api/plugins/list.php";
        echo "<p>API URL: <a href='$apiUrl' target='_blank'>$apiUrl</a></p>";

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "<p>HTTP Status: <span class='" . ($httpCode == 200 ? 'success' : 'error') . "'>$httpCode</span></p>";

        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if ($data) {
                echo "<p class='success'>✓ API returned " . count($data) . " plugins</p>";
                echo "<h3>Sample API Response:</h3>";
                echo "<pre>" . json_encode(array_slice($data, 0, 2), JSON_PRETTY_PRINT) . "</pre>";
            } else {
                echo "<p class='error'>✗ API response is not valid JSON</p>";
                echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
            }
        } else {
            echo "<p class='error'>✗ API request failed</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }

    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>

    <h2>5. JavaScript Fetch Test</h2>
    <button onclick="testFetch()">Test Fetch API</button>
    <div id="fetchResult"></div>

    <script>
        async function testFetch() {
            const resultDiv = document.getElementById('fetchResult');
            resultDiv.innerHTML = '<p style="color: #0af;">Testing...</p>';

            try {
                const response = await fetch('/api/plugins/list.php');
                const data = await response.json();

                resultDiv.innerHTML = `
                    <p style="color: #0f0;">✓ Fetch successful! Received ${data.length} plugins</p>
                    <pre>${JSON.stringify(data.slice(0, 2), null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<p style="color: #f00;">✗ Fetch failed: ${error.message}</p>`;
            }
        }
    </script>

    <p style="margin-top: 40px; color: #666;">Delete this file after testing for security!</p>
</body>
</html>
