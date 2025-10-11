<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
    echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
    exit();
}

if (isset($_POST)) {
    $statusSwitch = "";
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
    
    // Convert auto data to JSON string
    $autoDataJson = json_encode($autoData);

    $modified_by = $_SESSION['username'];

    $sql = "UPDATE Deduction 
            SET F1=?, F2=?, F3=?, F4=?, F5=?, F6=?, 
                F7=?, F8=?, F9=?, F10=?, F11=?, F12=?, 
                status=?, auto_data=?, modified_by=?, updated_at=NOW() 
            LIMIT 1";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param(
            "ddddddddddddsss",
            $F1, $F2, $F3, $F4, $F5, $F6,
            $F7, $F8, $F9, $F10, $F11, $F12,
            $statusSwitch, $autoDataJson, $modified_by
        );

        if($stmt->execute()){
            $stmt->close();
            $db->close();

            echo json_encode([
                'status' => 'success',
                'message' => 'Deduction updated successfully!'
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Database error: ' . $stmt->error
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'failed',
            'message' => 'Something went wrong!'
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Missing required fields!'
    ]);
    exit();
}

// Helper to handle nullable numeric inputs
function parseNullableFloat($key) {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return (float) $_POST[$key];
    }
    return 0;
}
?>