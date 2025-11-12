<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorSoProductArray = [];
    foreach ($data as $rows) {
        $Code = $rows['RawMaterialCode'];
        $Name = !empty($rows['RawMaterialName']) ? trim($rows['RawMaterialName']) : '';
        $Description = !empty($rows['RawMaterialName']) ? trim($rows['RawMaterialName']) : '';
        $Company = !empty($rows['Company']) ? searchCompanyIdByName($rows['Company'], $db) : '';
        $Plant = !empty($rows['Plant']) ? searchPlantIdByName($rows['Plant'], $db) : '';
        $Price = '0.00';
        $type = 'Raw Material';
        $action = "1";
        
        if($Code != null && $Code != '' && $Company != null && $Company != ''){
            $rawMatQuery = "SELECT * FROM Raw_Mat WHERE raw_mat_code = '$Code' AND company_id = '$Company' AND status='0'";
            $rawMatDetail = mysqli_query($db, $rawMatQuery);
            $rawMatRow = mysqli_fetch_assoc($rawMatDetail);
            
            if(empty($rawMatRow)){
                $Company = json_encode([(string)$Company]);
                if ($insert_stmt = $db->prepare("INSERT INTO Raw_Mat (raw_mat_code, name, description, price, type, company_id, plant_id, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssss', $Code, $Name, $Description, $Price, $type, $Company, $Plant, $uid, $uid);
                    $insert_stmt->execute();
                    $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                    $insert_stmt->close();    
                }
            }else{
                $errMsg = "Raw Material: ". $Name ." already exist in master data.";
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
