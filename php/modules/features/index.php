<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/BaseController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$controller = new BaseController($db);

switch ($action) {
    case 'save': $controller->saveCompanyFeature(); break;
    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
}
?>
