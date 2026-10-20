<?php
session_start();
require_once '../../db_connect.php';
require_once '../../requires/permissions.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("status" => "failed", "message" => "Unauthorized"));
    exit();
}

if (!hasModulePermission('User Management', 'Staff', ['reset_password'])) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
    exit();
}

if (isset($_POST['userID']) && trim($_POST['userID']) != '') {
    $id = trim($_POST['userID']);
    $password = password_hash("123456", PASSWORD_DEFAULT);
    $modifiedBy = $_SESSION['username'];

    // SADMIN accounts are hidden from the staff list, so they cannot be reset from it
    if ($stmt = $db->prepare("UPDATE Users SET password=?, modified_by=? WHERE id=? AND role <> 'SADMIN'")) {
        $stmt->bind_param("sss", $password, $modifiedBy, $id);

        if (!$stmt->execute()) {
            echo json_encode(array("status" => "failed", "message" => $stmt->error));
        } else if ($stmt->affected_rows === 0) {
            echo json_encode(array("status" => "failed", "message" => "User not found"));
        } else {
            echo json_encode(array("status" => "success", "message" => "Password has been reset to 123456"));
        }
        $stmt->close();
        $db->close();
    } else {
        echo json_encode(array("status" => "failed", "message" => "Something went wrong"));
    }
} else {
    echo json_encode(array("status" => "failed", "message" => "Please fill in all the fields"));
}
?>
