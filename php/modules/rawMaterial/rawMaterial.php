<?php
session_start();
require_once '../../db_connect.php';
require_once '../../services/BaseService.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['rawMatCode'])) {

    if (empty($_POST["id"])) {
        $rawMatId = null;
    } else {
        $rawMatId = trim($_POST["id"]);
    }

    if (empty($_POST["rawMatCode"])) {
        $rawMatCode = null;
    } else {
        $rawMatCode = trim($_POST["rawMatCode"]);
    }

    if (empty($_POST["rawMatName"])) {
        $rawMatName = null;
    } else {
        $rawMatName = trim($_POST["rawMatName"]);
    }

    if (empty($_POST["description"])) {
        $description = null;
    } else {
        $description = trim($_POST["description"]);
    }
    
    if (empty($_POST["varianceType"])) {
        $varianceType = null;
    } else {
        $varianceType = trim($_POST["varianceType"]);
    }

    if (empty($_POST["high"])) {
        $high = null;
    } else {
        $high = trim($_POST["high"]);
    }

    if (empty($_POST["low"])) {
        $low = null;
    } else {
        $low = trim($_POST["low"]);
    }

    if (!empty($rawMatId)) {
        $db->begin_transaction();
        try {
            // Old code / name, to update the weighing records that reference them
            $select_stmt = $db->prepare("SELECT raw_mat_code, name FROM Raw_Mat WHERE id=?");
            if (!$select_stmt) throw new Exception($db->error);
            $select_stmt->bind_param('s', $rawMatId);
            if (!$select_stmt->execute()) throw new Exception($select_stmt->error);
            $old = $select_stmt->get_result()->fetch_assoc();
            $select_stmt->close();

            $update_stmt = $db->prepare("UPDATE Raw_Mat SET raw_mat_code=?, name=?, description=?, variance=?, high=?, low=?, created_by=?, modified_by=? WHERE id=?");
            if (!$update_stmt) throw new Exception($db->error);
            $update_stmt->bind_param('sssssssss', $rawMatCode, $rawMatName, $description, $varianceType, $high, $low, $username, $username, $rawMatId);
            if (!$update_stmt->execute()) throw new Exception($update_stmt->error);
            $update_stmt->close();

            if ($old) {
                $service = new BaseService($db, $username);
                if ($old['raw_mat_code'] !== $rawMatCode) {
                    $service->updateMasterDataCodeValue($old['raw_mat_code'], $rawMatCode, 'Raw Material');
                }
                if ($old['name'] !== $rawMatName) {
                    $service->updateMasterDataNameValue($old['name'], $rawMatName, 'Raw Material');
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
        if ($insert_stmt = $db->prepare("INSERT INTO Raw_Mat (raw_mat_code, name, description, variance, high, low, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssss', $rawMatCode, $rawMatName, $description, $varianceType, $high, $low, $username, $username);

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

                $insert_stmt->close();
                $db->close();
            }
        }
    }
    
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>