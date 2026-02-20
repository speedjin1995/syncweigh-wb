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

    $modified_by = $_SESSION['username'];
    $params = [];
    $types = "";
    $setClauses = [];

    // Build dynamic SQL based on statusSwitch
    if ($statusSwitch == 'Manual') {
        $setClauses[] = "F1=?, F2=?, F3=?, F4=?, F5=?, F6=?, F7=?, F8=?, F9=?, F10=?, F11=?, F12=?";
        $types .= "dddddddddddd";
        $params = [
            parseNullableFloat('F1'), parseNullableFloat('F2'), parseNullableFloat('F3'),
            parseNullableFloat('F4'), parseNullableFloat('F5'), parseNullableFloat('F6'),
            parseNullableFloat('F7'), parseNullableFloat('F8'), parseNullableFloat('F9'),
            parseNullableFloat('F10'), parseNullableFloat('F11'), parseNullableFloat('F12')
        ];
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
        $setClauses[] = "auto_data=?";
        $types .= "s";
        $params[] = json_encode($autoData);
    } elseif ($statusSwitch == 'Default') {
        $setClauses[] = "default_range_min=?, default_range_max=?, default_range_weight=?";
        $types .= "ddd";
        $params = [
            parseNullableFloat('defaultRangeMin'),
            parseNullableFloat('defaultRangeMax'),
            parseNullableFloat('defaultWeight')
        ];
    } elseif ($statusSwitch == 'Customer_Supplier') {
        $setClauses[] = "customers=?, suppliers=?";
        $types .= "ss";
        $params = [
            json_encode(isset($_POST['customer']) ? $_POST['customer'] : []),
            json_encode(isset($_POST['supplier']) ? $_POST['supplier'] : [])
        ];
    }

    // Add status, modified_by and updated_at to all queries
    $setClauses[] = "status=?, modified_by=?, updated_at=NOW()";
    $types .= "ss";
    $params[] = $statusSwitch;
    $params[] = $modified_by;

    $sql = "UPDATE Deduction SET " . implode(", ", $setClauses) . " LIMIT 1";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param($types, ...$params);

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