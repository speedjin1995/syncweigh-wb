<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['id'];
}

if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
    $type = $_POST['type'];

    if ($type == 'screen'){
        $password2 = "";
        if (isset($_POST['password2']) && $_POST['password2']!=null && $_POST['password2']!="") {
            $password2 = $_POST['password2'];
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Please enter Password 2"
                )
            ); 
            exit;
        }

        if($stmt = $db->prepare("SELECT password2 FROM Users WHERE id = ?")){
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($password2, $row['password2'])) {
                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> "User authenticated successfully"
                        )
                    );
                } else {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> "Passwords do not match"
                        )
                    );
                }
            } else {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "User not found"
                    )
                );
            }
        }else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Somthings wrong"
                )
            );
        }
    }else{
        $password3 = "";
        if (isset($_POST['password3']) && $_POST['password3']!=null && $_POST['password3']!="") {
            $password3 = $_POST['password3'];
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Please enter Password 3"
                )
            ); 
            exit;
        }

        if($stmt = $db->prepare("SELECT password3 FROM Users WHERE id = ?")){
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($password3, $row['password3'])) {
                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> "User authenticated successfully"
                        )
                    );
                } else {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> "Passwords do not match"
                        )
                    );
                }
            } else {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "User not found"
                    )
                );
            }
        }else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Somthings wrong"
                )
            );
        }
    }

    $stmt->close();
    $db->close();
}else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
