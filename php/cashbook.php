<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
// Processing form data when form is submitted
if (isset($_POST['date'], $_POST['cashBookNo'], $_POST['totalDeduction'], $_POST['totalAddition'])) {
    
    if (empty($_POST["id"])) {
        $cashbookId = null;
    } else {
        $cashbookId = trim($_POST["id"]);
    }
    
    if (empty($_POST["date"])) {
        $date = null;
    } else {
        $date = DateTime::createFromFormat('d-m-Y', $_POST["date"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["cashBookNo"])) {
        $name = 'cash_book';
		if($update_stmt2 = $db->prepare("SELECT * FROM miscellaneous WHERE name=?")){
			$update_stmt2->bind_param('s', $name);

			if (! $update_stmt2->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong when querying miscellaneous"
                    )
                ); 
            }
            else{
                $cashBookNo = 'CB';
                $result2 = $update_stmt2->get_result();
				if ($row2 = $result2->fetch_assoc()) {
                    $charSize = strlen($row2['value']);
                    $value = $row2['value'];
                    for($i=0; $i<(4-(int)$charSize); $i++){
                        $cashBookNo.='0';  // S0000
                    }
                    $cashBookNo .= $value;
				} 
            }
		}
    } 
    else {
        $cashBookNo = trim($_POST["cashBookNo"]);
    }

    if (empty($_POST["totalDeduction"])) {
        $totalDeduction = null;
    } else {
        $totalDeduction = trim($_POST["totalDeduction"]);
    }

    if (empty($_POST["totalAddition"])) {
        $totalAddition = null;
    } else {
        $totalAddition = trim($_POST["totalAddition"]);
    }

    // Build JSON objects for each deduction record
    $deductionNo = isset($_POST['deductionNo']) ? $_POST['deductionNo']: [];
    $deductionType = isset($_POST['deductionType']) ? $_POST['deductionType']: [];
    $deductionDesc = isset($_POST['deductionDesc']) ? $_POST['deductionDesc']: [];
    $deductionAmt = isset($_POST['deductionAmt']) ? $_POST['deductionAmt']: [];

    $deductionRecords = [];
    if(isset($deductionNo) && $deductionNo != null && count($deductionNo) > 0){ 
        foreach ($deductionNo as $key => $value) {
            if (!empty($value) && isset($deductionType[$key]) && isset($deductionDesc[$key]) && isset($deductionAmt[$key])) {
                $deductionRecords[] = [
                    "no" => $value,
                    "type" => $deductionType[$key],
                    "desc" => $deductionDesc[$key],
                    "amount" => $deductionAmt[$key]
                ];
            }
        }
    }

    // Build JSON objects for each addition record
    $additionNo = isset($_POST['additionNo']) ? $_POST['additionNo']: [];
    $additionType = isset($_POST['additionType']) ? $_POST['additionType']: [];
    $additionDesc = isset($_POST['additionDesc']) ? $_POST['additionDesc']: [];
    $additionAmt = isset($_POST['additionAmt']) ? $_POST['additionAmt']: [];
    
    $additionRecords = [];
    if(isset($additionNo) && $additionNo != null && count($additionNo) > 0){ 
        foreach ($additionNo as $key => $value) {
            if (!empty($value) && isset($additionType[$key]) && isset($additionDesc[$key]) && isset($additionAmt[$key])) {
                $additionRecords[] = [
                    "no" => $value,
                    "type" => $additionType[$key],
                    "desc" => $additionDesc[$key],
                    "amount" => $additionAmt[$key]
                ];
            }
        }
    }

    if(!empty($cashbookId))
    {
        if ($update_stmt = $db->prepare("UPDATE Cash_Book SET cash_book_no=?, date=?, deduction_details=?, addition_details=?, total_deduction=?, total_addition=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $update_stmt->bind_param('sssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $username, $username, $cashbookId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                $update_stmt->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else
    {
        if ($insert_stmt = $db->prepare("INSERT INTO Cash_Book (cash_book_no, date, deduction_details, addition_details, total_deduction, total_addition, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $insert_stmt->bind_param('ssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $username, $username);

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

                // Update miscellaneous for cash book no
                $name = 'cash_book';
                if($update_stmt2 = $db->prepare("UPDATE miscellaneous SET value=value+1 WHERE name=?")){
                    $update_stmt2->bind_param('s', $name);

                    // Execute the prepared query.
                    if (! $update_stmt2->execute()) {
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt2->error
                            )
                        );
                    }
                    else{
                        $update_stmt2->close();

                        echo json_encode(
                            array(
                                "status"=> "success", 
                                "message"=> "Added Successfully!!" 
                            )
                        );
                    }
                }
            }
        }
    }
    
    $db->close();
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>