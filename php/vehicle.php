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
if (isset($_POST['vehicleNo'])) {

    if (empty($_POST["id"])) {
        $vehicleId = null;
    } else {
        $vehicleId = trim($_POST["id"]);
    }

    if (empty($_POST["vehicleNo"])) {
        $vehicleNo = null;
    } else {
        $vehicleNo = trim($_POST["vehicleNo"]);
    }

    if (empty($_POST["vehicleWeight"])) {
        $vehicleWeight = 0;
    } else {
        $vehicleWeight = trim($_POST["vehicleWeight"]);
    }

    if (empty($_POST["exDel"])) {
        $exDel = null;
    } else {
        if ($_POST["exDel"] == 'true'){
            $exDel = 'EX';
        }else{
            $exDel = 'DEL';
        }
    }

    if (empty($_POST["transporter"])) {
        $transporter = null;
    } else {
        $transporter = trim($_POST["transporter"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["customer"])) {
        $customer = null;
    } else {
        $customer = trim($_POST["customer"]);
    }

    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if(! empty($vehicleId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Vehicle SET veh_number=?, vehicle_weight=?, transporter_code=?, transporter_name=?, ex_del=?, customer_code=?, customer_name=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssss', $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $username, $username, $vehicleId);

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
                if ($insert_stmt = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssss', $vehicleId, $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $action, $username);
        
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
                }
            }
        }
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Vehicle (veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssss', $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $username, $username);

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

                $vehicleId = $insert_stmt->insert_id;

                $sel = mysqli_query($db,"select count(*) as allcount from Vehicle");
                $records = mysqli_fetch_assoc($sel);
                $totalRecords = $records['allcount'];

                if ($insert_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssssss', $vehicleId, $vehicleNo, $vehicleWeight, $transporterCode, $transporter, $exDel, $customerCode, $customer, $action, $username);
        
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