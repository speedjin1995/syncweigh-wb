<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../bootstrap.php';

use App\Modules\Weight\WeightController;
use App\Modules\Weight\WeightService;

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$service = new WeightService($db, (string)$_SESSION['username']);
$controller = new WeightController($service);

try {
    switch ($action) {
        case 'exportExcel':
            $file = $controller->exportExcel();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $file['fileName'] . '"');
            echo $file['content'];
            break;

        case 'exportPdf':
            echo json_encode($controller->exportPdf());
            break;

        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    error_log('weight/api.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
