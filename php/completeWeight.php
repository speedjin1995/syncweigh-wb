<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$cancel = "Y";
	
	if ($stmt2 = $db->prepare("UPDATE Weight SET is_complete=? WHERE id=?")) {
		$stmt2->bind_param('ss', $cancel, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Completed"
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
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>