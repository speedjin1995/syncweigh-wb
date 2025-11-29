<?php
session_start();

// User must login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

// Validate form
if(!isset($_POST['location_id']) || empty($_POST['location_id'])){
    header("location: ../select_location.php?error=choose_location");
    exit;
}

$location_id = intval($_POST['location_id']);
$_SESSION['location_id'] = $location_id;

// Redirect to app
header("location: ../index.php");
exit;
