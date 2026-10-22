<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';
require_once 'services/BaseService.php';

$username = $_SESSION["username"];

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = '';

	if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
		$type = $_POST['type'];
	}

	$service = new BaseService($db, $username);

	$module = $service->getReactivateModule($type);
	if ($module !== null && !hasModulePermission('Master Data', $module, ['reactivate'])) {
		echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
		exit;
	}

	try {
		$service->reactivateMasterData($type, $id);
		echo json_encode(array("status" => "success", "message" => "Reactivated"));
	} catch (Exception $e) {
		echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
	}
} else{
	echo json_encode(
		array(
			"status"=> "failed",
			"message"=> "Please fill in all the fields"
		)
	);
}
?>
