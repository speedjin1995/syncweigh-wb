<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../bootstrap.php';

use App\Modules\Report\ReportController;
use App\Modules\Report\ReportService;

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$service = new ReportService($db, (string)$_SESSION['username']);
$controller = new ReportController($service);

try {
    switch ($action) {
        case 'list':
            echo json_encode($controller->list());
            break;

        case 'auditLog':
            echo json_encode($controller->auditLog());
            break;

        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    error_log('report/api.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
