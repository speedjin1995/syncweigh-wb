<?php
session_start();
require_once '../../db_connect.php';
require_once '../../classes/ProfileController.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$controller = new ProfileController($db);

switch ($action) {
    case 'updateProfile': $controller->updateProfile(); break;
    case 'changePassword': $controller->changePassword(); break;
    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
}
?>
