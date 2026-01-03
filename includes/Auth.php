<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Verify JWT token from request
    public function verifyToken() {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] :
                     (isset($headers['authorization']) ? $headers['authorization'] : null);

        if (!$authHeader) {
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $payload = JWT::decode($token, JWT_SECRET);

        if (!$payload) {
            return null;
        }

        // Get user from database
        $stmt = $this->db->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $payload['userId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        return $user;
    }

    // Generate JWT token
    public function generateToken($userId) {
        $payload = [
            'userId' => $userId,
            'exp' => time() + JWT_EXPIRY
        ];

        return JWT::encode($payload, JWT_SECRET);
    }

    // Hash password
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verify password
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Check if user is admin
    public function isAdmin($user) {
        return $user && $user['role'] === 'admin';
    }

    // Require authentication
    public function requireAuth() {
        $user = $this->verifyToken();

        if (!$user) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }

        return $user;
    }

    // Require admin role
    public function requireAdmin() {
        $user = $this->requireAuth();

        if (!$this->isAdmin($user)) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied. Admin only.']);
            exit;
        }

        return $user;
    }

    // Generate password reset token
    public function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
}
