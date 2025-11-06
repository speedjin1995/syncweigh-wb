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
        $Code = $rows['ProjectCode'];
        $Name = !empty($rows['ProjectName']) ? trim($rows['ProjectName']) : '';
        $Company = !empty($rows['Company']) ? searchCompanyIdByName($rows['Company'], $db) : '';
        $Plant = !empty($rows['Plant']) ? searchPlantIdByName($rows['Plant'], $db) : '';
        $action = "1";
        
        # Checking for existing Project.
        if($Code != null && $Code != ''){
            $projectQuery = "SELECT * FROM Projects WHERE project = '$Code' AND status='0'";
            $projectDetail = mysqli_query($db, $projectQuery);
            $projectRow = mysqli_fetch_assoc($projectDetail);
            
            if(empty($projectRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Projects (project, project_name, company_id, plant_id, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssss', $Code, $Name, $Company, $Plant, $uid, $uid);
                    $insert_stmt->execute();
                    $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                    $insert_stmt->close();
                }
            }else{
                $errMsg = "Project: ". $Name ." already exist in master data.";
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
