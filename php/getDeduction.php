<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['userID'], $_POST['type'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

    $table = null;
    $idName = null;
    if ($type == 'Customer') {
        $table = "Customer_Deduction";
        $idName = "customer_id";
    } else if ($type == 'Supplier') {
        $table = "Supplier_Deduction";
        $idName = "supplier_id";
    }

    if ($table == null) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Invalid type"
            )); 
    }else{
        if ($update_stmt = $db->prepare("SELECT * FROM $table WHERE $idName=?")) {
            $update_stmt->bind_param('s', $id);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $result = $update_stmt->get_result();
                $message = array();

                while ($row = $result->fetch_assoc()) {
                    $message['id'] = $row['id'];
                    $message['customer_supplier_id'] = ($type == 'Customer') ? $row['customer_id'] : $row['supplier_id'];
                    $message['F1'] = $row['F1'];
                    $message['F2'] = $row['F2'];
                    $message['F3'] = $row['F3'];
                    $message['F4'] = $row['F4'];
                    $message['F5'] = $row['F5'];
                    $message['F6'] = $row['F6'];
                    $message['F7'] = $row['F7'];
                    $message['F8'] = $row['F8'];
                    $message['F9'] = $row['F9'];
                    $message['F10'] = $row['F10'];
                    $message['F11'] = $row['F11'];
                    $message['F12'] = $row['F12'];
                    $message['auto_data'] = json_decode($row['auto_data'], true);
                    $message['status'] = $row['status'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>