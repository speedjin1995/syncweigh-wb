<?php
session_start();
require_once 'db_connect.php';

$username = $_SESSION["username"];

if(isset($_POST['id'], $_POST['deleteReason'])) {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$del = "1";
	
	$isMulti = 'N';
	if(isset($_POST['isMulti']) && $_POST['isMulti']!=null && $_POST['isMulti']!=""){
		$isMulti = $_POST['isMulti'];
	}

	if ($isMulti == 'MULTI'){
		if(is_array($_POST['id'])){
			$ids = implode(",", $_POST['id']);
		}else{
			$ids = $_POST['id'];
		}

		if ($stmt2 = $db->prepare("UPDATE Cash_Book SET deleted=?, delete_reason=? WHERE id IN ($ids)")) {
			$stmt2->bind_param('ss', $del, $_POST['deleteReason']);
			
			if($stmt2->execute()){
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
		if ($stmt2 = $db->prepare("UPDATE Cash_Book SET deleted=?, delete_reason=? WHERE id=?")) {
			$stmt2->bind_param('sss', $del, $_POST['deleteReason'], $id);
			
			if($stmt2->execute()){
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Deleted"
					)
				);
	
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
