<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['supplierCode'])) {

    if (empty($_POST["id"])) {
        $supplierId = null;
    } else {
        $supplierId = trim($_POST["id"]);
    }

    if (empty($_POST["supplierCode"])) {
        $supplierCode = null;
    } else {
        $supplierCode = trim($_POST["supplierCode"]);
    }

    if (empty($_POST["companyRegNo"])) {
        $companyRegNo = null;
    } else {
        $companyRegNo = trim($_POST["companyRegNo"]);
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
    
    if(! empty($supplierId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Supplier SET supplier_code=?, company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, address_line_4=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssssss', $supplierCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $phoneNo, $faxNo, $username, $username, $supplierId);

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
                if ($insert_stmt = $db->prepare("INSERT INTO Supplier_Log (supplier_id, supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssss', $supplierId, $supplierCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $phoneNo, $faxNo, $action, $username);
        
                    // Execute the prepared query.
                    if (! $insert_stmt->execute()) {}
                    else{
                        $insert_stmt->close();
                    }

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
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Supplier (supplier_code , company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $supplierCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $phoneNo, $faxNo, $username, $username);

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

                $sel = mysqli_query($db,"select count(*) as allcount from Supplier");
                $records = mysqli_fetch_assoc($sel);
                $totalRecords = $records['allcount'];

                if ($insert_log = $db->prepare("INSERT INTO Supplier_Log (supplier_id, supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssssssss', $totalRecords, $supplierCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $phoneNo, $faxNo, $action, $username);
        
                    // Execute the prepared query.
                    if (! $insert_log->execute()) {}
                    else{
                        $insert_log->close();
                    }
                }

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