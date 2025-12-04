<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    $errorSoProductArray = [];
    foreach ($data as $rows) {
        $DriverName = !empty($rows['DriverName']) ? trim($rows['DriverName']) : '';
        $DriverIc = !empty($rows['DriverIc']) ? trim($rows['DriverIc']) : '';
        $status = '0';
        $actionId = 1;

        # Checking for existing driver code.
        if($DriverName != null && $DriverName != ''){
            $driverQuery = "SELECT * FROM Driver WHERE driver_name = '$DriverName' AND status = '0'";
            $driverDetail = mysqli_query($db, $driverQuery);
            $driverRow = mysqli_fetch_assoc($driverDetail);

            if(empty($driverRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Driver (driver_name, driver_ic) VALUES (?, ?)")) {
                    $insert_stmt->bind_param('sssss', $DriverName, $DriverIc, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $transporterId = $insert_stmt->insert_id;
                    $insert_stmt->close(); 

                    if ($insert_log = $db->prepare("INSERT INTO Driver_Log (driver_name, driver_id, driver_ic, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_log->bind_param('sssssssssssssss', $DriverName, $driverId, $DriverIc, $actionId, $uid);
                        $insert_log->execute();
                        $insert_log->close();
                    }    
                }
            }else{
                $errMsg = "Driver: ". $DriverName ." already exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
    }

    $db->close();

    if (!empty($errorSoProductArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorSoProductArray 
            )
        );
    }else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Added Successfully!!" 
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
