<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/PlannedScheduleController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

require_once '../../requires/permissions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$actionPermissions = ['getAll' => 'view', 'getLogs' => 'view_log', 'upload' => 'upload_excel', 'deactivate' => 'cancelled', 'deactivateMulti' => 'cancelled'];
if ($action === 'save') {
    $requiredPermission = empty($_POST['id']) ? 'create' : 'edit';
} else {
    $requiredPermission = $actionPermissions[$action] ?? null;
}
if ($requiredPermission !== null && !hasModulePermission('Planned Schedule', 'Planned Schedule', [$requiredPermission])) {
    echo json_encode(['status' => 'failed', 'message' => 'You do not have permission to do this']);
    exit();
}
$controller = new PlannedScheduleController($db);

switch ($action) {
    case 'getAll': $controller->getAll(); break;
    case 'upload': $controller->upload(); break;
    case 'save': $controller->save(); break;
    case 'deactivate': $controller->deactivate(); break;
    case 'deactivateMulti': $controller->deactivateMulti(); break;
    case 'getLogs': $controller->getLogs(); break;
    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
}
?>
