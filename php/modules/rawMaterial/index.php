<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/RawMaterialController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
require_once '../../requires/permissions.php';
$actionPermissions = ['create' => 'create', 'update' => 'edit', 'delete' => 'cancelled', 'deleteMulti' => 'cancelled', 'upload' => 'upload_excel', 'savePricing' => 'manage_pricing'];
if (isset($actionPermissions[$action]) && !hasModulePermission('Master Data', 'Raw Material', [$actionPermissions[$action]])) {
    echo json_encode(['status' => 'failed', 'message' => 'You do not have permission to do this']);
    exit;
}
$controller = new RawMaterialController($db);

switch ($action) {
    case 'create':  $controller->create(); break;
    case 'update':  $controller->update(); break;
    case 'delete':  $controller->delete(); break;
    case 'deleteMulti': $controller->deleteMulti(); break;
    case 'upload':  $controller->upload(); break;
    case 'get':     $controller->get(); break;
    case 'getAll':  $controller->getAll(); break;
    case 'getPricing': $controller->getPricing(); break;
    case 'savePricing': $controller->savePricing(); break;
    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
}
?>
