<?php
session_start();
require_once '../../db_connect.php';
require_once '../../services/BaseService.php';

if (!isset($_SESSION['id'])) {
    echo '<script type="text/javascript">location.href = "../login.php";</script>';
} else {
    $username = $_SESSION["username"];
}
$id = $_SESSION['id'];

if (isset($_POST['destinationCode'])) {

    $destinationId = empty($_POST["id"]) ? null : trim($_POST["id"]);
    $destinationCode = empty($_POST["destinationCode"]) ? null : trim($_POST["destinationCode"]);
    $destinationName = empty($_POST["destinationName"]) ? null : trim($_POST["destinationName"]);
    $description = empty($_POST["description"]) ? null : trim($_POST["description"]);

    if (!empty($destinationId)) {
        $db->begin_transaction();
        try {
            // Old code / name, to update the weighing records that reference them
            $select_stmt = $db->prepare("SELECT destination_code, name FROM Destination WHERE id=?");
            if (!$select_stmt) throw new Exception($db->error);
            $select_stmt->bind_param('s', $destinationId);
            if (!$select_stmt->execute()) throw new Exception($select_stmt->error);
            $old = $select_stmt->get_result()->fetch_assoc();
            $select_stmt->close();

            $update_stmt = $db->prepare("UPDATE Destination SET destination_code=?, name=?, description=?, created_by=?, modified_by=? WHERE id=?");
            if (!$update_stmt) throw new Exception($db->error);
            $update_stmt->bind_param('ssssss', $destinationCode, $destinationName, $description, $username, $username, $destinationId);
            if (!$update_stmt->execute()) throw new Exception($update_stmt->error);
            $update_stmt->close();

            if ($old) {
                $service = new BaseService($db, $username);
                if ($old['destination_code'] !== $destinationCode) {
                    $service->updateMasterDataCodeValue($old['destination_code'], $destinationCode, 'Destination');
                }
                if ($old['name'] !== $destinationName) {
                    $service->updateMasterDataNameValue($old['name'], $destinationName, 'Destination');
                }
            }

            $db->commit();
            $db->close();
            echo json_encode(array("status" => "success", "message" => "Updated Successfully!!"));
        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
        }
    } else {
        if ($stmt = $db->prepare("INSERT INTO Destination (destination_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
            $stmt->bind_param('sssss', $destinationCode, $destinationName, $description, $username, $username);

            if (!$stmt->execute()) {
                echo json_encode(array("status" => "failed", "message" => $stmt->error));
            } else {
                $stmt->close();
                $db->close();
                echo json_encode(array("status" => "success", "message" => "Added Successfully!!"));
            }
        }
    }
} else {
    echo json_encode(array("status" => "failed", "message" => "Please fill in all the fields"));
}
?>
