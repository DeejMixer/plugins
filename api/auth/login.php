<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid credentials']);
    exit;
}

try {
    $db = new Database();
    $auth = new Auth();

    // Find user
    $stmt = $db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    // Verify password
    if (!$auth->verifyPassword($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    // Generate token
    $token = $auth->generateToken($user['id']);

    // Return user data (without password)
    echo json_encode([
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
