<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['fromDate']) && isset($_POST['toDate']) && isset($_POST['transactionStatus']) && isset($_POST['weightType']) && isset($_POST['customerSupplier']) && isset($_POST['invoiceNo'])){
    $fromDate = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
    $fromDateTime = $fromDate->format('Y-m-d 00:00:00');
    $toDate = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
    $toDateTime = $toDate->format('Y-m-d 23:59:59');
    $transactionStatus = $_POST['transactionStatus'];
    $weightType = $_POST['weightType'];
    $customerSupplier = $_POST['customerSupplier'];
    $invoiceNo = mysqli_real_escape_string($db, $_POST['invoiceNo']);
    $weighingData = array();
    $message = array();
    $totalNettWeight = 0;

    if ($transactionStatus == 'Sales' || $transactionStatus == 'Local'){
        $sql = "SELECT * FROM Weight WHERE transaction_date >= ?  AND transaction_date <= ? AND transaction_status = ? AND weight_type = ? AND customer_name = ? AND  invoice_no = ? AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'";
    }else{
        $sql = "SELECT * FROM Weight WHERE transaction_date >= ?  AND transaction_date <= ? AND transaction_status = ? AND weight_type = ? AND supplier_name = ? AND  invoice_no = ? AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'";
    }

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ssssss', $fromDateTime, $toDateTime, $transactionStatus, $weightType, $customerSupplier, $invoiceNo);
        
        // Execute the prepared query.
        if (! $stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $weighingData[] = array(
                    "id" => $row['id'],
                    "transaction_id" => $row['transaction_id'],
                    "cust_supp_code" => ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local') ? $row['customer_code'] : $row['supplier_code'],
                    "cust_supp_name" => ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local') ? $row['customer_name'] : $row['supplier_name'],
                    "weight_type"=> $row['weight_type'],
                    "lorry_plate_no1" => $row['lorry_plate_no1'],
                    "gross_weight1" => $row['gross_weight1'],
                    "gross_weight1_date"=> $row['gross_weight1_date'],
                    "tare_weight1" => $row['tare_weight1'],
                    "tare_weight1_date"=> $row['tare_weight1_date'],
                    "nett_weight1" => $row['nett_weight1'],
                    "unit_price" => $row['unit_price'],
                    "sub_total" => $row['sub_total'],
                    "sst" => $row['sst'],
                    "total_price" => $row['total_price'],
                    "invoice_no" => $row['invoice_no'],
                );
        
                $totalNettWeight += $row['nett_weight1']/1000;
            }

            $message['weights'] = $weighingData;

            // Get payment voucher details
            $paymentVoucherData = array();
            if ($payment_check_stmt = $db->prepare("SELECT * FROM Payment_Voucher WHERE customer_supplier=? AND invoice_no=? AND deleted=0")) {
                $payment_check_stmt->bind_param('ss', $customerSupplier, $invoiceNo);
                $payment_check_stmt->execute();
                $payment_check_result = $payment_check_stmt->get_result();
                $paymentVoucherData = $payment_check_result->fetch_assoc();
                $payment_check_stmt->close();
            }

            $message['totalNettWeight'] = $totalNettWeight;
            $message['paymentVoucher'] = $paymentVoucherData;

            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));  
        }
        $stmt->close();
    }

    $db->close();
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>