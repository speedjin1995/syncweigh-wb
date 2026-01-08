<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $ids = $_POST['id'];
    $unitPrices = $_POST['unit_price'];
    $subTotals = $_POST['sub_total'];
    $ssts = $_POST['sst'];
    $totalPrices = $_POST['total_price'];
    
    $success = true;
    
    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i];
        $unitPrice = $unitPrices[$i];
        $subTotal = $subTotals[$i];
        $sst = $ssts[$i];
        $totalPrice = $totalPrices[$i];
        
        if ($update_stmt = $db->prepare("UPDATE Weight SET unit_price=?, sub_total=?, sst=?, total_price=?, modified_by=? WHERE id=?")) {
            $update_stmt->bind_param('ssssss', $unitPrice, $subTotal, $sst, $totalPrice, $username, $id);
            
            if (!$update_stmt->execute()) {
                $success = false;
                break;
            }
            $update_stmt->close();
        } else {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        echo json_encode(array(
            "status" => "success",
            "message" => "Pricing updated successfully!"
        ));
    } else {
        echo json_encode(array(
            "status" => "failed",
            "message" => "Failed to update pricing"
        ));
    }
    
    $db->close();
}
?>