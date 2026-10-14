<?php
session_start();
require_once 'db_connect.php';
require_once 'modules/vehicle/vehicleEntry.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("status" => "failed", "message" => "Session expired, please login again"));
    exit;
}

if (empty($_POST['vehicleNo'])) {
    echo json_encode(array("status" => "failed", "message" => "Please select a vehicle"));
    exit;
}

$vehicleNo = trim($_POST['vehicleNo']);
$date = date('Y-m-d');

// Optional d-m-Y date, used by the vehicle entry limit modal
if (!empty($_POST['date'])) {
    $dateTime = DateTime::createFromFormat('d-m-Y', trim($_POST['date']));

    if (!$dateTime) {
        echo json_encode(array("status" => "failed", "message" => "Invalid date"));
        exit;
    }

    $date = $dateTime->format('Y-m-d');
}

$entryStatus = getVehicleEntryStatus($db, $vehicleNo, $date);

if (empty($entryStatus)) {
    echo json_encode(array("status" => "success", "limited" => false, "exceeded" => false, "max" => 0, "count" => 0));
} else {
    echo json_encode(array(
        "status" => "success",
        "limited" => $entryStatus['limited'],
        "exceeded" => $entryStatus['exceeded'],
        "max" => $entryStatus['max'],
        "count" => $entryStatus['count']
    ));
}

$db->close();
?>
