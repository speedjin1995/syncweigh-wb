<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Location WHERE id=?")) {
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
                $message['location_code'] = $row['location_code'];
                $message['location_name'] = $row['location_name'];
                $message['indicator'] = $row['indicator'];
                $message['com_port'] = $row['com_port'];
                $message['bits_per_second'] = $row['bits_per_second'];
                $message['data_bits'] = $row['data_bits'];
                $message['parity'] = $row['parity'];
                $message['stop_bits'] = $row['stop_bits'];
                $message['plant_id'] = $row['plant_id'];
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