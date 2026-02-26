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
if (isset($_POST['date'], $_POST['employee'], $_POST['paySlipNo'])) {

    if (empty($_POST["id"])) {
        $payslipId = null;
    } else {
        $payslipId = trim($_POST["id"]);
    }

    if (empty($_POST["date"])) {
        $date = null;
    } else {
        $date = DateTime::createFromFormat('d-m-Y', $_POST["date"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["employee"])) {
        $employee = null;
    } else {
        $employee = trim($_POST["employee"]);
    }

    if (empty($_POST["paySlipNo"])) {
        $name = 'payslip';
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
                $paySlipNo = 'PS';
                $result2 = $update_stmt2->get_result();
				if ($row2 = $result2->fetch_assoc()) {
                    $charSize = strlen($row2['value']);
                    $value = $row2['value'];
                    for($i=0; $i<(4-(int)$charSize); $i++){
                        $paySlipNo.='0';  // PS0000
                    }
                    $paySlipNo .= $value;
				} 
            }
		}
    } 
    else {
        $paySlipNo = trim($_POST["paySlipNo"]);
    }

    if (empty($_POST["paymentType"])) {
        $paymentType = null;
    } else {
        $paymentType = trim($_POST["paymentType"]);
    }

    if (empty($_POST["chequeNo"])) {
        $chequeNo = null;
    } else {
        $chequeNo = trim($_POST["chequeNo"]);
    }

    if (empty($_POST["cashBook"])) {
        $cashBook = null;
    } else {
        $cashBook = trim($_POST["cashBook"]);
    }

    // Earnings Details Build
    $earningsNo = isset($_POST['earningsNo']) ? $_POST['earningsNo']: [];
    $earningsType = isset($_POST['earningsType']) ? $_POST['earningsType']: [];
    $earningsDesc = isset($_POST['earningsDesc']) ? $_POST['earningsDesc']: [];
    $earningsAmt = isset($_POST['earningsAmt']) ? $_POST['earningsAmt']: [];
    
    $earningsDetails = [];
    if(isset($earningsNo) && $earningsNo != null && count($earningsNo) > 0){ 
        foreach ($earningsNo as $key => $value) {
            if (!empty($value)) {
                $earningsDetails[] = [
                    "no" => $value,
                    "type" => $earningsType[$key],
                    "desc" => $earningsDesc[$key],
                    "amt" => $earningsAmt[$key]
                ];
            }
        }
    }

    $earningsJson = !empty($earningsDetails) ? json_encode($earningsDetails) : null;
	// End of Earnings Details Build

    if (empty($_POST["grossPay"])) {
        $grossPay = null;
    } else {
        $grossPay = trim($_POST["grossPay"]);
    }

    // Deduction Details Build
    $deductionNo = isset($_POST['deductionNo']) ? $_POST['deductionNo']: [];
    $deductionType = isset($_POST['deductionType']) ? $_POST['deductionType']: [];
    $deductionDesc = isset($_POST['deductionDesc']) ? $_POST['deductionDesc']: [];
    $deductionAmt = isset($_POST['deductionAmt']) ? $_POST['deductionAmt']: [];
    
    $deductionDetails = [];
    if(isset($deductionNo) && $deductionNo != null && count($deductionNo) > 0){ 
        foreach ($deductionNo as $key => $value) {
            if (!empty($value)) {
                $deductionDetails[] = [
                    "no" => $value,
                    "type" => $deductionType[$key],
                    "desc" => $deductionDesc[$key],
                    "amt" => $deductionAmt[$key]
                ];
            }
        }
    }

    $deductionJson = !empty($deductionDetails) ? json_encode($deductionDetails) : null;
	// End of Deduction Details Build

    if (empty($_POST["totalDeduction"])) {
        $totalDeduction = null;
    } else {
        $totalDeduction = trim($_POST["totalDeduction"]);
    }

    if (empty($_POST["netPay"])) {
        $netPay = null;
    } else {
        $netPay = trim($_POST["netPay"]);
    }
    
    if(! empty($payslipId))
    {
        if ($update_stmt = $db->prepare("UPDATE Payslip SET payslip_no=?, user_id=?, date=?, payment_type=?, cheque_no=?, cashbook_id=?, earnings_detail=?, gross_pay=?, deductions_detail=?, total_deductions=?, net_pay=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssssssss', $paySlipNo, $employee, $date, $paymentType, $chequeNo, $cashBook, $earningsJson, $grossPay, $deductionJson, $totalDeduction, $netPay, $username, $payslipId);

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
                $db->close();

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
        if ($insert_stmt = $db->prepare("INSERT INTO Payslip (payslip_no, user_id, date, payment_type, cheque_no, cashbook_id, earnings_detail, gross_pay, deductions_detail, total_deductions, net_pay, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssss', $paySlipNo, $employee, $date, $paymentType, $chequeNo, $cashBook, $earningsJson, $grossPay, $deductionJson, $totalDeduction, $netPay, $username, $username);

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
                $name = 'payslip';
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