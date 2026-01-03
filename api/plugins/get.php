<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['message' => 'Plugin ID required']);
    exit;
}

try {
    $db = new Database();

    $stmt = $db->prepare("SELECT p.*, GROUP_CONCAT(pd.device_name) as devices
                         FROM plugins p
                         LEFT JOIN plugin_devices pd ON p.id = pd.plugin_id
                         WHERE p.id = ?
                         GROUP BY p.id");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plugin = $result->fetch_assoc();

    if (!$plugin) {
        http_response_code(404);
        echo json_encode(['message' => 'Plugin not found']);
        exit;
    }

    $plugin['devices'] = $plugin['devices'] ? explode(',', $plugin['devices']) : ['Mixlar Mix'];
    $plugin['featured'] = (bool)$plugin['featured'];
    $plugin['downloads'] = (int)$plugin['downloads'];

    echo json_encode($plugin);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
