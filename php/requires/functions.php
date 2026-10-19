<?php
// Feature flag and permission helpers used by the report, weighing and master data screens
require_once __DIR__ . '/permissions.php';

// Permission module names for each transaction status (stored value => module name)
function transactionStatusModules() {
    return array('Sales' => 'Sales', 'Purchase' => 'Purchase', 'Local' => 'Internal Transfer', 'Misc' => 'Miscellaneous');
}

// Permission module names for each weight type (stored value => module name, always under Weighing)
function weightTypeModules() {
    return array('Normal' => 'Normal Type', 'Container' => 'Container Type', 'Empty Container' => 'Empty Container Type', 'Different Container' => 'Different Container Type');
}

// Transaction status permission in a category: 'Reports' for the report screen, 'Weighing' for the weighing screen
function statusPermitted($category, $status, $action = 'view') {
    $modules = transactionStatusModules();
    $actions = is_array($action) ? $action : array($action);
    return isset($modules[$status]) && hasModulePermission($category, $modules[$status], $actions);
}

function weightTypePermitted($weightType, $action = 'view') {
    $modules = weightTypeModules();
    $actions = is_array($action) ? $action : array($action);
    return isset($modules[$weightType]) && hasModulePermission('Weighing', $modules[$weightType], $actions);
}

// SQL clause that keeps only the statuses (in $statusCategory) and weight types the user may act on; empty when all are allowed
function permissionExclusion($statusCategory, $statusColumn, $weightTypeColumn, $statusAction = 'view') {
    $sql = '';
    $statuses = array_values(array_filter(array_keys(transactionStatusModules()), function ($s) use ($statusCategory, $statusAction) {
        return statusPermitted($statusCategory, $s, $statusAction);
    }));
    if (count($statuses) < count(transactionStatusModules())) {
        $sql .= empty($statuses) ? " and 1 = 0" : " and {$statusColumn} in ('" . implode("', '", $statuses) . "')";
    }
    $weightTypes = array_values(array_filter(array_keys(weightTypeModules()), function ($w) {
        return weightTypePermitted($w);
    }));
    if (count($weightTypes) < count(weightTypeModules())) {
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
