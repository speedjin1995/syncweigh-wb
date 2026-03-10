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
if (isset($_POST['customerCode'])) {

    if (empty($_POST["id"])) {
        $customerId = null;
    } else {
        $customerId = trim($_POST["id"]);
    }

    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if (empty($_POST["companyRegNo"])) {
        $companyRegNo = null;
    } else {
        $companyRegNo = trim($_POST["companyRegNo"]);
    }

    if (empty($_POST["newRegNo"])) {
        $newRegNo = null;
    } else {
        $newRegNo = trim($_POST["newRegNo"]);
    }

    if (empty($_POST["companyName"])) {
        $companyName = null;
    } else {
        $companyName = trim($_POST["companyName"]);
    }

    if (empty($_POST["addressLine1"])) {
        $addressLine1 = null;
    } else {
        $addressLine1 = trim($_POST["addressLine1"]);
    }

    if (empty($_POST["addressLine2"])) {
        $addressLine2 = null;
    } else {
        $addressLine2 = trim($_POST["addressLine2"]);
    }

    if (empty($_POST["addressLine3"])) {
        $addressLine3 = null;
    } else {
        $addressLine3 = trim($_POST["addressLine3"]);
    }

    if (empty($_POST["addressLine4"])) {
        $addressLine4 = null;
    } else {
        $addressLine4 = trim($_POST["addressLine4"]);
    }

    if (empty($_POST["phoneNo"])) {
        $phoneNo = null;
    } else {
        $phoneNo = trim($_POST["phoneNo"]);
    }

    if (empty($_POST["faxNo"])) {
        $faxNo = null;
    } else {
        $faxNo = trim($_POST["faxNo"]);
    }

    if (empty($_POST["contactName"])) {
        $contactName = null;
    } else {
        $contactName = trim($_POST["contactName"]);
    }

    if (empty($_POST["icNo"])) {
        $icNo = null;
    } else {
        $icNo = trim($_POST["icNo"]);
    }

    if (empty($_POST["tinNo"])) {
        $tinNo = null;
    } else {
        $tinNo = trim($_POST["tinNo"]);
    }

    if (empty($_POST["mpob"])) {
        $mpob = null;
    } else {
        $mpob = trim($_POST["mpob"]);
    }

    if (empty($_POST["mspoNo"])) {
        $mspoNo = null;
    } else {
        $mspoNo = trim($_POST["mspoNo"]);
    }

    if (empty($_POST["paymentTerm"])) {
        $paymentTerm = null;
    } else {
        $paymentTerm = trim($_POST["paymentTerm"]);
    }

    if (empty($_POST["paymentBy"])) {
        $paymentBy = null;
    } else {
        $paymentBy = trim($_POST["paymentBy"]);
    }

    if (empty($_POST["harvestingPrice"])) {
        $harvestingPrice = null;
    } else {
        $harvestingPrice = trim($_POST["harvestingPrice"]);
    }

    if (empty($_POST["transportPrice"])) {
        $transportPrice = null;
    } else {
        $transportPrice = trim($_POST["transportPrice"]);
    }
    
    if(! empty($customerId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Customer SET customer_code=?, company_reg_no=?, new_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, contact_name=?, ic_no=?, tin_no=?, mpob=?, mspo_no=?, payment_term=?, payment_by=?, harvesting_price=?, transport_price=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssssssssssssssss', $customerCode, $companyRegNo, $newRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $contactName, $icNo, $tinNo, $mpob, $mspoNo, $paymentTerm, $paymentBy, $harvestingPrice, $transportPrice, $username, $username, $customerId);

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
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Customer (customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, payment_by, harvesting_price, transport_price, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssssssssssss', $customerCode, $companyRegNo, $newRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $contactName, $icNo, $tinNo, $mpob, $mspoNo, $paymentTerm, $paymentBy, $harvestingPrice, $transportPrice, $username, $username);

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
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );

                $insert_stmt->close();
                $db->close();
            }
        }
    }
    
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