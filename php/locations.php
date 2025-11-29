<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$uid = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['locationCode'], $_POST['locationName'], $_POST["plant"])) {

    if (empty($_POST["id"])) {
        $id = null;
    } else {
        $id = trim($_POST["id"]);
    }

    if (empty($_POST["locationCode"])) {
        $locationCode = null;
    } else {
        $locationCode = trim($_POST["locationCode"]);
    }

    if (empty($_POST["locationName"])) {
        $locationName = null;
    } else {
        $locationName = trim($_POST["locationName"]);
    }

    if (empty($_POST["plant"])) {
        $plant = null;
    } else {
        $plant = trim($_POST["plant"]);
    }

    if (empty($_POST["indicator"])) {
        $indicator = 'D2008';
    } else {
        $indicator = trim($_POST["indicator"]);
    }

    if (empty($_POST["serialPort"])) {
        $serialPort = 'COM3';
    } else {
        $serialPort = trim($_POST["serialPort"]);
    }

    if (empty($_POST["serialPortBaudRate"])) {
        $serialPortBaudRate = '2400';
    } else {
        $serialPortBaudRate = trim($_POST["serialPortBaudRate"]);
    }

    if (empty($_POST["serialPortDataBits"])) {
        $serialPortDataBits = '7';
    } else {
        $serialPortDataBits = trim($_POST["serialPortDataBits"]);
    }

    if (empty($_POST["serialPortParity"])) {
        $serialPortParity = 'E';
    } else {
        $serialPortParity = trim($_POST["serialPortParity"]);
    }

    if (empty($_POST["serialPortStopBits"])) {
        $serialPortStopBits = '1';
    } else {
        $serialPortStopBits = trim($_POST["serialPortStopBits"]);
    }
    
    if(! empty($id))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Location SET location_code=?, location_name=?, plant_id=?, indicator=?, com_port=?, bits_per_second=?, data_bits=?, parity=?, stop_bits=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssss', $locationCode, $locationName, $plant, $indicator, $serialPort, $serialPortBaudRate, $serialPortDataBits, $serialPortParity, $serialPortStopBits, $id);

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
        if ($insert_stmt = $db->prepare("INSERT INTO Location (location_code, location_name, plant_id, indicator, com_port, bits_per_second, data_bits, parity, stop_bits) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssss', $locationCode, $locationName, $plant, $indicator, $serialPort, $serialPortBaudRate, $serialPortDataBits, $serialPortParity, $serialPortStopBits);

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
                $plantId = $insert_stmt->insert_id;
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