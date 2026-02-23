<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    $errorArray = [];
    foreach ($data as $rows) {
        $GraderName = !empty($rows['GraderName']) ? trim($rows['GraderName']) : '';
        $CertificateID = !empty($rows['CertificateID']) ? trim($rows['CertificateID']) : '';
        $actionId = 1;

        # Checking for existing Grader Name & Certificate ID
        if($GraderName != null && $GraderName != '' && $CertificateID != null && $CertificateID != ''){
            $graderQuery = "SELECT * FROM Grader WHERE grader_name = '$GraderName' AND cert_id = '$CertificateID' AND status = '0'";
            $graderDetail = mysqli_query($db, $graderQuery);
            $graderRow = mysqli_fetch_assoc($graderDetail);
            if(empty($graderRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Grader (grader_name, cert_id, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssss', $GraderName, $CertificateID, $uid, $uid);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }else{
                $errMsg = "Grader: ".$GraderName." with Certificate ID: ".$CertificateID." already exists in master data.";
                $errorArray[] = $errMsg;
                continue;
            }
        }
    }

    $db->close();

    if (!empty($errorArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorArray 
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
