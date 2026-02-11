<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/function.php';

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

    // Query Previous Record to get accumulated values
    $accumDeduction = [];
    $accumAddition = [];
    
    $dateObj = new DateTime($date);
    $dateOnly = $dateObj->format('Y-m-d');
    
    // Check for duplicate date
    if (empty($cashbookId)) {
        $checkSql = "SELECT id FROM Cash_Book WHERE DATE(date) = ? AND deleted = 0";
        if ($check_stmt = $db->prepare($checkSql)) {
            $check_stmt->bind_param('s', $dateOnly);
            if ($check_stmt->execute()) {
                $check_result = $check_stmt->get_result();
                if ($check_result->num_rows > 0) {
                    echo json_encode([
                        "status" => "failed",
                        "message" => "Cash book entry for this date already exists"
                    ]);
                    $check_stmt->close();
                    $db->close();
                    exit;
                }
            }
            $check_stmt->close();
        }
    }
    
    $weighingRecords = [];
    if ($dateObj->format('d') == '01') {
        // Start of month - use current values as base
        $accumDeduction = $deductionRecords;
        $accumAddition = $additionRecords;

        // Query today weighing records
        if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE DATE(transaction_date)=? AND is_complete='Y' AND is_cancel <> 'Y' AND status=0")) {
            $dateOnly = $dateObj->format('Y-m-d');
            $select_stmt->bind_param('s', $dateOnly);
            if ($select_stmt->execute()) {
                $result = $select_stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $weighingRecords[] = $row;
                }
            }
            $select_stmt->close();

            // Group by transaction status
            $weighingRecords = groupByTransactionStatus($weighingRecords);
            $dailyPurchase = calculateFFBPurchase($weighingRecords);
            $dailySales = calculateFFBSales($weighingRecords);

        }
    } else {
        // Get previous record
        $sql = "SELECT accum_deduction, accum_addition, accum_purchase, accum_sales FROM Cash_Book WHERE DATE(date) < ? AND deleted = 0 ORDER BY date DESC LIMIT 1";
        if ($select_stmt = $db->prepare($sql)) {
            $dateOnly = $dateObj->format('Y-m-d');
            $select_stmt->bind_param('s', $dateOnly);
            if ($select_stmt->execute()) {
                $result = $select_stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $prevDeduction = json_decode($row['accum_deduction'], true) ?: [];
                    $prevAddition = json_decode($row['accum_addition'], true) ?: [];
                    
                    // Build lookup arrays by type
                    $prevDeductionMap = [];
                    foreach ($prevDeduction as $item) {
                        $prevDeductionMap[$item['type']] = floatval($item['amount']);
                    }
                    
                    $prevAdditionMap = [];
                    foreach ($prevAddition as $item) {
                        $prevAdditionMap[$item['type']] = floatval($item['amount']);
                    }
                    
                    // Accumulate deductions
                    foreach ($deductionRecords as $record) {
                        $prevAmount = isset($prevDeductionMap[$record['type']]) ? $prevDeductionMap[$record['type']] : 0;
                        $accumDeduction[] = [
                            'no' => $record['no'],
                            'type' => $record['type'],
                            'desc' => $record['desc'],
                            'amount' => $prevAmount + floatval($record['amount'])
                        ];
                    }
                    
                    // Accumulate additions
                    foreach ($additionRecords as $record) {
                        $prevAmount = isset($prevAdditionMap[$record['type']]) ? $prevAdditionMap[$record['type']] : 0;
                        $accumAddition[] = [
                            'no' => $record['no'],
                            'type' => $record['type'],
                            'desc' => $record['desc'],
                            'amount' => $prevAmount + floatval($record['amount'])
                        ];
                    }
                } else {
                    // No previous record - use current values
                    $accumDeduction = $deductionRecords;
                    $accumAddition = $additionRecords;
                }
            }
            $select_stmt->close();
        }
    }

    if(!empty($cashbookId))
    {
        if ($update_stmt = $db->prepare("UPDATE Cash_Book SET cash_book_no=?, date=?, deduction_details=?, addition_details=?, total_deduction=?, total_addition=?, accum_deduction=?, accum_addition=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $accumDeductionJson = json_encode($accumDeduction);
            $accumAdditionJson = json_encode($accumAddition);
            $update_stmt->bind_param('sssssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $accumDeductionJson, $accumAdditionJson, $username, $username, $cashbookId);
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
        if ($insert_stmt = $db->prepare("INSERT INTO Cash_Book (cash_book_no, date, deduction_details, addition_details, total_deduction, total_addition, accum_deduction, accum_addition, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $accumDeductionJson = json_encode($accumDeduction);
            $accumAdditionJson = json_encode($accumAddition);
            $insert_stmt->bind_param('ssssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $accumDeductionJson, $accumAdditionJson, $username, $username);

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