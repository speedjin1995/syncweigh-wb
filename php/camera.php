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
if (isset($_POST['cameraName'])) {

    if (empty($_POST["id"])) {
        $cameraId = null;
    } else {
        $cameraId = trim($_POST["id"]);
    }

    if (empty($_POST["cameraName"])) {
        $cameraName = null;
    } else {
        $cameraName = trim($_POST["cameraName"]);
    }

    if (empty($_POST["serialNo"])) {
        $serialNo = null;
    } else {
        $serialNo = trim($_POST["serialNo"]);
    }

    if (empty($_POST["active"])) {
        $active = null;
    } else {
        $active = trim($_POST["active"]);
    }

    if(! empty($cameraId))
    {
        if ($update_stmt = $db->prepare("UPDATE Camera SET name=?, serial_number=?, active=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssss', $cameraName, $serialNo, $active, $cameraId);

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
        if ($insert_stmt = $db->prepare("INSERT INTO Camera (name, serial_number, active) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $cameraName, $serialNo, $active);

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