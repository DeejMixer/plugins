<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    $db = new Database();

    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    $featured = $_GET['featured'] ?? null;

    $sql = "SELECT p.*, GROUP_CONCAT(pd.device_name) as devices
            FROM plugins p
            LEFT JOIN plugin_devices pd ON p.id = pd.plugin_id
            WHERE p.status IN ('approved', 'instruction', 'download', 'installed')";

    $params = [];
    $types = '';

    if ($category && $category !== 'all') {
        $sql .= " AND p.category = ?";
        $params[] = $category;
        $types .= 's';
    }

    if ($featured === 'true') {
        $sql .= " AND p.featured = 1";
    }

    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.tag LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }

    $sql .= " GROUP BY p.id ORDER BY p.featured DESC, p.downloads DESC, p.created_at DESC";

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
        $row['devices'] = $row['devices'] ? explode(',', $row['devices']) : ['Mixlar Mix'];
        $row['featured'] = (bool)$row['featured'];
        $row['downloads'] = (int)$row['downloads'];
        $plugins[] = $row;
    }

    echo json_encode($plugins);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error']);
    error_log($e->getMessage());
}
