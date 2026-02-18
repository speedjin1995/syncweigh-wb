<?php
session_start();
require_once "db_connect.php";

if ($update_stmt = $db->prepare("SELECT * FROM Payment_Voucher WHERE outstanding_amount > 0 AND deleted = '0' ORDER BY voucher_no ASC")) {        
    // Execute the prepared query.
    if (! $update_stmt->execute()) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
            )); 
    }
    else{
        $result = $update_stmt->get_result();
        $message = array();
        
        while ($row = $result->fetch_assoc()) {
            $message[] = array(
                "id" => $row["id"],
                "voucher_no" => $row["voucher_no"],
                "customer_supplier" => $row["customer_supplier"],
                "voucher_date" => $row["voucher_date"],
                "invoice_no" => $row["invoice_no"],
                "unit_price" => $row["unit_price"],
                "tax" => $row["tax"],
                "total_nett_weight" => $row["total_nett_weight"],
                "total_amount" => $row["total_amount"],
                "deduction_amount" => $row["deduction_amount"],
                "addition_amount" => $row["addition_amount"],
                "final_amount" => $row["final_amount"],
                "outstanding_amount" => $row["outstanding_amount"]
             );
        }
        
        echo json_encode(
            array(
                "status" => "success",
                "message" => $message
            ));   
    }
}
?>