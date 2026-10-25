<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/DepositController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$controller = new DepositController($db);

try {
    switch ($action) {
        case 'check': $controller->check(); break;
        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (Throwable $e) {
    error_log('deposit/index.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
?>
