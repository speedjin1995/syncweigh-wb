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
        $Code = $rows['Item'];
        $Name = !empty($rows['Description']) ? trim($rows['Description']) : '';
        $Description = !empty($rows['Description']) ? trim($rows['Description']) : '';
        $Price = '0.00';
        $action = "1";
        
        # Checking for existing Product.
        if($Code != null && $Code != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$Code' AND status='0'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, description, price, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssss', $Code, $Name, $Description, $Price, $uid, $uid);
                    $insert_stmt->execute();
                    $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                    $insert_stmt->close();

                    if ($insert_log = $db->prepare("INSERT INTO Product_Log (product_id, product_code, name, description, price, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_log->bind_param('sssssss', $invid, $Code, $Name, $Description, $Price, $action, $uid);
                        $insert_log->execute();
                        $insert_log->close();
                    }            
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
