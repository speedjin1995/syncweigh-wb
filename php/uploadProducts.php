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
        $Code = $rows['ProductCode'];
        $Name = !empty($rows['ProductName']) ? trim($rows['ProductName']) : '';
        $Description = !empty($rows['ProductName']) ? trim($rows['ProductName']) : '';
        $Company = !empty($rows['Company']) ? searchCompanyIdByName($rows['Company'], $db) : '';
        $Plant = !empty($rows['Plant']) ? searchPlantIdByName($rows['Plant'], $db) : '';
        $Price = '0.00';
        $action = "1";
        
        # Checking for existing Product.
        if($Code != null && $Code != '' && $Company != null && $Company != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$Code' AND company_id = '$Company' AND status='0'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, description, price, company_id, plant_id, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssss', $Code, $Name, $Description, $Price, $Company, $Plant, $uid, $uid);
                    $insert_stmt->execute();
                    $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                    $insert_stmt->close();
                }
            }else{
                $errMsg = "Product: ". $Name ." already exist in master data.";
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
