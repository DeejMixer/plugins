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

    // Total plugins
    $result = $db->query("SELECT COUNT(*) as count FROM plugins");
    $totalPlugins = $result->fetch_assoc()['count'];

    // Approved plugins
    $result = $db->query("SELECT COUNT(*) as count FROM plugins WHERE status = 'approved'");
    $approvedPlugins = $result->fetch_assoc()['count'];

    // Pending plugins
    $result = $db->query("SELECT COUNT(*) as count FROM plugins WHERE status = 'pending'");
    $pendingPlugins = $result->fetch_assoc()['count'];

    // Total users
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $result->fetch_assoc()['count'];

    // Total downloads
    $result = $db->query("SELECT SUM(downloads) as total FROM plugins");
    $totalDownloads = $result->fetch_assoc()['total'] ?? 0;

    echo json_encode([
        'totalPlugins' => (int)$totalPlugins,
        'approvedPlugins' => (int)$approvedPlugins,
        'pendingPlugins' => (int)$pendingPlugins,
        'totalUsers' => (int)$totalUsers,
        'totalDownloads' => (int)$totalDownloads
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
