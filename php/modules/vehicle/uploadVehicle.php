<?php
session_start();
require_once '../../db_connect.php';
require_once '../../requires/permissions.php';
$requiredPermission = 'upload_excel';
if (!hasModulePermission('Master Data', 'Vehicles', [$requiredPermission])) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to do this"));
    exit;
}
require_once '../../requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorSoProductArray = [];
    $status = '0';

    foreach ($data as $rows) {
        $VehicleNo = !empty($rows['VehicleNo']) ? trim($rows['VehicleNo']) : '';
        $VehicleWeight = !empty($rows['VehicleWeightKG']) ? trim($rows['VehicleWeightKG']) : '';
        $CustomerCode = !empty($rows['CustomerCode']) ? trim($rows['CustomerCode']) : '';
        $CustomerName = !empty($rows['CustomerName']) ? trim($rows['CustomerName']) : '';
        $SupplierCode = !empty($rows['SupplierCode']) ? trim($rows['SupplierCode']) : '';
        $SupplierName = !empty($rows['SupplierName']) ? trim($rows['SupplierName']) : '';
        $VehicleType = !empty($rows['VehicleType']) ? trim($rows['VehicleType']) : null;
        $actionId = 1;

        # Customer check
        if ($CustomerCode != null && $CustomerCode != '') {
            $customerRow = searchCustomerByCode($CustomerCode, $db);

            if (empty($customerRow)) {
                $errorSoProductArray[] = "Customer: " . $CustomerCode . " doesn't exist in master data.";
                continue;
            }

            $customerCode = $customerRow['customer_code'];
            $customerName = $customerRow['name'];
        }

        # Supplier check
        if ($SupplierCode != null && $SupplierCode != '') {
            $supplierRow = searchSupplierByCode($SupplierCode, $db);

            if (empty($supplierRow)) {
                $errorSoProductArray[] = "Supplier: " . $SupplierCode . " doesn't exist in master data.";
                continue;
            }

            $supplierCode = $supplierRow['supplier_code'];
            $supplierName = $supplierRow['name'];
        }

        # Vehicle existence check
        if ($VehicleNo != null && $VehicleNo != '') {
            $check = $db->prepare("SELECT id FROM Vehicle WHERE veh_number = ? AND status = ?");
            $check->bind_param('ss', $VehicleNo, $status);
            $check->execute();
            $vehRow = $check->get_result()->fetch_assoc();
            $check->close();

            if (empty($vehRow)) {
                if ($insert_stmt = $db->prepare("INSERT INTO Vehicle (veh_number, vehicle_weight, customer_code, customer_name, supplier_code, supplier_name, vehicle_type, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssss', $VehicleNo, $VehicleWeight, $customerCode, $customerName, $supplierCode, $supplierName, $VehicleType, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }
            } else {
                $update_stmt = $db->prepare("UPDATE Vehicle SET vehicle_weight = ?, customer_code = ?, customer_name = ?, supplier_code = ?, supplier_name = ?, vehicle_type = ?, modified_by = ? WHERE id = ?");
                $update_stmt->bind_param('sssssssi', $VehicleWeight, $customerCode, $customerName, $supplierCode, $supplierName, $VehicleType, $uid, $vehRow['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
    }

    $db->close();

    if (!empty($errorSoProductArray)) {
        echo json_encode(array("status" => "error", "message" => $errorSoProductArray));
    } else {
        echo json_encode(array("status" => "success", "message" => "Added Successfully!!"));
    }
} else {
    echo json_encode(array("status" => "failed", "message" => "Please fill in all the fields"));
}
