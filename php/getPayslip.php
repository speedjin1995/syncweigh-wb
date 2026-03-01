<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Payslip WHERE id=?")) {
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
                $message['payslip_no'] = $row['payslip_no'];
                $message['user_id'] = $row['user_id'];
                $message['date'] = $row['date'];
                $message['payment_type'] = $row['payment_type'];
                $message['cheque_no'] = $row['cheque_no'];
                $message['cashbook_id'] = $row['cashbook_id'];
                $message['earnings_detail'] = json_decode($row['earnings_detail'], true);
                $message['deductions_detail'] = json_decode($row['deductions_detail'], true);
                $message['gross_pay'] = $row['gross_pay'];
                $message['total_deductions'] = $row['total_deductions'];
                $message['net_pay'] = $row['net_pay'];

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