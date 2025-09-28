<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
    echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
    exit();
}

if (isset($_POST)) {
    // Helper to handle nullable numeric inputs
    function parseNullableFloat($key) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') {
            return (float) $_POST[$key];
        }
        return 0;
    }

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

    $status = isset($_POST['status']) && $_POST['status'] === "Enable" ? "Enable" : "Disable";
    $modified_by = $_SESSION['username'];

    $sql = "UPDATE Deduction 
            SET F1=?, F2=?, F3=?, F4=?, F5=?, F6=?, 
                F7=?, F8=?, F9=?, F10=?, F11=?, F12=?, 
                status=?, modified_by=?, updated_at=NOW() 
            LIMIT 1";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param(
            "ddddddddddddss",
            $F1, $F2, $F3, $F4, $F5, $F6,
            $F7, $F8, $F9, $F10, $F11, $F12,
            $status, $modified_by
        );

        if($stmt->execute()){
            $stmt->close();
            $db->close();

            echo '<script type="text/javascript">alert("Deduction updated successfully!");</script>'; 
            header("location: ../deductionSetup.php");
            exit();
        } else {
            echo '<script type="text/javascript">alert("'.$stmt->error.'");</script>'; 
            header("location: ../deductionSetup.php");
            exit();
        }
    } else {
        echo '<script type="text/javascript">alert("Something went wrong!");</script>'; 
        header("location: ../deductionSetup.php");
        exit();
    }
} else {
    echo '<script type="text/javascript">alert("Missing required fields!");</script>'; 
    header("location: ../deductionSetup.php");
    exit();
}
?>