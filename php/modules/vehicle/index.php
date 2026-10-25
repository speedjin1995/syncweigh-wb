<?php
session_start();
require_once '../../db_connect.php';
require_once '../../requires/permissions.php';
require_once '../../classes/VehicleController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$actionPermissions = ['delete' => 'cancelled', 'upload' => 'upload_excel'];
if (isset($actionPermissions[$action]) && !hasModulePermission('Master Data', 'Vehicles', [$actionPermissions[$action]])) {
    echo json_encode(['status' => 'failed', 'message' => 'You do not have permission to do this']);
    exit;
}

$controller = new VehicleController($db);

try {
    switch ($action) {
        case 'save':       $controller->save(); break;
        case 'get':        $controller->get(); break;
        case 'list':       $controller->list(); break;
        case 'delete':     $controller->delete(); break;
        case 'upload':     $controller->upload(); break;
        case 'checkEntry': $controller->checkEntry(); break;
        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (Throwable $e) {
    error_log('vehicle/index.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
?>
