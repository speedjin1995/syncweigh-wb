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
if (isset($_POST['unit'])) {

    if (empty($_POST["id"])) {
        $unitId = null;
    } else {
        $unitId = trim($_POST["id"]);
    }

    if (empty($_POST["unit"])) {
        $unit = null;
    } else {
        $unit = trim($_POST["unit"]);
    }
    
    if(! empty($unitId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Unit SET unit=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssss', $unit, $username, $username, $unitId);

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
                if ($insert_stmt = $db->prepare("INSERT INTO Unit_Log (unit_id, unit, action_id, action_by) VALUES (?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssss', $unitId, $unit, $action, $username);
        
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
        if ($insert_stmt = $db->prepare("INSERT INTO Unit (unit, created_by, modified_by) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $unit, $username, $username);

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

                $sel = mysqli_query($db,"select count(*) as allcount from Unit");
                $records = mysqli_fetch_assoc($sel);
                $totalRecords = $records['allcount'];

                if ($insert_log = $db->prepare("INSERT INTO Unit_Log (unit_id, unit, action_id, action_by) VALUES (?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssss', $totalRecords, $unit, $action, $username);
        
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