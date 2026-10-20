<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../bootstrap.php';

use App\Modules\Weight\WeightController;
use App\Modules\Weight\WeightPrintService;
use App\Modules\Weight\WeightService;

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$service = new WeightService($db, (string)$_SESSION['username']);
$controller = new WeightController($service, new WeightPrintService($db, (string)$_SESSION['username']));

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

        case 'list':
            echo json_encode($controller->list());
            break;

        case 'listEmptyContainer':
            echo json_encode($controller->listEmptyContainer());
            break;

        case 'get':
            echo json_encode($controller->get());
            break;

        case 'delete':
            echo json_encode($controller->delete());
            break;

        case 'containers':
            echo json_encode($controller->containers());
            break;

        case 'emptyContainer':
            echo json_encode($controller->emptyContainer());
            break;

        case 'print':
            echo json_encode($controller->print());
            break;

        case 'save':
            echo json_encode($controller->save());
            break;

        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    error_log('weight/api.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
