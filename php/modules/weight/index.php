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

switch ($action) {
    case 'getPendingByVehicle': $controller->getPendingByVehicle(); break;
    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
}
?>
