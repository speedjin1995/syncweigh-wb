<?php
session_start();
require_once '../../db_connect.php';
require_once '../../requires/permissions.php';
$requiredPermission = empty($_POST["id"]) ? 'create' : 'edit';
if (!hasModulePermission('Master Data', 'Plant', [$requiredPermission])) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
    exit;
}
require_once '../../services/BaseService.php';

if (!isset($_SESSION['id'])) {
    echo '<script type="text/javascript">location.href = "../login.php";</script>';
} else {
    $username = $_SESSION["username"];
}

if (isset($_POST['plantCode'], $_POST['plantName'])) {

    $plantId = empty($_POST["id"]) ? null : trim($_POST["id"]);
    $plantCode = empty($_POST["plantCode"]) ? null : trim($_POST["plantCode"]);
    $plantName = empty($_POST["plantName"]) ? null : trim($_POST["plantName"]);
    $addressLine1 = empty($_POST["addressLine1"]) ? null : trim($_POST["addressLine1"]);
    $addressLine2 = empty($_POST["addressLine2"]) ? null : trim($_POST["addressLine2"]);
    $addressLine3 = empty($_POST["addressLine3"]) ? null : trim($_POST["addressLine3"]);
    $phoneNo = empty($_POST["phoneNo"]) ? null : trim($_POST["phoneNo"]);
    $faxNo = empty($_POST["faxNo"]) ? null : trim($_POST["faxNo"]);

    if (!empty($plantId)) {
        $db->begin_transaction();
        try {
            // Old code / name, to update the weighing records that reference them
            $select_stmt = $db->prepare("SELECT plant_code, name FROM Plant WHERE id=?");
            if (!$select_stmt) throw new Exception($db->error);
            $select_stmt->bind_param('s', $plantId);
            if (!$select_stmt->execute()) throw new Exception($select_stmt->error);
            $old = $select_stmt->get_result()->fetch_assoc();
            $select_stmt->close();

            $update_stmt = $db->prepare("UPDATE Plant SET plant_code=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE id=?");
            if (!$update_stmt) throw new Exception($db->error);
            $update_stmt->bind_param('ssssssssss', $plantCode, $plantName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $username, $username, $plantId);
            if (!$update_stmt->execute()) throw new Exception($update_stmt->error);
            $update_stmt->close();

            if ($old) {
                $service = new BaseService($db, $username);
                if ($old['plant_code'] !== $plantCode) {
                    $service->updateMasterDataCodeValue($old['plant_code'], $plantCode, 'Plant');
                }
                if ($old['name'] !== $plantName) {
                    $service->updateMasterDataNameValue($old['name'], $plantName, 'Plant');
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
        if ($stmt = $db->prepare("INSERT INTO Plant (plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param('sssssssss', $plantCode, $plantName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $username, $username);

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
