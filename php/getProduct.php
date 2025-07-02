<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Product WHERE id=?")) {
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
                $message['product_code'] = $row['product_code'];
                $message['name'] = $row['name'];
                $message['price'] = $row['price'];
                $message['description'] = $row['description'];
                $message['variance'] = $row['variance'];
                $message['high'] = $row['high'];
                $message['low'] = $row['low'];
            }

            // retrieve products
            $empQuery = "SELECT * FROM Product_RawMat WHERE product_id = $id AND status = '0' ORDER BY id ASC";
            $empRecords = mysqli_query($db, $empQuery);
            $rawMats = array();
            $rawMatCount = 1;

            while($row2 = mysqli_fetch_assoc($empRecords)) {
                $rawMats[] = array(
                    "no" => $rawMatCount,
                    "id" => $row2['id'],
                    "product_id" => $row2['product_id'],
                    "raw_mat_code" => $row2['raw_mat_code'],
                    "raw_mat_weight" => $row2['raw_mat_weight'],
                );
                $rawMatCount++;
            }

            $message['rawMats'] = $rawMats;
            
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