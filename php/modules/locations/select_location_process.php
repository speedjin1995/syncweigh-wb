<?php
session_start();

// User must login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../../../login.php");
    exit;
}

// Validate form
if(!isset($_POST['plant']) || empty($_POST['plant'])){
    header("location: ../../../selectLocation.php?error=choose_plant");
    exit;
}

if(!isset($_POST['location_id']) || empty($_POST['location_id'])){
    header("location: ../../../selectLocation.php?error=choose_location");
    exit;
}

$location_id = intval($_POST['location_id']);
$plant = intval($_POST['plant']);

// Plant must be one assigned to the user
$userPlantIds = is_array($_SESSION['plant_id'] ?? null) ? array_map('intval', $_SESSION['plant_id']) : [];
if(!in_array($plant, $userPlantIds, true)){
    header("location: ../../../selectLocation.php?error=choose_plant");
    exit;
}

// Location must be active and belong to the selected plant
require_once '../../db_connect.php';
$validLocation = false;
if($stmt = $db->prepare("SELECT id FROM Location WHERE id = ? AND plant_id = ? AND status = 0")){
    $stmt->bind_param('ii', $location_id, $plant);
    if($stmt->execute()){
        $validLocation = $stmt->get_result()->num_rows > 0;
    }
    $stmt->close();
}

if(!$validLocation){
    header("location: ../../../selectLocation.php?error=choose_location");
    exit;
}

$_SESSION['location_id'] = $location_id;
$_SESSION['selected_plant_id'] = $plant;

// Redirect to app
if(($_SESSION['package'] ?? '') == 'Lite'){
    // Redirect user to welcome page
    header("location: ../../../simple.php");
}
else{
    // Redirect user to welcome page
    header("location: ../../../index.php");
}