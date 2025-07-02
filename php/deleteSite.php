<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "1";
	$action = "3";

	$type = '';

	if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
		$type = $_POST['type'];
	}
	
	if ($type == 'MULTI'){
		if(is_array($_POST['userID'])){
			$ids = implode(",", $_POST['userID']);
		}else{
			$ids = $_POST['userID'];
		}

		if ($stmt2 = $db->prepare("UPDATE Site SET status=? WHERE id IN ($ids)")) {
			$stmt2->bind_param('s', $del);
			
			if($stmt2->execute()){
				foreach($_POST['userID'] as $id){
					if ($insert_stmt = $db->prepare("INSERT INTO Site_Log (site_id, action_id, action_by) VALUES (?, ?, ?)")) {
						$insert_stmt->bind_param('sss', $id, $action, $username);
						// Execute the prepared query.
						$insert_stmt->execute();
						$insert_stmt->close();
					}
				}

				$stmt2->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Deleted"
					)
				);
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
					"message"=> "Somthings wrong"
				)
			);
		}
	}else{
		if ($stmt2 = $db->prepare("UPDATE Site SET status=? WHERE id=?")) {
			$stmt2->bind_param('ss', $del , $id);
			
			if($stmt2->execute()){
				if ($insert_stmt = $db->prepare("INSERT INTO Site_Log (site_id, action_id, action_by) VALUES (?, ?, ?)")) {
					$insert_stmt->bind_param('sss', $id, $action, $username);
		
					// Execute the prepared query.
					if (! $insert_stmt->execute()) {
						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $insert_stmt->error
							)
						);
					}
					else{
						$insert_stmt->close();
						echo json_encode(
							array(
								"status"=> "success", 
								"message"=> "Deleted"
							)
						);
					}
				}

				$stmt2->close();
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
