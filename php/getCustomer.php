<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
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
                $message['customer_code'] = $row['customer_code'];
                $message['company_reg_no'] = $row['company_reg_no'];
                $message['new_reg_no'] = $row['new_reg_no'];
                $message['name'] = $row['name'];
                $message['address_line_1'] = $row['address_line_1'];
                $message['address_line_2'] = $row['address_line_2'];
                $message['address_line_3'] = $row['address_line_3'];
                $message['phone_no'] = $row['phone_no'];
                $message['fax_no'] = $row['fax_no'];
                $message['contact_name'] = $row['contact_name'];
                $message['ic_no'] = $row['ic_no'];
                $message['tin_no'] = $row['tin_no'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
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