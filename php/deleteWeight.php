<?php
session_start();
require_once 'db_connect.php';

$username = $_SESSION["username"];

if(isset($_POST['id'], $_POST['cancelReason'], $_POST['isEmptyContainer'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$cancelReason = filter_input(INPUT_POST, 'cancelReason', FILTER_SANITIZE_STRING);
	$isEmptyContainer = filter_input(INPUT_POST, 'isEmptyContainer', FILTER_SANITIZE_STRING);
	//$del = "1";
	$cancel = "Y";
	$action = "3";

	if ($isEmptyContainer == 'Y'){
		$deleteStatus = '1';
		if ($stmt2 = $db->prepare("UPDATE Weight_Container SET status=?, cancelled_reason=? WHERE id=?")) {
			$stmt2->bind_param('sss', $deleteStatus, $cancelReason, $id);
			
			if($stmt2->execute()){
				// if ($insert_stmt = $db->prepare("INSERT INTO Supplier_Log (supplier_id, action_id, action_by) VALUES (?, ?, ?)")) {
				// 	$insert_stmt->bind_param('sss', $id, $action, $username);
		
				// 	// Execute the prepared query.
				// 	if (! $insert_stmt->execute()) {
				// 		echo json_encode(
				// 		    array(
				// 		        "status"=> "failed", 
				// 		        "message"=> $insert_stmt->error
				// 		    )
				// 		);
				// 	}
				// 	else{
						// $insert_stmt->close();
						// echo json_encode(
						// 	array(
						// 		"status"=> "success", 
						// 		"message"=> "Deleted"
						// 	)
						// );
				// 	}
				// }
	
				$stmt2->close();
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Deleted"
					)
				);
	
				// $stmt2->close();
				$db->close();
			} else{
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $stmt2->error
					)
				);
			}
		} 
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Somethings wrong"
				)
			);
		}
	}else{
		if ($stmt2 = $db->prepare("UPDATE Weight SET is_complete=?, is_cancel=?, cancelled_reason=? WHERE id=?")) {
			$stmt2->bind_param('ssss', $cancel, $cancel, $cancelReason, $id);
			
			if($stmt2->execute()){
				// if ($insert_stmt = $db->prepare("INSERT INTO Supplier_Log (supplier_id, action_id, action_by) VALUES (?, ?, ?)")) {
				// 	$insert_stmt->bind_param('sss', $id, $action, $username);
		
				// 	// Execute the prepared query.
				// 	if (! $insert_stmt->execute()) {
				// 		echo json_encode(
				// 		    array(
				// 		        "status"=> "failed", 
				// 		        "message"=> $insert_stmt->error
				// 		    )
				// 		);
				// 	}
				// 	else{
						// $insert_stmt->close();
						// echo json_encode(
						// 	array(
						// 		"status"=> "success", 
						// 		"message"=> "Deleted"
						// 	)
						// );
				// 	}
				// }
	
				$stmt2->close();
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Deleted"
					)
				);
	
				// $stmt2->close();
				$db->close();
			} else{
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $stmt2->error
					)
				);
			}
		} 
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Somethings wrong"
				)
			);
		}
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
