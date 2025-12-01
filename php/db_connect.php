<?php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION['company'])){
    session_destroy();
    header("location: ../login.php");
    exit;
}
else{
    date_default_timezone_set('Asia/Kuala_Lumpur');
    //$db = mysqli_connect("localhost", "u178823648_".$_SESSION['company'], "@Sync5500", "u178823648_".$_SESSION['company']);
    $db = mysqli_connect("srv1637.hstgr.io", "u178823648_".$_SESSION['company'], "@Sync5500", "u178823648_".$_SESSION['company']);
    
    if(mysqli_connect_errno()){
        echo 'Database connection failed with following errors: ' . mysqli_connect_error();
        die();
    }
}
?>
