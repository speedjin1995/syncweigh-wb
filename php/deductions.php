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

    // Manual
    $F1  = parseNullableFloat('F1');
    $F2  = parseNullableFloat('F2');
    $F3  = parseNullableFloat('F3');
    $F4  = parseNullableFloat('F4');
    $F5  = parseNullableFloat('F5');
    $F6  = parseNullableFloat('F6');
    $F7  = parseNullableFloat('F7');
    $F8  = parseNullableFloat('F8');
    $F9  = parseNullableFloat('F9');
    $F10 = parseNullableFloat('F10');
    $F11 = parseNullableFloat('F11');
    $F12 = parseNullableFloat('F12');

    // Auto
    $autoData = [];
    if ($statusSwitch == 'Auto' && isset($_POST['rangeFrom']) && is_array($_POST['rangeFrom'])) {
        $rangeFrom = $_POST['rangeFrom'];
        $rangeTo = $_POST['rangeTo'] ?? [];
        $negativeKg = $_POST['negativeKg'] ?? [];
        $positiveKg = $_POST['positiveKg'] ?? [];
        $negativePerc = $_POST['negativePerc'] ?? [];
        $positivePerc = $_POST['positivePerc'] ?? [];
        
        // Build auto data array
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
    $autoDataJson = json_encode($autoData);

    $created_by = $_SESSION['username'];
    $modified_by = $_SESSION['username'];

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
        if ($update_stmt = $db->prepare("UPDATE $table SET $idName=?, F1=?, F2=?, F3=?, F4=?, F5=?, F6=?, F7=?, F8=?, F9=?, F10=?, F11=?, F12=?, auto_data=?, status=?, modified_by=?, updated_at=NOW() WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssssssssssss', $custSuppId, $F1, $F2, $F3, $F4, $F5, $F6, $F7, $F8, $F9, $F10, $F11, $F12, $autoDataJson, $statusSwitch, $modified_by, $deductionId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
        }
    }else{
        if ($insert_stmt = $db->prepare("INSERT INTO $table ($idName, F1, F2, F3, F4, F5, F6, F7, F8, F9, F10, F11, F12, auto_data, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())")) {
            $insert_stmt->bind_param('ssssssssssssssss', $custSuppId, $F1, $F2, $F3, $F4, $F5, $F6, $F7, $F8, $F9, $F10, $F11, $F12, $autoDataJson, $statusSwitch, $created_by);

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );

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