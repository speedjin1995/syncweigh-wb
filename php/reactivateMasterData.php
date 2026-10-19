<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

$username = $_SESSION["username"];

// Sets the row active and writes its Reactivated log row here.
// The table's UPDATE trigger skips its own log row while @skip_audit = 1.
function reactivateRow($db, $username, $table, $logTable, $logColumns, $selectColumns, $id) {
	$db->query("SET @skip_audit = 1");
	$stmt = $db->prepare("UPDATE {$table} SET status='0', modified_by=? WHERE id=?");
	if (!$stmt) {
		$db->query("SET @skip_audit = 0");
		return array("status"=> "failed", "message"=> "Somethings wrong");
	}
	$stmt->bind_param('ss', $username, $id);
	$updated = $stmt->execute();
	$error = $stmt->error;
	$stmt->close();
	$db->query("SET @skip_audit = 0");
	if (!$updated) {
		return array("status"=> "failed", "message"=> $error);
	}

	$action = "6"; // Log_Action 6 = Reactivated
	$insert = $db->prepare("INSERT INTO {$logTable} ({$logColumns}, action_id, action_by, event_date) SELECT {$selectColumns}, ?, modified_by, modified_date FROM {$table} WHERE id = ?");
	if (!$insert) {
		return array("status"=> "failed", "message"=> "Somethings wrong");
	}
	$insert->bind_param('ss', $action, $id);
	$inserted = $insert->execute();
	$error = $insert->error;
	$insert->close();
	if (!$inserted) {
		return array("status"=> "failed", "message"=> $error);
	}
	return array("status"=> "success", "message"=> "Reactivated");
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = '';

	if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
		$type = $_POST['type'];
	}

	$reactivateModules = array('Customer' => 'Customer', 'Destination' => 'Destination', 'Product' => 'Products', 'RawMat' => 'Raw Material', 'Supplier' => 'Supplier', 'Vehicle' => 'Vehicles', 'Transporter' => 'Transporter', 'Plant' => 'Plant');
	if (isset($reactivateModules[$type]) && !hasModulePermission('Master Data', $reactivateModules[$type], ['reactivate'])) {
		echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
		exit;
	}
	if ($type == 'Customer') {
		echo json_encode(reactivateRow($db, $username, 'Customer', 'Customer_Log',
			"customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no",
			"id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no",
			$id));
	}elseif ($type == 'Destination') {
		echo json_encode(reactivateRow($db, $username, 'Destination', 'Destination_Log',
			"destination_id, destination_code, name, description",
			"id, destination_code, name, description",
			$id));
	}elseif ($type == 'Product') {
		echo json_encode(reactivateRow($db, $username, 'Product', 'Product_Log',
			"product_id, product_code, name, description, variance, high, low",
			"id, product_code, name, description, variance, high, low",
			$id));
	}elseif ($type == 'RawMat') {
		echo json_encode(reactivateRow($db, $username, 'Raw_Mat', 'Raw_Mat_Log',
			"raw_mat_id, raw_mat_code, name, description, variance, high, low",
			"id, raw_mat_code, name, description, variance, high, low",
			$id));
	}elseif ($type == 'Supplier') {
		echo json_encode(reactivateRow($db, $username, 'Supplier', 'Supplier_Log',
			"supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no",
			"id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no",
			$id));
	}elseif ($type == 'Vehicle') {
		echo json_encode(reactivateRow($db, $username, 'Vehicle', 'Vehicle_Log',
			"vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, contractor_id, raw_mat_code, raw_mat_name, bin_no, bin_type",
			"id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, contractor_id, raw_mat_code, raw_mat_name, bin_no, bin_type",
			$id));
	}elseif ($type == 'Transporter') {
		echo json_encode(reactivateRow($db, $username, 'Transporter', 'Transporter_Log',
			"transporter_id, transporter_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no",
			"id, transporter_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no",
			$id));
	}elseif ($type == 'User') {
		echo json_encode(reactivateRow($db, $username, 'Users', 'Users_Log',
			"user_id, employee_code, username, name, useremail, password, plant_id, languages, status",
			"id, employee_code, username, name, useremail, password, plant_id, languages, status",
			$id));
	}elseif ($type == 'Plant') {
		echo json_encode(reactivateRow($db, $username, 'Plant', 'Plant_Log',
			"plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, misc, do_no",
			"id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, misc, do_no",
			$id));
	}
} else{
    echo json_encode(
        array(
            "status"=> "failed",
            "message"=> "Please fill in all the fields"
        )
    );
}
?>
