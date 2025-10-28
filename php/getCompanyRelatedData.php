<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['companyId'])){
    $companyId = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);

    $tables = ['Customer', 'Supplier', 'Product', 'Raw_Mat', 'Projects'];
    $response = [
        "status" => "success",
        "message" => []
    ];

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE company_id = ? AND status = 0";
        if ($stmt = $db->prepare($sql)) {
            $stmt->bind_param('s', $companyId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $records = [];

                while ($row = $result->fetch_assoc()) {
                    $records[] = $row;
                }

                $response['message'][$table] = $records;
            } else {
                $response['message'][$table] = [];
            }

            $stmt->close();
        } else {
            $response['message'][$table] = [];
        }
    }

    echo json_encode($response);
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>