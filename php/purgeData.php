<?php
session_start();
require_once 'db_connect.php';

$username = $_SESSION["username"];

if(isset($_POST['fromDateSearch'], $_POST['toDateSearch'])){
	if($_POST['fromDateSearch'] != null && $fromDateSearch['fromDate'] != ''){
        $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDateSearch']);
        $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
    }

    if($_POST['toDateSearch'] != null && $_POST['toDateSearch'] != ''){
        $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDateSearch']);
        $toDateTime = $dateTime->format('Y-m-d 23:59:59');
    }

	$action = "3";
	
	if ($stmt2 = $db->prepare("DELETE FROM Weight SET WHERE transaction_date>=? AND transaction_date<=?")) {
        $stmt2->bind_param('ss', $fromDateTime , $toDateTime);
        
        if($stmt2->execute()){
            $stmt = $db->prepare("DELETE FROM Weight_Log SET WHERE transaction_date>=? AND transaction_date<=?");
            $stmt->bind_param('ss', $fromDateTime , $toDateTime);
            $stmt->execute();

            if ($insert_stmt = $db->prepare("INSERT INTO Purge_Data_Log (from_date, to_date, action_id, action_by) VALUES (?, ?, ?, ?)")) {
            	$insert_stmt->bind_param('ssss', $fromDateTime, $toDateTime, $action, $username);
    
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
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>