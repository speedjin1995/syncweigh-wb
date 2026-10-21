<?php
session_start();
require_once 'db_connect.php';
require_once 'modules/vehicle/vehicleEntry.php';
require_once 'requires/lookup.php';
require_once 'requires/deposit.php';

$username = $_SESSION["username"];

if(isset($_POST['cancelReason'], $_POST['isEmptyContainer'])){
	// Cancel needs the Weighing cancelled permission on the status of every record being cancelled
	$cancelChecks = array();
	if (!empty($_POST['id'])) {
		$cancelChecks[] = array(($_POST['isEmptyContainer'] == 'Y') ? 'Weight_Container' : 'Weight', $_POST['id']);
	}
	if (!empty($_POST['containerId'])) {
		$cancelChecks[] = array('Weight_Container', $_POST['containerId']);
	}
	foreach ($cancelChecks as $cancelCheck) {
		$cancelIds = array_filter(array_map('intval', explode(',', $cancelCheck[1])));
		if (empty($cancelIds)) {
			continue;
		}
		$cancelResult = $db->query("SELECT transaction_status FROM {$cancelCheck[0]} WHERE id IN (" . implode(',', $cancelIds) . ")");
		if ($cancelResult === false) {
			echo json_encode(array("status" => "failed", "message" => "Something went wrong"));
			exit();
		}
		while ($cancelRow = $cancelResult->fetch_assoc()) {
			if (!statusPermitted('Weighing', $cancelRow['transaction_status'], 'cancelled')) {
				echo json_encode(array("status" => "failed", "message" => "You do not have permission to cancel this weighing record"));
				exit();
			}
		}
	}

	$cancelReason = filter_input(INPUT_POST, 'cancelReason', FILTER_SANITIZE_STRING);
	$isEmptyContainer = filter_input(INPUT_POST, 'isEmptyContainer', FILTER_SANITIZE_STRING);
	//$del = "1";
	$cancel = "Y";
	$action = "3";
	$isMulti = '';
	$id;
	$containerId; 
	
	if(isset($_POST['isMulti']) && $_POST['isMulti']!=null && $_POST['isMulti']!=""){
		$isMulti = $_POST['isMulti'];
	}

	if(isset($_POST['id']) && $_POST['id']!=null && $_POST['id']!=""){
		$id = $_POST['id'];
	}

	if(isset($_POST['containerId']) && $_POST['containerId']!=null && $_POST['containerId']!=""){
		$containerId = $_POST['containerId'];
	} 

	if ($isMulti == 'Y'){
		$ids = $_POST['id'];

		if ($isEmptyContainer == 'Y'){
			$deleteStatus = '1';
			if ($stmt2 = $db->prepare("UPDATE Weight_Container SET status=?, cancelled_reason=? WHERE id IN ($ids)")) {
				$stmt2->bind_param('ss', $deleteStatus, $cancelReason);
				
				if($stmt2->execute()){
					reverseVehicleEntry($db, 'Weight_Container', $ids, $username, $cancelReason);
					$stmt2->close();
					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Deleted"
						)
					);
		
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
			$statusArray = [];
			$message = [];

			if (isset($id) && !empty($id)){
				if ($stmt = $db->prepare("UPDATE Weight SET is_complete=?, is_cancel=?, cancelled_reason=? WHERE id IN ($ids)")) {
					$stmt->bind_param('sss', $cancel, $cancel, $cancelReason);
					
					if($stmt->execute()){
						$stmt->close();
						reverseVehicleEntry($db, 'Weight', $ids, $username, $cancelReason);
						depositRefundWeights($db, $ids, $username);
						$statusArray[] = "success";
						$message[] = "Deleted";
					} else{
						$statusArray[] = "failed";
						$message[] = $stmt->error;
					}
				} 
				else{
					$statusArray[] = "failed";
					$message[] = "Somethings wrong";
				}
			}

			$deleteStatus = '1';
			if (isset($containerId) && !empty($containerId)){
				if ($stmt2 = $db->prepare("UPDATE Weight_Container SET status=?, cancelled_reason=? WHERE id IN ($containerId)")) {
					$stmt2->bind_param('ss', $deleteStatus, $cancelReason);
					
					if($stmt2->execute()){
						$stmt2->close();
						reverseVehicleEntry($db, 'Weight_Container', $containerId, $username, $cancelReason);
						$statusArray[] = "success";
						$message[] = "Deleted";
					} else{
						$statusArray[] = "failed";
						$message[] = $stmt->error;
					}
				} 
				else{
					$statusArray[] = "failed";
					$message[] = "Somethings wrong";
				}
			}

			if (in_array('failed', $statusArray)) {
				$status = 'failed';
			}else{
				$status = 'success';
				$message = 'Deleted';
			}

			echo json_encode(
				array(
					"status"=> $status, 
					"message"=> $message
				)
			);

			$db->close();
		}
	}else{
		if ($isEmptyContainer == 'Y'){
			$deleteStatus = '1';
			if ($stmt2 = $db->prepare("UPDATE Weight_Container SET status=?, cancelled_reason=? WHERE id=?")) {
				$stmt2->bind_param('sss', $deleteStatus, $cancelReason, $id);
				
				if($stmt2->execute()){
					reverseVehicleEntry($db, 'Weight_Container', $id, $username, $cancelReason);
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
					reverseVehicleEntry($db, 'Weight', $id, $username, $cancelReason);
					depositRefundWeights($db, $id, $username);
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
