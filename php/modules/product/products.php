<?php
session_start();
require_once '../../db_connect.php';
require_once '../../requires/permissions.php';
$requiredPermission = empty($_POST["id"]) ? 'create' : 'edit';
if (!hasModulePermission('Master Data', 'Products', [$requiredPermission])) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
    exit;
}
require_once '../../services/BaseService.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
// Processing form data when form is submitted
if (isset($_POST['productCode'])) {

    if (empty($_POST["id"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["id"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
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

    if (!empty($productId)) {
        $db->begin_transaction();
        try {
            // Old code / name, to update the weighing records that reference them
            $select_stmt = $db->prepare("SELECT product_code, name FROM Product WHERE id=?");
            if (!$select_stmt) throw new Exception($db->error);
            $select_stmt->bind_param('s', $productId);
            if (!$select_stmt->execute()) throw new Exception($select_stmt->error);
            $old = $select_stmt->get_result()->fetch_assoc();
            $select_stmt->close();

            $update_stmt = $db->prepare("UPDATE Product SET product_code=?, name=?, description=?, variance=?, high=?, low=?, created_by=?, modified_by=? WHERE id=?");
            if (!$update_stmt) throw new Exception($db->error);
            $update_stmt->bind_param('sssssssss', $productCode, $productName, $description, $varianceType, $high, $low, $username, $username, $productId);
            if (!$update_stmt->execute()) throw new Exception($update_stmt->error);
            $update_stmt->close();

            if ($old) {
                $service = new BaseService($db, $username);
                if ($old['product_code'] !== $productCode) {
                    $service->updateMasterDataCodeValue($old['product_code'], $productCode, 'Product');
                }
                if ($old['name'] !== $productName) {
                    $service->updateMasterDataNameValue($old['name'], $productName, 'Product');
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
        if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, description, variance, high, low, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssss', $productCode, $productName, $description, $varianceType, $high, $low, $username, $username);

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
                $productId = $insert_stmt->insert_id;

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