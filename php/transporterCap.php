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
if (isset($_POST['transportFit'])) {

    if (empty($_POST["id"])) {
        $transporterId = null;
    } else {
        $transporterId = trim($_POST["id"]);
    }

    if (empty($_POST["transportFit"])) {
        $transportFit = null;
    } else {
        $transportFit = trim($_POST["transportFit"]);
    }

    if (empty($_POST["transportLoad"])) {
        $transportLoad = null;
    } else {
        $transportLoad = trim($_POST["transportLoad"]);
    }

    
    if(! empty($transporterId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Transport_Cap SET transport_fit=?, transport_load=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssss', $transportFit, $transportLoad, $username, $transporterId);

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
        if ($insert_stmt = $db->prepare("INSERT INTO Transport_Cap (transport_fit, transport_load, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $transportFit, $transportLoad, $username, $username);

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

                // $sel = mysqli_query($db,"select count(*) as allcount from Transporter");
                // $records = mysqli_fetch_assoc($sel);
                // $totalRecords = $records['allcount'];

                // if ($insert_log = $db->prepare("INSERT INTO Transporter_Log (transporter_id, transporter_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                //     $insert_log->bind_param('sssssssssssssss', $totalRecords, $transporterCode, $companyRegNo, $newRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $contactName, $icNo, $tinNo, $action, $username);
        
                //     // Execute the prepared query.
                //     if (! $insert_log->execute()) {
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "failed", 
                //         //         "message"=> $insert_stmt->error
                //         //     )
                //         // );
                //     }
                //     else{
                //         $insert_log->close();
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "success", 
                //         //         "message"=> "Added Successfully!!" 
                //         //     )
                //         // );
                //     }
                // }

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