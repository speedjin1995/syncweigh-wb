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
if (isset($_POST['graderName']) && isset($_POST['graderCertId'])) {

    if (empty($_POST["id"])) {
        $graderId = null;
    } else {
        $graderId = trim($_POST["id"]);
    }

    if (empty($_POST["graderName"])) {
        $graderName = null;
    } else {
        $graderName = trim($_POST["graderName"]);
    }

    if (empty($_POST["graderCertId"])) {
        $certId = null;
    } else {
        $certId = trim($_POST["graderCertId"]);
    }
    
    if(! empty($graderId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Grader SET grader_name=?, cert_id=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssss', $graderName, $certId, $username, $graderId);

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
        if ($insert_stmt = $db->prepare("INSERT INTO Grader (grader_name, cert_id, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $graderName, $certId, $username, $username);

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