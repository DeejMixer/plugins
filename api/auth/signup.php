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
require_once __DIR__ . '/../../includes/Email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

$errors = [];

if (strlen($username) < 3) {
    $errors[] = ['msg' => 'Username must be at least 3 characters'];
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = ['msg' => 'Please enter a valid email'];
}

if (strlen($password) < 6) {
    $errors[] = ['msg' => 'Password must be at least 6 characters'];
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

try {
    $db = new Database();
    $auth = new Auth();

    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['message' => 'User already exists']);
        exit;
    }

    // Create user
    $hashedPassword = $auth->hashPassword($password);
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if (!$stmt->execute()) {
        throw new Exception('Failed to create user');
    }

    $userId = $db->lastInsertId();

    // Send welcome email
    Email::sendWelcome($email, $username);

    // Generate token
    $token = $auth->generateToken($userId);

    // Return user data
    echo json_encode([
        'token' => $token,
        'user' => [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'role' => 'user'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
