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

// Get raw input for debugging
$rawInput = file_get_contents('php://input');
error_log("Login attempt - Raw input: " . $rawInput);

$data = json_decode($rawInput, true);

// Check if JSON parsing failed
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON parsing error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['message' => 'Invalid request format']);
    exit;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Log the received data (without password)
error_log("Login attempt - Email: " . $email . ", Has password: " . (empty($password) ? 'NO' : 'YES'));

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Email and password are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid email format']);
    exit;
}

try {
    $db = new Database();
    $auth = new Auth();

    error_log("Database connection successful");

    // Find user
    $stmt = $db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        error_log("User not found for email: " . $email);
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    error_log("User found - ID: " . $user['id'] . ", Role: " . $user['role']);

    // Verify password
    if (!$auth->verifyPassword($password, $user['password'])) {
        error_log("Password verification failed for user: " . $email);
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    error_log("Login successful for user: " . $email);

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
    error_log("Login error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['message' => 'Server error. Please check if database is configured correctly.']);
}
