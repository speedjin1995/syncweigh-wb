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
if (isset($_POST['siteCode'])) {

    if (empty($_POST["id"])) {
        $transporterId = null;
    } else {
        $transporterId = trim($_POST["id"]);
    }

    if (empty($_POST["siteCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["siteCode"]);
    }

    if (empty($_POST["projectName"])) {
        $projectName = null;
    } else {
        $projectName = trim($_POST["projectName"]);
    }
    
    if(!empty($transporterId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Projects SET project=?, project_name=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssss', $transporterCode, $projectName, $username, $username, $transporterId);

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
        if ($insert_stmt = $db->prepare("INSERT INTO Projects (project, project_name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $transporterCode, $projectName, $username, $username);

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
                $siteId = $insert_stmt->insert_id; // Get the inserted plant ID

                $insert_stmt->close();
                $db->close();
                
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
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