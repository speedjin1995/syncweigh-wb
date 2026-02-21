<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
    echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
    exit();
}

if (isset($_POST)) {
    $type = "";
    $deductionId = "";
    $custSuppId = "";
    $statusSwitch = "";

    if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
        $type = $_POST['type'];
    }

    if(isset($_POST['deductionId']) && $_POST['deductionId']!=null && $_POST['deductionId']!=""){
        $deductionId = $_POST['deductionId'];
    }

    if(isset($_POST['custSuppId']) && $_POST['custSuppId']!=null && $_POST['custSuppId']!=""){
        $custSuppId = $_POST['custSuppId'];
    }

    if(isset($_POST['statusSwitch']) && $_POST['statusSwitch']!=null && $_POST['statusSwitch']!=""){
		$statusSwitch = $_POST['statusSwitch'];
	}

    $created_by = $_SESSION['username'];
    $modified_by = $_SESSION['username'];

    // Build dynamic columns and values based on statusSwitch
    $columns = [];
    $values = [];
    $types = "s"; // First param is always custSuppId
    $params = [$custSuppId];

    if ($statusSwitch == 'Manual') {
        $columns = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12'];
        $types .= "dddddddddddd";
        $params = array_merge($params, [
            parseNullableFloat('F1'), parseNullableFloat('F2'), parseNullableFloat('F3'),
            parseNullableFloat('F4'), parseNullableFloat('F5'), parseNullableFloat('F6'),
            parseNullableFloat('F7'), parseNullableFloat('F8'), parseNullableFloat('F9'),
            parseNullableFloat('F10'), parseNullableFloat('F11'), parseNullableFloat('F12')
        ]);
    } elseif ($statusSwitch == 'Auto') {
        $autoData = [];
        if (isset($_POST['rangeFrom']) && is_array($_POST['rangeFrom'])) {
            $rangeFrom = $_POST['rangeFrom'];
            $rangeTo = $_POST['rangeTo'] ?? [];
            $negativeKg = $_POST['negativeKg'] ?? [];
            $positiveKg = $_POST['positiveKg'] ?? [];
            $negativePerc = $_POST['negativePerc'] ?? [];
            $positivePerc = $_POST['positivePerc'] ?? [];
            
            foreach ($rangeFrom as $key => $value) {
                if (!empty($value) || !empty($rangeTo[$key])) {
                    $autoData[] = [
                        'rangeFrom' => (float)($value ?? 0),
                        'rangeTo' => (float)($rangeTo[$key] ?? 0),
                        'negativeKg' => (float)($negativeKg[$key] ?? 0),
                        'positiveKg' => (float)($positiveKg[$key] ?? 0),
                        'negativePerc' => (float)($negativePerc[$key] ?? 0),
                        'positivePerc' => (float)($positivePerc[$key] ?? 0)
                    ];
                }
            }
        }
        $columns = ['auto_data'];
        $types .= "s";
        $params[] = json_encode($autoData);
    }

    $table = null;
    $idName = null;
    if ($type == 'Customer') {
        $table = "Customer_Deduction";
        $idName = "customer_id";
    } else if ($type == 'Supplier') {
        $table = "Supplier_Deduction";
        $idName = "supplier_id";
    }

    if(!empty($deductionId)) {
        // UPDATE
        $setClauses = array_map(function($col) { return "$col=?"; }, $columns);
        $setClauses[] = "status=?";
        $setClauses[] = "modified_by=?";
        $setClauses[] = "updated_at=NOW()";
        
        $updateTypes = $types . "sss";
        $updateParams = array_merge($params, [$statusSwitch, $modified_by, $deductionId]);
        
        $sql = "UPDATE $table SET $idName=?, " . implode(", ", $setClauses) . " WHERE id=?";
        
        if ($update_stmt = $db->prepare($sql)) {
            $update_stmt->bind_param($updateTypes, ...$updateParams);

            if (! $update_stmt->execute()) {
                echo json_encode(["status"=> "failed", "message"=> $update_stmt->error]);
            } else {
                echo json_encode(["status"=> "success", "message"=> "Updated Successfully!!"]);
            }
            $update_stmt->close();
        }
    } else {
        // INSERT
        $insertCols = array_merge([$idName], $columns, ['status', 'created_by', 'created_at']);
        $placeholders = array_fill(0, count($insertCols) - 1, '?');
        $placeholders[] = 'NOW()';
        
        $insertTypes = $types . "ss";
        $insertParams = array_merge($params, [$statusSwitch, $created_by]);
        
        $sql = "INSERT INTO $table (" . implode(", ", $insertCols) . ") VALUES (" . implode(", ", $placeholders) . ")";
        
        if ($insert_stmt = $db->prepare($sql)) {
            $insert_stmt->bind_param($insertTypes, ...$insertParams);

            if (! $insert_stmt->execute()) {
                echo json_encode(["status"=> "failed", "message"=> $insert_stmt->error]);
            } else {
                echo json_encode(["status"=> "success", "message"=> "Added Successfully!!"]);
            }
            $insert_stmt->close();
        }
    }

    $db->close();
} else {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Missing required fields!'
    ]);
}

// Helper to handle nullable numeric inputs
function parseNullableFloat($key) {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return (float) $_POST[$key];
    }
    return 0;
}
?>