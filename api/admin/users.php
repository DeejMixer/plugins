<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    $auth = new Auth();
    $user = $auth->requireAdmin();

    $db = new Database();

    $result = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
