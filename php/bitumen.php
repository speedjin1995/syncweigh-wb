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
$phoneNo = $_SESSION['plant'];
$faxNo = date("Y-m-d H:i:s");

// Processing form data when form is submitted
if (empty($_POST["id"])) {
    $transporterId = null;
} else {
    $transporterId = trim($_POST["id"]);
}

if (empty($_POST["rawMatCode"])) {
    $transporterCode = null;
} else {
    $transporterCode = trim($_POST["rawMatCode"]);
}

if (empty($_POST["rawMatName"])) {
    $companyName = null;
} else {
    $companyName = trim($_POST["rawMatName"]);
}

if (empty($_POST["weight"])) {
    $addressLine1 = null;
} else {
    $addressLine1 = trim($_POST["weight"]);
}

if (empty($_POST["drum"])) {
    $addressLine2 = null;
} else {
    $addressLine2 = trim($_POST["drum"]);
}

if (empty($_POST["diesel"])) {
    $addressLine3 = null;
} else {
    $addressLine3 = trim($_POST["diesel"]);
}

if(! empty($transporterId)){
    if ($update_stmt = $db->prepare("UPDATE Bitumen SET `60/70`=?, pg76=?, crmb=?, lfo=?, diesel=?, plant_code=? WHERE id=?")) {
        $update_stmt->bind_param('sssssss', $transporterCode, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $transporterId);

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

            /*if ($insert_stmt = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssssss', $transporterId, $transporterCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $action, $username);
    
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    // echo json_encode(
                    //     array(
                    //         "status"=> "failed", 
                    //         "message"=> $insert_stmt->error
                    //     )
                    // );
                }
                else{
                    $insert_stmt->close();
                    
                    // echo json_encode(
                    //     array(
                    //         "status"=> "success", 
                    //         "message"=> "Added Successfully!!" 
                    //     )
                    // );
                }

                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }*/
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
    if ($insert_stmt = $db->prepare("INSERT INTO Bitumen (`60/70`, pg76, crmb, lfo, diesel, plant_code, created_datetime) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('sssssss', $transporterCode, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo);

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

            /*$sel = mysqli_query($db,"select count(*) as allcount from Transporter");
            $records = mysqli_fetch_assoc($sel);
            $totalRecords = $records['allcount'];

            if ($insert_log = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_log->bind_param('sssssssssss', $totalRecords, $transporterCode, $companyRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $action, $username);
    
                // Execute the prepared query.
                if (! $insert_log->execute()) {
                    // echo json_encode(
                    //     array(
                    //         "status"=> "failed", 
                    //         "message"=> $insert_stmt->error
                    //     )
                    // );
                }
                else{
                    $insert_log->close();
                    // echo json_encode(
                    //     array(
                    //         "status"=> "success", 
                    //         "message"=> "Added Successfully!!" 
                    //     )
                    // );
                }
            }*/

            $insert_stmt->close();
            $db->close();
            
        }
    }
}
?>