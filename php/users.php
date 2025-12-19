<?php
session_start();
require_once "db_connect.php";

if(!isset($_SESSION['username'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['employeeCode'], $_POST['username'], $_POST['useremail'], $_POST['roles'], $_POST['allowManual'], $_POST['allowDeduct'])){
    $id = $_SESSION['id'];
    $name = $_SESSION["username"];

    $param_code = null;
    $password = "123456";
    $param_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $param_useremail = filter_input(INPUT_POST, 'useremail', FILTER_SANITIZE_STRING);
    $param_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
    $param_token = bin2hex(random_bytes(50)); // generate unique token
    $param_role = filter_input(INPUT_POST, 'roles', FILTER_SANITIZE_STRING);
    $param_allowmanual = filter_input(INPUT_POST, 'allowManual', FILTER_SANITIZE_STRING);
    $param_allowdeduct = filter_input(INPUT_POST, 'allowDeduct', FILTER_SANITIZE_STRING);
    
    if(isset($_POST['plantId']) && $_POST['plantId'] != null){
        $param_code = filter_input(INPUT_POST, 'employeeCode', FILTER_SANITIZE_STRING);
    }

    $param_plant = array();

    if(isset($_POST['plantId']) && $_POST['plantId'] != null){
        $param_plant = $_POST['plantId'];
    }

    $param_plant = json_encode($param_plant);
    $param_created_by = $name;
    $param_modified_by = $name;

    if($_POST['id'] != null && $_POST['id'] != ''){
        ### Check if new username exists or not but need to exclude current user ###
        if ($username_stmt = $db->prepare("SELECT * FROM Users WHERE username = ? AND id != ?")) {
            $username_stmt->bind_param("si", $param_username, $_POST['id']);

            // Execute the prepared query.
            if (! $username_stmt->execute()) {
                $username_stmt->close();
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $username_stmt->error
                    )
                );
                exit();
            } else {
                $username_stmt->store_result();

                if($username_stmt->num_rows > 0){
                    $username_stmt->close();
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> "Editted Username already exists!!"
                        )
                    );
                    exit();
                }

                $username_stmt->close();
            }
        }

        if ($update_stmt = $db->prepare("UPDATE Users SET username=?, name=?, useremail=?, role=?, modified_by=?, plant_id=?, employee_code=?, allow_manual=?, allow_deduct=? WHERE id=?")) {
            $update_stmt->bind_param("ssssssssss", $param_username, $param_name, $param_useremail, $param_role, $param_modified_by, $param_plant, $param_code, $param_allowmanual, $param_allowdeduct, $_POST['id']);
            $action = "2";

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
    else{
        ### Check if username already exists or not ###
        if ($username_stmt = $db->prepare("SELECT * FROM Users WHERE username = ?")) {
            $username_stmt->bind_param("s", $param_username);

            // Execute the prepared query.
            if (! $username_stmt->execute()) {
                $username_stmt->close();
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $username_stmt->error
                    )
                );
                exit();
            } else {
                $username_stmt->store_result();

                if($username_stmt->num_rows > 0){
                    $username_stmt->close();
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> "Username already exists!!"
                        )
                    );
                    exit();
                }
            }
        }

        ### Only default password for new user if allow deduct is enabled ###
        if ($param_allowdeduct == 'Y'){
            $param_password2 = password_hash('123456', PASSWORD_DEFAULT); // Creates a password hash
            $param_token2 = bin2hex(random_bytes(50)); // generate unique token
            $param_password3 = password_hash('123456', PASSWORD_DEFAULT); // Creates a password hash
            $param_token3 = bin2hex(random_bytes(50)); // generate unique token
        }

        if ($insert_stmt = $db->prepare("INSERT INTO Users (employee_code, useremail, username, name, password, token, password2, token2, password3, token3, role, plant_id, allow_manual, allow_deduct, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {            
            $insert_stmt->bind_param("ssssssssssssssss", $param_code, $param_useremail, $param_username, $param_name, $param_password, $param_token, $param_password2, $param_token2, $param_password3, $param_token3, $param_role, $param_plant, $param_allowmanual, $param_allowdeduct, $param_created_by, $param_modified_by);
            $action = "1";

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
                $insert_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status" => "success", 
                        "message" => "Added Successfully!!",
                        "plants" => $param_plant
                    )
                );
            }
        }
    }

    /*if($action == "1"){
        $sql3 = "INSERT INTO Users_Log (employee_code, username, user_department, status, password, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt3 = mysqli_prepare($link, $sql3)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt3, "sssssss", $param_code, $param_username, $param_role, $param_status, $param_password, $param_action, $param_actionBy);

            // Set parameters
            $param_code = $employeeCode;
            $param_username = $username;
            $param_password = "123456"; // Creates a password hash
            $param_role = $roles;
            $param_status = "0";
            $param_action = $action;
            $param_actionBy = $name;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt3)) {
                echo "Added";
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt3);
        }
    }
    else{

    }*/
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>