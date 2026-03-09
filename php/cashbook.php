<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/function.php';
require_once 'requires/lookup.php';

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
    $pvSelect = isset($_POST['pvSelect']) ? $_POST['pvSelect']: [];

    $deductionRecords = [];
    $deductionFfbStorage = 0;
    if(isset($deductionNo) && $deductionNo != null && count($deductionNo) > 0){ 
        foreach ($deductionNo as $key => $value) {
            if (!empty($value) && isset($deductionType[$key]) && isset($deductionDesc[$key]) && isset($deductionAmt[$key])) {
                if($deductionType[$key] == 'FFBSHORTAGE') {
                    $deductionFfbStorage += floatval($deductionAmt[$key]);
                }

                $record = [
                    "no" => $value,
                    "type" => $deductionType[$key],
                    "desc" => $deductionDesc[$key],
                    "amount" => $deductionAmt[$key]
                ];
                
                // Add pv_id if type is CREDITFFBPAY
                if($deductionType[$key] == 'CREDITFFBPAY' && isset($pvSelect[$key]) && !empty($pvSelect[$key])) {
                    $record['pv_id'] = $pvSelect[$key];
                }
                
                $deductionRecords[] = $record;
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
    
    // Query Previous Record to get accumulated values
    $weighingRecords = [];
    $accumDeduction = [];
    $accumAddition = [];
    $accumPurchase = [];
    $accumSales = [];
    $prevValues = [];

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
    }

    // Check if first record of the month exists
    $firstOfMonth = $dateObj->format('Y-m-01');
    $isFirstRecordOfMonth = false;
    
    if ($check_stmt = $db->prepare("SELECT id FROM Cash_Book WHERE DATE(date) >= ? AND DATE(date) < ? AND deleted = 0 LIMIT 1")) {
        $check_stmt->bind_param('ss', $firstOfMonth, $dateOnly);
        if ($check_stmt->execute()) {
            $check_result = $check_stmt->get_result();
            $isFirstRecordOfMonth = ($check_result->num_rows == 0);
        }
        $check_stmt->close();
    }

    if ($isFirstRecordOfMonth) {
        // Start of month - use current values as base
        $accumDeduction = $deductionRecords;
        $accumAddition = $additionRecords;

        // Group by transaction status
        $weighingRecords = groupByTransactionStatus($weighingRecords);
        $dailyPurchase = calculateFFBPurchase($weighingRecords, $db);
        $dailySales = calculateFFBSales($weighingRecords, $db);

        // Accumulate purchase
        $accumPurchase = [
            'accumCashFfbPurchase' => floatval($dailyPurchase['totalCashPrice']),
            'accumCashWeight' => $dailyPurchase['totalCashWeight'],
            'accumTermWeight' => $dailyPurchase['totalTermWeight'],
            'accumFfbIn' => floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight']),
            'accumFfbInRejected' => floatval($dailyPurchase['totalRejectedWeight']),
            'accumFfbOut' => floatval($dailySales['totalSalesWeight'])
        ];

        // Balance C/F = (FFB purchase cash weight[MT] + FFB purchase term weight[MT]) - FFB purchase rejected weight - FFB sales weight[MT] - FFB shortage weight[MT]
        $balanceCf = (floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight'])) - floatval($dailyPurchase['totalRejectedWeight']) - floatval($dailySales['totalSalesWeight'] - $deductionFfbStorage);

        // Previous Day Values
        $prevValues = [
            'ffbBalance' => floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight']) - floatval($dailyPurchase['totalRejectedWeight']) - floatval($dailySales['totalSalesWeight']),
            'balanceCf' => $balanceCf
        ];
    } else {
        // Get previous record
        $sql = "SELECT accum_deduction, accum_addition, accum_purchase, accum_sales, prev_values FROM Cash_Book WHERE DATE(date) < ? AND deleted = 0 ORDER BY date DESC LIMIT 1";
        if ($select_stmt = $db->prepare($sql)) {
            $dateOnly = $dateObj->format('Y-m-d');
            $select_stmt->bind_param('s', $dateOnly);
            if ($select_stmt->execute()) {
                $result = $select_stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $prevDeduction = json_decode($row['accum_deduction'], true) ?: [];
                    $prevAddition = json_decode($row['accum_addition'], true) ?: [];
                    $prevPurchase = json_decode($row['accum_purchase'], true) ?: [];
                    $prevSales = json_decode($row['accum_sales'], true) ?: [];
                    $prevWDValues = json_decode($row['prev_values'], true) ?: []; //$previous working day values
                    
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

                    // Group by transaction status
                    $weighingRecords = groupByTransactionStatus($weighingRecords);
                    $dailyPurchase = calculateFFBPurchase($weighingRecords, $db);
                    $dailySales = calculateFFBSales($weighingRecords, $db);

                    // Accumulate purchase
                    $accumPurchase = [
                        'accumCashFfbPurchase' => floatval($dailyPurchase['totalCashPrice']) + (isset($prevPurchase['accumCashFfbPurchase']) ? floatval($prevPurchase['accumCashFfbPurchase']) : 0),
                        'accumCashWeight' => floatval($dailyPurchase['totalCashWeight']) + (isset($prevPurchase['accumCashWeight']) ? floatval($prevPurchase['accumCashWeight']) : 0),
                        'accumTermWeight' => floatval($dailyPurchase['totalTermWeight']) + (isset($prevPurchase['accumTermWeight']) ? floatval($prevPurchase['accumTermWeight']) : 0),
                        'accumFfbIn' => floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight']) + (isset($prevPurchase['accumFfbIn']) ? floatval($prevPurchase['accumFfbIn']) : 0),
                        'accumFfbInRejected' => floatval($dailyPurchase['totalRejectedWeight']) + (isset($prevPurchase['accumFfbInRejected']) ? floatval($prevPurchase['accumFfbInRejected']) : 0),
                        'accumFfbOut' => floatval($dailySales['totalSalesWeight']) + (isset($prevPurchase['accumFfbOut']) ? floatval($prevPurchase['accumFfbOut']) : 0)
                    ];

                    // Balance C/F = Prev FFB Bal[MT] + (FFB purchase cash weight[MT] + FFB purchase term weight[MT]) - FFB purchase rejected weight - FFB sales weight[MT] - FFB shortage weight[MT]
                    $balanceCf = (floatval($prevWDValues['balanceCf']) + floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight'])) - floatval($dailyPurchase['totalRejectedWeight']) - floatval($dailySales['totalSalesWeight'] - $deductionFfbStorage);

                    // Previous Day Values
                    $prevValues = [
                        'ffbBalance' => floatval($dailyPurchase['totalCashWeight']) + floatval($dailyPurchase['totalTermWeight']) - floatval($dailyPurchase['totalRejectedWeight']) - floatval($dailySales['totalSalesWeight']),
                        'balanceCf' => $balanceCf
                    ];

                } else {
                    // No previous record - use current values
                    $accumDeduction = $deductionRecords;
                    $accumAddition = $additionRecords;
                }
            }
            $select_stmt->close();
        }
    }

    ######## Calculate Payment Voucher deduction amount for FFB payment ############
    $creditFfbPay = 0;
    $outstandingDetails = [];
    $processedPVs = []; // Track which PVs we've already processed
    $currentCashbookAmounts = []; // Track total amounts per PV for current cashbook
    $currentCashbookDescs = []; // Track descriptions per PV for current cashbook
    $currentCashbookDate = $date; // Track cashbook date
    
    // First, calculate total amount per PV for current cashbook
    foreach($deductionRecords as $record) {
        if($record['type'] == 'CREDITFFBPAY') {
            $paymentVoucherId = isset($record['pv_id']) ? $record['pv_id'] : $record['desc'];
            $creditFfbPay += floatval($record['amount']);
            
            if(!isset($currentCashbookAmounts[$paymentVoucherId])) {
                $currentCashbookAmounts[$paymentVoucherId] = 0;
            }
            $currentCashbookAmounts[$paymentVoucherId] += floatval($record['amount']);
            
            // Store description
            if(!isset($currentCashbookDescs[$paymentVoucherId])) {
                $currentCashbookDescs[$paymentVoucherId] = isset($record['desc']) ? $record['desc'] : '';
            }
        }
    }
    
    // Then, update outstanding details
    foreach($currentCashbookAmounts as $paymentVoucherId => $totalAmount) {
        if ($pv_stmt = $db->prepare("SELECT final_amount, outstanding_details FROM Payment_Voucher WHERE id=?")) {
            $pv_stmt->bind_param('i', $paymentVoucherId);
            if ($pv_stmt->execute()) {
                $pv_result = $pv_stmt->get_result();
                if ($pv_row = $pv_result->fetch_assoc()) {
                    $pvOutstanding = [];
                    if(!empty($pv_row['outstanding_details'])) {
                        $pvOutstanding = json_decode($pv_row['outstanding_details'], true) ?: [];
                    }
                    
                    // Find and replace matching cashbook_no or add new
                    $found = false;
                    foreach($pvOutstanding as $key => $item) {
                        if($item['cashbook_no'] == $cashBookNo) {
                            $pvOutstanding[$key]['amount'] = $totalAmount;
                            $pvOutstanding[$key]['cashbook_date'] = $currentCashbookDate;
                            $pvOutstanding[$key]['desc'] = isset($currentCashbookDescs[$paymentVoucherId]) ? $currentCashbookDescs[$paymentVoucherId] : '';
                            $found = true;
                            break;
                        }
                    }
                    
                    if(!$found) {
                        $pvOutstanding[] = [
                            'cashbook_no' => $cashBookNo,
                            'cashbook_date' => $currentCashbookDate,
                            'pv_id' => intval($paymentVoucherId),
                            'desc' => isset($currentCashbookDescs[$paymentVoucherId]) ? $currentCashbookDescs[$paymentVoucherId] : '',
                            'amount' => $totalAmount
                        ];
                    }
                    
                    $outstandingDetails[intval($paymentVoucherId)] = [
                        'final_amount' => floatval($pv_row['final_amount']),
                        'details' => $pvOutstanding
                    ];
                }
            }
            $pv_stmt->close();
        }
    } //echo json_encode($outstandingDetails);exit;

    if(!empty($cashbookId))
    {
        if ($update_stmt = $db->prepare("UPDATE Cash_Book SET cash_book_no=?, date=?, deduction_details=?, addition_details=?, total_deduction=?, total_addition=?, accum_deduction=?, accum_addition=?, accum_purchase=?, prev_values=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $accumDeductionJson = json_encode($accumDeduction);
            $accumAdditionJson = json_encode($accumAddition);
            $accumPurchaseJson = json_encode($accumPurchase);
            $prevValuesJson = json_encode($prevValues);
            $outstandingJson = json_encode($outstandingDetails); 
            $update_stmt->bind_param('sssssssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $accumDeductionJson, $accumAdditionJson, $accumPurchaseJson, $prevValuesJson, $username, $username, $cashbookId);
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

                // Update payment voucher outstanding details
                foreach($outstandingDetails as $pvId => $pvData) {
                    $finalAmount = $pvData['final_amount'];
                    $details = $pvData['details'];
                    $outstandingAmount = $finalAmount;
                    
                    foreach($details as $item) {
                        $outstandingAmount -= floatval($item['amount']);
                    }

                    if ($pv_update_stmt = $db->prepare("UPDATE Payment_Voucher SET outstanding_details=?, outstanding_amount=? WHERE id=?")) {
                        $outstandingDetailsJson = json_encode($details);
                        $pv_update_stmt->bind_param('sdi', $outstandingDetailsJson, $outstandingAmount, $pvId);
                        if (! $pv_update_stmt->execute()) {
                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> "Failed to update payment voucher outstanding details for PV ID: " . $pvId
                                )
                            );
                        }
                        $pv_update_stmt->close();
                    }
                }

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
        if ($insert_stmt = $db->prepare("INSERT INTO Cash_Book (cash_book_no, date, deduction_details, addition_details, total_deduction, total_addition, accum_deduction, accum_addition, accum_purchase, prev_values, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $deductionJson = json_encode($deductionRecords);
            $additionJson = json_encode($additionRecords);
            $accumDeductionJson = json_encode($accumDeduction);
            $accumAdditionJson = json_encode($accumAddition);
            $accumPurchaseJson = json_encode($accumPurchase);
            $prevValuesJson = json_encode($prevValues);
            $outstandingJson = json_encode($outstandingDetails);
            $insert_stmt->bind_param('ssssssssssss', $cashBookNo, $date, $deductionJson, $additionJson, $totalDeduction, $totalAddition, $accumDeductionJson, $accumAdditionJson, $accumPurchaseJson, $prevValuesJson, $username, $username);

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

                        // Update payment voucher outstanding details
                        foreach($outstandingDetails as $pvId => $pvData) {
                            $finalAmount = $pvData['final_amount'];
                            $details = $pvData['details'];
                            $outstandingAmount = $finalAmount;
                            
                            foreach($details as $item) {
                                $outstandingAmount -= floatval($item['amount']);
                            }

                            if ($pv_update_stmt = $db->prepare("UPDATE Payment_Voucher SET outstanding_details=?, outstanding_amount=? WHERE id=?")) {
                                $outstandingDetailsJson = json_encode($details);
                                $pv_update_stmt->bind_param('sdi', $outstandingDetailsJson, $outstandingAmount, $pvId);
                                if (! $pv_update_stmt->execute()) {
                                    echo json_encode(
                                        array(
                                            "status"=> "failed", 
                                            "message"=> "Failed to update payment voucher outstanding details for PV ID: " . $pvId
                                        )
                                    );
                                }
                                $pv_update_stmt->close();
                            }
                        }

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