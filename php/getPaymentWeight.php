<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(isset($_POST['transactionStatus']) && isset($_POST['weightType']) && isset($_POST['customerSupplier']) && isset($_POST['transactionDate'])){
    $transactionStatus = $_POST['transactionStatus'];
    $weightType = $_POST['weightType'];
    $customerSupplier = $_POST['customerSupplier'];
    $transactionDate = DateTime::createFromFormat('d-m-Y', $_POST['transactionDate']);
    $transactionDateFormatted = $transactionDate->format('Y-m-d');
    $weighingData = array();
    $message = array();
    $totalNettWeight = 0;

    if ($transactionStatus == 'Sales' || $transactionStatus == 'Local'){
        $sql = "SELECT * FROM Weight WHERE transaction_status = ? AND weight_type = ? AND customer_name = ? AND DATE(transaction_date) = ? AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'";
    }else{
        $sql = "SELECT * FROM Weight WHERE transaction_status = ? AND weight_type = ? AND supplier_name = ? AND DATE(transaction_date) = ? AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'";
    }

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ssss', $transactionStatus, $weightType, $customerSupplier, $transactionDateFormatted);
        
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
            if ($payment_check_stmt = $db->prepare("SELECT * FROM Payment_Voucher WHERE customer_supplier=? AND DATE(voucher_date)=? AND deleted=0")) {
                $payment_check_stmt->bind_param('ss', $customerSupplier, $transactionDateFormatted);
                $payment_check_stmt->execute();
                $payment_check_result = $payment_check_stmt->get_result();
                $paymentVoucherData = $payment_check_result->fetch_assoc();
                $payment_check_stmt->close();
            }

            $message['totalNettWeight'] = $totalNettWeight;
            $message['paymentVoucher'] = $paymentVoucherData;
            $message['cust_supp_detail'] = ($transactionStatus == 'Sales' || $transactionStatus == 'Local') ? searchCustomerDetailByName($customerSupplier, $db) : searchSupplierDetailByName($customerSupplier, $db);

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