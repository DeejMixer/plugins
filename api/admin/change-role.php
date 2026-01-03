<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    $auth = new Auth();
    $currentUser = $auth->requireAdmin();

    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    $role = $data['role'] ?? null;

    if (!$id || !$role || !in_array($role, ['user', 'admin'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid request']);
        exit;
    }

    $db = new Database();

    $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'User not found']);
        exit;
    }

    echo json_encode(['message' => 'User role updated']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
