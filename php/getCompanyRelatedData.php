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
        if (in_array($table, ['Product', 'Raw_Mat'])) {
            // json column, use JSON_CONTAINS to check if companyId is in the array
            $sql = "SELECT * FROM $table WHERE status = 0 AND JSON_CONTAINS(company_id, ?, '$')";
            $param = json_encode((string) $companyId); // JSON_CONTAINS expects a JSON value, e.g. '"9"'
        } else {
            $sql = "SELECT * FROM $table WHERE company_id = ? AND status = 0";
            $param = $companyId;
        }

        if ($stmt = $db->prepare($sql)) {
            $stmt->bind_param('s', $param);

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