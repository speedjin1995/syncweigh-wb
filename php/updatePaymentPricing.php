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
if (isset($_POST['id'], $_POST['customerSupplier'], $_POST['invoiceNo']) && !empty($_POST['id']) && !empty($_POST['customerSupplier']) && !empty($_POST['invoiceNo'])) {
    $ids = $_POST['id'];

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
    $deductionDesc = $_POST['deduction_desc'];
    $deductionAmount = $_POST['deduction_amount'];
    $additionDesc = $_POST['addition_desc'];
    $additionAmount = $_POST['addition_amount'];
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
        // Checking to see if there are existing payment voucher record
        if ($payment_check_stmt = $db->prepare("SELECT id FROM Payment_Voucher WHERE customer_supplier=? AND invoice_no=? AND deleted=0")) {
            $payment_check_stmt->bind_param('ss', $_POST['customerSupplier'], $_POST['invoiceNo']);
            $payment_check_stmt->execute();
            $payment_check_result = $payment_check_stmt->get_result();
            
            if ($payment_check_result->num_rows > 0) {
                // Update existing record
                $payment_row = $payment_check_result->fetch_assoc();
                $payment_id = $payment_row['id'];
                $payment_check_stmt->close();
                
                if ($update_payment_stmt = $db->prepare("UPDATE Payment_Voucher SET unit_price=?, tax=?, total_nett_weight=?, total_amount=?, total_deductions=?, total_additions=?, final_amount=?, deduction_desc=?, deduction_amount=?, addition_desc=?, addition_amount=?, modified_by=? WHERE id=?")) {
                    $deduction_desc_json = json_encode($deductionDesc);
                    $deduction_amount_json = json_encode($deductionAmount);
                    $addition_desc_json = json_encode($additionDesc);
                    $addition_amount_json = json_encode($additionAmount);
                    
                    $update_payment_stmt->bind_param('ddddddssssssi', $unitPrice, $tax, $totalNettWeight, $subtotal, $totalDeductions, $totalAdditions, $finalAmount, $deduction_desc_json, $deduction_amount_json, $addition_desc_json, $addition_amount_json, $username, $payment_id);
                    $update_payment_stmt->execute();
                    $update_payment_stmt->close();
                }
            } else {
                // Insert new record
                $payment_check_stmt->close();
                
                if ($insert_payment_stmt = $db->prepare("INSERT INTO Payment_Voucher (customer_supplier, invoice_no, unit_price, tax, total_nett_weight, total_amount, total_deductions, total_additions, final_amount, deduction_desc, deduction_amount, addition_desc, addition_amount, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $deduction_desc_json = json_encode($deductionDesc);
                    $deduction_amount_json = json_encode($deductionAmount);
                    $addition_desc_json = json_encode($additionDesc);
                    $addition_amount_json = json_encode($additionAmount);
                    
                    $insert_payment_stmt->bind_param('ssdddddddsssss', $_POST['customerSupplier'], $_POST['invoiceNo'], $unitPrice, $tax, $totalNettWeight, $subtotal, $totalDeductions, $totalAdditions, $finalAmount, $deduction_desc_json, $deduction_amount_json, $addition_desc_json, $addition_amount_json, $username);
                    $insert_payment_stmt->execute();
                    $insert_payment_stmt->close();
                }
            }
        }

        echo json_encode(array(
            "status" => "success",
            "message" => "Pricing updated successfully!"
        ));
    } else {
        echo json_encode(array(
            "status" => "failed",
            "message" => "Failed to update pricing"
        ));
    }
    
    $db->close();
}
?>