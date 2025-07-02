<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $rows) {
        $DestinationCode = !empty($rows['DestinationCode']) ? trim($rows['DestinationCode']) : '';
        $DestinationName = !empty($rows['DestinationName']) ? trim($rows['DestinationName']) : '';
        $Description = !empty($rows['Description']) ? trim($rows['Description']) : '';

        # Check if destination code exist in DB
        $status = "0";
        $destinationQuery = "SELECT * FROM Destination WHERE destination_code = '$DestinationCode' AND status = '$status'";
        $destinationDetail = mysqli_query($db, $destinationQuery);
        $destinationRow = mysqli_fetch_assoc($destinationDetail);

        if(empty($destinationRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Destination (destination_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssss', $DestinationCode, $DestinationName, $Description, $uid, $uid);
                $insert_stmt->execute();
                $desId = $insert_stmt->insert_id; // Get the inserted destination ID
                $insert_stmt->close();
    
                $action = "1";
                if ($insert_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, description, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssss', $desId, $DestinationCode, $DestinationName, $description, $action, $uid);
                    $insert_log->execute();
                    $insert_log->close();
                }            
            }
        }
        
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
