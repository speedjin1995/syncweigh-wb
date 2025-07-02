<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
$db = mysqli_connect("srv597.hstgr.io", "u664110560_".$_SESSION['company'], "@Sync5500", "u664110560_".$_SESSION['company']);

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}
?>
