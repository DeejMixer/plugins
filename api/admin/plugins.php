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

    $status = $_GET['status'] ?? null;
    $category = $_GET['category'] ?? null;

    $sql = "SELECT p.*, u.username as author_username, u.email as author_email,
            GROUP_CONCAT(pd.device_name) as devices
            FROM plugins p
            LEFT JOIN users u ON p.author_id = u.id
            LEFT JOIN plugin_devices pd ON p.id = pd.plugin_id";

    $conditions = [];
    $params = [];
    $types = '';

    if ($status && $status !== 'all') {
        $conditions[] = "p.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    if ($category && $category !== 'all') {
        $conditions[] = "p.category = ?";
        $params[] = $category;
        $types .= 's';
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";

    if (!empty($params)) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($sql);
    }

    $plugins = [];
    while ($row = $result->fetch_assoc()) {
        $row['authorId'] = ['email' => $row['author_email']];
        $row['devices'] = $row['devices'] ? explode(',', $row['devices']) : ['Mixlar Mix'];
        $row['featured'] = (bool)$row['featured'];
        $row['downloads'] = (int)$row['downloads'];
        unset($row['author_email'], $row['author_username']);
        $plugins[] = $row;
    }

    echo json_encode($plugins);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
