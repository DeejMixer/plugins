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
$email = trim($data['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Please enter a valid email']);
    exit;
}

try {
    $db = new Database();
    $auth = new Auth();

    // Find user
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Always return success to prevent email enumeration
    if (!$user) {
        echo json_encode(['message' => 'If that email exists, a reset link has been sent']);
        exit;
    }

    // Generate reset token
    $resetToken = $auth->generateResetToken();
    $hashedToken = hash('sha256', $resetToken);
    $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour

    // Save token to database
    $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $hashedToken, $expiry, $user['id']);
    $stmt->execute();

    // Send email
    Email::sendPasswordReset($email, $resetToken);

    echo json_encode(['message' => 'If that email exists, a reset link has been sent']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
