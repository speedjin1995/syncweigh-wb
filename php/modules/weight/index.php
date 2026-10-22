<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/WeightController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$controller = new WeightController($db);

try {
    switch ($action) {
        case 'getPendingByVehicle': $controller->getPendingByVehicle(); break;
        case 'get':                 $controller->get(); break;
        case 'delete':              $controller->delete(); break;
        case 'print':               $controller->print(); break;
        case 'save':                $controller->save(); break;
        case 'list':                $controller->list(); break;
        case 'listEmptyContainer':  $controller->listEmptyContainer(); break;
        case 'containers':          $controller->containers(); break;
        case 'emptyContainer':      $controller->emptyContainer(); break;
        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (Throwable $e) {
    error_log('weight/index.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
?>
