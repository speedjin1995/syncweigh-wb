<?php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION['company'])){
    session_destroy();
    header("location: ../login.php");
    exit;
}
else{
    date_default_timezone_set('Asia/Kuala_Lumpur');
    //$db = mysqli_connect("localhost", "u735381057_plytech", "@Sync5500", "u735381057_plytech");
    $db = mysqli_connect("srv2051.hstgr.io", "u735381057_plytech", "@Sync5500", "u735381057_plytech");
    
    if(mysqli_connect_errno()){
        echo 'Database connection failed with following errors: ' . mysqli_connect_error();
        die();
    }
}
?>
