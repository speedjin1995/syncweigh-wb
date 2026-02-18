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
if (isset($_POST['id'], $_POST['customerSupplier'], $_POST['voucherDate']) && !empty($_POST['id']) && !empty($_POST['customerSupplier']) && !empty($_POST['voucherDate'])) {
    $ids = $_POST['id'];
    if (empty($_POST["voucherDate"])) {
        $voucherDate = null;
    } else {
        $voucherDate = DateTime::createFromFormat('d-m-Y', $_POST["voucherDate"])->format('Y-m-d H:i:s');
        $voucherDateOnly = DateTime::createFromFormat('d-m-Y', $_POST["voucherDate"])->format('Y-m-d');
    }

    if (empty($_POST["invoiceNo"])) {
        $invoiceNo = null;
    } else {
        $invoiceNo = trim($_POST["invoiceNo"]);
    }

    if (empty($_POST["voucherNo"])) {
        $name = 'payment_voucher';
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
                $voucherNo = 'PV';
                $result2 = $update_stmt2->get_result();
				if ($row2 = $result2->fetch_assoc()) {
                    $charSize = strlen($row2['value']);
                    $value = $row2['value'];
                    for($i=0; $i<(4-(int)$charSize); $i++){
                        $voucherNo.='0';  // PV0000
                    }
                    $voucherNo .= $value;
				} 
            }
		}
    } else {
        $voucherNo = trim($_POST["voucherNo"]);
    }

    if (empty($_POST["unitPrice"])) {
        $unitPrice = 0;
    } else {
        $unitPrice = trim($_POST["unitPrice"]);
    }

    if (empty($_POST["tax"])) {
        $tax = 0;
    } else {
        $tax = trim($_POST["tax"]);
    }

    if (empty($_POST["totalNettWeight"])) {
        $totalNettWeight = 0;
    } else {
        $totalNettWeight = trim($_POST["totalNettWeight"]);
    }

    if (empty($_POST["totalDeductions"])) {
        $totalDeductions = 0;
    } else {
        $totalDeductions = trim($_POST["totalDeductions"]);
    }

    if (empty($_POST["totalAdditions"])) {
        $totalAdditions = 0;
    } else {
        $totalAdditions = trim($_POST["totalAdditions"]);
    }

    if (empty($_POST["subtotal"])) {
        $subtotal = 0;
    } else {
        $subtotal = trim($_POST["subtotal"]);
    }

    if (empty($_POST["deductions"])) {
        $deductions = 0;
    } else {
        $deductions = trim($_POST["deductions"]);
    }

    if (empty($_POST["additions"])) {
        $additions = 0;
    } else {
        $additions = trim($_POST["additions"]);
    }

    if (empty($_POST["finalAmount"])) {
        $finalAmount = 0;
    } else {
        $finalAmount = trim($_POST["finalAmount"]);
    }

    // Weight Details
    $unitPrices = $_POST['unit_price'];
    $subTotals = $_POST['sub_total'];
    $ssts = $_POST['sst'];
    $totalPrices = $_POST['total_price'];

    // Deduction and Addition Calculation
    $deductionDesc = isset($_POST['deduction_desc']) ? $_POST['deduction_desc'] : [];
    $deductionAmount = isset($_POST['deduction_amount']) ? $_POST['deduction_amount'] : [];
    $additionDesc = isset($_POST['addition_desc']) ? $_POST['addition_desc'] : [];
    $additionAmount = isset($_POST['addition_amount']) ? $_POST['addition_amount'] : [];
    $success = true;
    
    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i];
        $unitPrice = $unitPrices[$i];
        $subTotal = $subTotals[$i];
        $sst = $ssts[$i];
        $totalPrice = $totalPrices[$i];
        
        if ($update_stmt = $db->prepare("UPDATE Weight SET unit_price=?, sub_total=?, sst=?, total_price=?, modified_by=? WHERE id=?")) {
            $update_stmt->bind_param('ssssss', $unitPrice, $subTotal, $sst, $totalPrice, $username, $id);
            
            if (!$update_stmt->execute()) {
                $success = false;
                break;
            }
            $update_stmt->close();
        } else {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        // Build JSON objects for each deduction record
        $deductionRecords = [];
        if (!empty($deductionDesc)){
            foreach ($deductionDesc as $key => $desc) {
                if (!empty($desc) && isset($deductionAmount[$key])) {
                    $deductionRecords[] = [
                        "deduction_desc" => $desc,
                        "deduction_amount" => $deductionAmount[$key]
                    ];
                }
            }
        }

        $additionRecords = [];
        if (!empty($additionDesc)){
            foreach ($additionDesc as $key => $desc) {
                if (!empty($desc) && isset($additionAmount[$key])) {
                    $additionRecords[] = [
                        "addition_desc" => $desc,
                        "addition_amount" => $additionAmount[$key]
                    ];
                }
            }
        }

        // Checking to see if there are existing payment voucher record
        if ($payment_check_stmt = $db->prepare("SELECT id FROM Payment_Voucher WHERE customer_supplier=? AND DATE(voucher_date)=? AND deleted=0")) {
            $payment_check_stmt->bind_param('ss', $_POST['customerSupplier'], $voucherDateOnly);
            $payment_check_stmt->execute();
            $payment_check_result = $payment_check_stmt->get_result();
            
            if ($payment_check_result->num_rows > 0) {
                // Update existing record
                $payment_row = $payment_check_result->fetch_assoc();
                $paymentId = $payment_row['id'];
                $payment_check_stmt->close();
                
                if ($update_payment_stmt = $db->prepare("UPDATE Payment_Voucher SET voucher_date=?, unit_price=?, tax=?, total_nett_weight=?, total_amount=?, deduction_amount=?, addition_amount=?, final_amount=?, deduction_details=?, addition_details=?, modified_by=? WHERE id=?")) {
                    $deductionsJson = json_encode($deductionRecords);
                    $additionJson = json_encode($additionRecords);
                    
                    $update_payment_stmt->bind_param('ssssssssssss', $voucherDate, $unitPrice, $tax, $totalNettWeight, $subtotal, $totalDeductions, $totalAdditions, $finalAmount, $deductionsJson, $additionJson, $username, $paymentId);
                    $update_payment_stmt->execute();
                    $update_payment_stmt->close();
                }
            } else {
                // Insert new record
                $payment_check_stmt->close();
                
                if ($insert_payment_stmt = $db->prepare("INSERT INTO Payment_Voucher (customer_supplier, voucher_no, invoice_no, voucher_date, unit_price, tax, total_nett_weight, total_amount, deduction_amount, addition_amount, final_amount, outstanding_amount, deduction_details, addition_details, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $deductionsJson = json_encode($deductionRecords);
                    $additionJson = json_encode($additionRecords);
                    
                    $insert_payment_stmt->bind_param('ssssssssssssssss', $_POST['customerSupplier'], $voucherNo, $invoiceNo, $voucherDate, $unitPrice, $tax, $totalNettWeight, $subtotal, $totalDeductions, $totalAdditions, $finalAmount, $finalAmount, $deductionsJson, $additionJson, $username, $username);

                    if (! $insert_payment_stmt->execute()) {
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $insert_payment_stmt->error
                            )
                        );
                    }
                    else{
                        $insert_payment_stmt->close();

                        // Update miscellaneous for voucher no
                        $name = 'payment_voucher';
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
        }
    } else {
        echo json_encode(array(
            "status" => "failed",
            "message" => "Failed to update pricing"
        ));
    }
    
    $db->close();
}
?>