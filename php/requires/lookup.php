<?php
require_once __DIR__ . '/permissions.php';
function convertDatetimeToDate($datetime){
    $date = new DateTime($datetime);
  
    return $date->format('d/m/Y'); 
}

function searchPlantCodeById($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['plant_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameById($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE plant_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProjectByCode($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Site WHERE site_code=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchTransporterNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Transporter WHERE transporter_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchAgentNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Agents WHERE agent_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchDestinationCodeByName($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Destination WHERE name=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['destination_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchFilePathById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM files WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['filepath'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchNamebyId($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Users WHERE username=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchActionNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Log_Action WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['description'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchLocationNameById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Location WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['location_name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchCustomerByCode($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row;
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchSupplierByCode($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row;
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchContractorById($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Contractor WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row;
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function excelSerialToDate($serial) {
    // Excel date starts from 1900-01-01, subtract 1 for correct calculation
    $baseDate = strtotime('1899-12-30');
    return date('Y-m-d', strtotime("+$serial days", $baseDate));
}
// Report permissions: transaction status uses the Reports module, weight type uses the Weighing module
function reportStatusModules() {
    return array('Sales' => 'Sales', 'Purchase' => 'Purchase', 'Local' => 'Internal Transfer', 'Misc' => 'Miscellaneous');
}

function reportWeightTypeModules() {
    return array('Normal' => 'Normal Type', 'Container' => 'Container Type', 'Empty Container' => 'Empty Container Type', 'Different Container' => 'Different Container Type');
}

function reportStatusPermitted($status, $action = 'view') {
    $modules = reportStatusModules();
    return isset($modules[$status]) && hasModulePermission('Reports', $modules[$status], [$action]);
}

function reportWeightTypePermitted($weightType) {
    $modules = reportWeightTypeModules();
    return isset($modules[$weightType]) && hasModulePermission('Weighing', $modules[$weightType], ['view']);
}

// SQL clause that keeps only the statuses and weight types the user may view (skipped when all are allowed)
function reportPermissionExclusion($statusColumn, $weightTypeColumn, $statusAction = 'view') {
    $sql = '';
    $statuses = array_values(array_filter(array_keys(reportStatusModules()), function ($s) use ($statusAction) {
        return reportStatusPermitted($s, $statusAction);
    }));
    if (count($statuses) < count(reportStatusModules())) {
        $sql .= empty($statuses) ? " and 1 = 0" : " and {$statusColumn} in ('" . implode("', '", $statuses) . "')";
    }
    $weightTypes = array_values(array_filter(array_keys(reportWeightTypeModules()), 'reportWeightTypePermitted'));
    if (count($weightTypes) < count(reportWeightTypeModules())) {
        $sql .= empty($weightTypes) ? " and 1 = 0" : " and {$weightTypeColumn} in ('" . implode("', '", $weightTypes) . "')";
    }
    return $sql;
}

function transactionStatusFeatures() {
    return array(
        'Sales' => 'transaction_dispatch',
        'Purchase' => 'transaction_receiving',
        'Local' => 'transaction_internal_transfer',
        'Misc' => 'transaction_miscellaneous'
    );
}

function isTransactionStatusEnabled($status, $db) {
    $featureCodes = transactionStatusFeatures();
    if (!isset($featureCodes[$status])) {
        return true;
    }
    require_once __DIR__ . '/../classes/BaseController.php';
    $baseController = new BaseController($db);
    return $baseController->isFeatureEnabled($featureCodes[$status]);
}

// SQL clause that keeps only the statuses whose feature is enabled
function transactionStatusExclusion($db, $column) {
    $features = transactionStatusFeatures();
    $enabled = array();
    foreach ($features as $status => $featureCode) {
        if (isTransactionStatusEnabled($status, $db)) {
            $enabled[] = "'" . $status . "'";
        }
    }
    if (count($enabled) == count($features)) {
        return '';
    }
    if (empty($enabled)) {
        return " and 1 = 0";
    }
    return " and " . $column . " IN (" . implode(', ', $enabled) . ")";
}
?>
