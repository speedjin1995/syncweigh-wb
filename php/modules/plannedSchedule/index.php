<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/PlannedScheduleController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

// Same roles that can see Master Data in layouts/sidebar.php
if (!in_array($_SESSION['roles'] ?? '', array('MANAGER', 'ADMIN', 'SADMIN'))) {
    echo json_encode(['status' => 'failed', 'message' => 'You do not have permission to use the planned schedule']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
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
