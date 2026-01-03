<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    $auth = new Auth();
    $user = $auth->requireAuth();

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $name = trim($data['name'] ?? '');
    $category = $data['category'] ?? '';
    $description = trim($data['description'] ?? '');
    $tag = trim($data['tag'] ?? '');

    $validCategories = ['core', 'streaming', 'smarthome', 'control', 'creative'];

    if (empty($name) || empty($category) || empty($description) || empty($tag)) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        exit;
    }

    if (!in_array($category, $validCategories)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid category']);
        exit;
    }

    $db = new Database();

    // Insert plugin
    $author = $data['author'] ?? $user['username'];
    $social_url = $data['socialUrl'] ?? $data['social_url'] ?? null;
    $image_color = $data['imageColor'] ?? $data['image_color'] ?? 'from-blue-600 to-indigo-600';
    $icon = $data['icon'] ?? 'fa-puzzle-piece';
    $download_url = $data['downloadUrl'] ?? $data['download_url'] ?? null;
    $instruction_url = $data['instructionUrl'] ?? $data['instruction_url'] ?? null;
    $version = $data['version'] ?? '1.0.0';

    $stmt = $db->prepare("INSERT INTO plugins (name, category, tag, author, author_id, social_url, description, image_color, icon, download_url, instruction_url, version, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

    $stmt->bind_param("ssssisssssss", $name, $category, $tag, $author, $user['id'], $social_url, $description, $image_color, $icon, $download_url, $instruction_url, $version);

    if (!$stmt->execute()) {
        throw new Exception('Failed to create plugin');
    }

    $pluginId = $db->lastInsertId();

    // Insert devices
    $devices = $data['devices'] ?? ['Mixlar Mix'];
    $stmt = $db->prepare("INSERT INTO plugin_devices (plugin_id, device_name) VALUES (?, ?)");
    foreach ($devices as $device) {
        $stmt->bind_param("is", $pluginId, $device);
        $stmt->execute();
    }

    echo json_encode([
        'message' => 'Plugin submitted for review',
        'plugin' => ['id' => $pluginId]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
