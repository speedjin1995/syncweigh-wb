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
        $TransporterFit = !empty($rows['TransporterFit']) ? $rows['TransporterFit'] : '';
        $TransporterLoad = !empty($rows['TransporterLoad']) ? trim($rows['TransporterLoad']) : '';
        $action = "1";
        
        # Checking for existing Project.
        if($TransporterFit != null && $TransporterFit != '' && $TransporterLoad != null && $TransporterLoad != ''){
            $transportCapQuery = "SELECT * FROM Transport_Cap WHERE transport_fit = '$TransporterFit' AND transport_load = '$TransporterLoad' AND status='0'";
            $transportCapDetail = mysqli_query($db, $transportCapQuery);
            $transportCapRow = mysqli_fetch_assoc($transportCapDetail);
            
            if(empty($transportCapRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Transport_Cap (transport_fit, transport_load, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssss', $TransporterFit, $TransporterLoad, $uid, $uid);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }
            }else{
                $errMsg = "Transporter Cap: ". $TransporterFit ." and Transporter Load: ". $TransporterLoad ." already exist in master data.";
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
