<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../bootstrap.php';

use App\Modules\Settings\SettingsController;
use App\Modules\Settings\SettingsService;

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'failed', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$service = new SettingsService($db, (string)$_SESSION['username']);
$controller = new SettingsController($service);

try {
    switch ($action) {
        case 'updateProfile':
            echo json_encode($controller->profile());
            break;

        case 'changePassword':
            echo json_encode($controller->password());
            break;

        case 'updateCompany':
            echo json_encode($controller->company());
            break;

        case 'updatePort':
            echo json_encode($controller->port());
            break;

        default:
            echo json_encode(['status' => 'failed', 'message' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    error_log('settings/api.php - ' . $e->getMessage());
    echo json_encode(['status' => 'failed', 'message' => 'Something went wrong']);
}
