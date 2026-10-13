<?php
session_start();
require_once '../../db_connect.php';
require_once '../../services/BaseService.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
$id = $_SESSION['id'];

if (isset($_POST['customerCode'])) {

    $customerId = empty($_POST["id"]) ? null : trim($_POST["id"]);
    $customerCode = empty($_POST["customerCode"]) ? null : trim($_POST["customerCode"]);
    $companyRegNo = empty($_POST["companyRegNo"]) ? null : trim($_POST["companyRegNo"]);
    $newRegNo = empty($_POST["newRegNo"]) ? null : trim($_POST["newRegNo"]);
    $companyName = empty($_POST["companyName"]) ? null : trim($_POST["companyName"]);
    $addressLine1 = empty($_POST["addressLine1"]) ? null : trim($_POST["addressLine1"]);
    $addressLine2 = empty($_POST["addressLine2"]) ? null : trim($_POST["addressLine2"]);
    $addressLine3 = empty($_POST["addressLine3"]) ? null : trim($_POST["addressLine3"]);
    $addressLine4 = empty($_POST["addressLine4"]) ? null : trim($_POST["addressLine4"]);
    $phoneNo = empty($_POST["phoneNo"]) ? null : trim($_POST["phoneNo"]);
    $faxNo = empty($_POST["faxNo"]) ? null : trim($_POST["faxNo"]);
    $contactName = empty($_POST["contactName"]) ? null : trim($_POST["contactName"]);
    $icNo = empty($_POST["icNo"]) ? null : trim($_POST["icNo"]);
    $tinNo = empty($_POST["tinNo"]) ? null : trim($_POST["tinNo"]);
    
    if (!empty($customerId)) {
        $db->begin_transaction();
        try {
            // Old code / name, to update the weighing records that reference them
            $select_stmt = $db->prepare("SELECT customer_code, name FROM Customer WHERE id=?");
            if (!$select_stmt) throw new Exception($db->error);
            $select_stmt->bind_param('s', $customerId);
            if (!$select_stmt->execute()) throw new Exception($select_stmt->error);
            $old = $select_stmt->get_result()->fetch_assoc();
            $select_stmt->close();

            $update_stmt = $db->prepare("UPDATE Customer SET customer_code=?, company_reg_no=?, new_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, contact_name=?, ic_no=?, tin_no=?, created_by=?, modified_by=? WHERE id=?");
            if (!$update_stmt) throw new Exception($db->error);
            $update_stmt->bind_param('sssssssssssssss', $customerCode, $companyRegNo, $newRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $contactName, $icNo, $tinNo, $username, $username, $customerId);
            if (!$update_stmt->execute()) throw new Exception($update_stmt->error);
            $update_stmt->close();

            if ($old) {
                $service = new BaseService($db, $username);
                if ($old['customer_code'] !== $customerCode) {
                    $service->updateMasterDataCodeValue($old['customer_code'], $customerCode, 'Customer');
                }
                if ($old['name'] !== $companyName) {
                    $service->updateMasterDataNameValue($old['name'], $companyName, 'Customer');
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
        if ($insert_stmt = $db->prepare("INSERT INTO Customer (customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssssss', $customerCode, $companyRegNo, $newRegNo, $companyName, $addressLine1, $addressLine2, $addressLine3, $phoneNo, $faxNo, $contactName, $icNo, $tinNo, $username, $username);

            if (!$insert_stmt->execute()) {
                echo json_encode(array("status" => "failed", "message" => $insert_stmt->error));
            } else {
                $insert_stmt->close();
                $db->close();
                echo json_encode(array("status" => "success", "message" => "Added Successfully!!"));
            }
        }
    }
} else {
    echo json_encode(array("status" => "failed", "message" => "Please fill in all the fields"));
}
?>
