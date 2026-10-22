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
// Weighing export in the Alam Flora layout (weights in tonnes). Used by export.php (Excel) and exportPdf.php (print).
// $rows are Weight / Weight_Container rows. Returns the headers, one assoc row per weighing (keyed by header),
// the totals (summed columns only, others blank) and the per-waste summary.
function weightExportLayout($db, $rows) {
    $gates = array();
    $gateQuery = $db->query("SELECT id, location_code FROM Location");
    while ($gate = $gateQuery->fetch_assoc()) {
        $gates[$gate['id']] = $gate['location_code'];
    }

    $vehicles = array();
    $vehicleQuery = $db->query("SELECT veh_number, vehicle_type, bin_no, bin_type FROM Vehicle WHERE status = '0'");
    while ($vehicle = $vehicleQuery->fetch_assoc()) {
        $vehicles[$vehicle['veh_number']] = $vehicle;
    }

    // Deposit movements are logged with remark 'Weighing <transaction_id>' (see DepositService::settle)
    $deposits = array();
    $remarks = array();
    foreach ($rows as $row) {
        $remarks[] = "'" . $db->real_escape_string('Weighing ' . $row['transaction_id']) . "'";
    }
    $depositQuery = empty($remarks) ? false : $db->query("SELECT remark, balance_before, balance_after FROM Deposit_Transaction WHERE remark IN (" . implode(',', $remarks) . ") ORDER BY id ASC");
    while ($depositQuery && $deposit = $depositQuery->fetch_assoc()) {
        if (!isset($deposits[$deposit['remark']])) {
            $deposits[$deposit['remark']] = array('before' => $deposit['balance_before']);
        }
        $deposits[$deposit['remark']]['after'] = $deposit['balance_after'];
    }

    $headers = array('TRANSACTION ID', 'SUPPLIER CODE', 'SUPPLIER NAME', 'VEHICLE NO.', 'VEHICLE TYPE', 'WASTE CODE', 'WASTE NAME', 'BIN NO', 'BIN TYPE',
        'GATE IN', 'DATE IN', 'TIME IN', 'WEIGHT IN (MT)', 'GATE OUT', 'DATE OUT', 'TIME OUT', 'WEIGHT OUT (MT)', 'NETT WEIGHT (MT)',
        'DEPOSIT (RM)', 'UNIT PRICE (RM)', 'SUB TOTAL PRICE (RM)', 'BALANCE CREDIT (RM)', 'DURATION', 'REMARKS', 'USER ID');
    $summedColumns = array('WEIGHT IN (MT)', 'WEIGHT OUT (MT)', 'NETT WEIGHT (MT)', 'SUB TOTAL PRICE (RM)', 'BALANCE CREDIT (RM)');

    $lines = array();
    $totals = array_fill_keys($headers, '');
    foreach ($summedColumns as $column) {
        $totals[$column] = 0.0;
    }
    $summary = array();

    foreach ($rows as $row) {
        $isSalesOrMisc = $row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc';
        $vehicle = $vehicles[$row['lorry_plate_no1']] ?? array();
        $deposit = $deposits['Weighing ' . $row['transaction_id']] ?? null;

        // Duration from gross to tare capture, H:MM
        $duration = '';
        if (!empty($row['gross_weight1_date']) && !empty($row['tare_weight1_date'])) {
            $minutes = intdiv(max(0, strtotime($row['tare_weight1_date']) - strtotime($row['gross_weight1_date'])), 60);
            $duration = sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
        }

        $line = array(
            'TRANSACTION ID' => $row['transaction_id'],
            'SUPPLIER CODE' => $isSalesOrMisc ? $row['customer_code'] : $row['supplier_code'],
            'SUPPLIER NAME' => $isSalesOrMisc ? $row['customer_name'] : $row['supplier_name'],
            'VEHICLE NO.' => $row['lorry_plate_no1'],
            'VEHICLE TYPE' => $vehicle['vehicle_type'] ?? '',
            'WASTE CODE' => $isSalesOrMisc ? $row['product_code'] : $row['raw_mat_code'],
            'WASTE NAME' => $isSalesOrMisc ? $row['product_name'] : $row['raw_mat_name'],
            'BIN NO' => $vehicle['bin_no'] ?? '',
            'BIN TYPE' => $vehicle['bin_type'] ?? '',
            'GATE IN' => $gates[$row['location_id']] ?? '',
            'DATE IN' => empty($row['gross_weight1_date']) ? '' : date('d/m/Y', strtotime($row['gross_weight1_date'])),
            'TIME IN' => empty($row['gross_weight1_date']) ? '' : date('h:i:s A', strtotime($row['gross_weight1_date'])),
            'WEIGHT IN (MT)' => (float) $row['gross_weight1'] / 1000,
            'GATE OUT' => $gates[$row['location_id2']] ?? '',
            'DATE OUT' => empty($row['tare_weight1_date']) ? '' : date('d/m/Y', strtotime($row['tare_weight1_date'])),
            'TIME OUT' => empty($row['tare_weight1_date']) ? '' : date('h:i:s A', strtotime($row['tare_weight1_date'])),
            'WEIGHT OUT (MT)' => (float) $row['tare_weight1'] / 1000,
            'NETT WEIGHT (MT)' => (float) $row['final_weight'] / 1000,
            'DEPOSIT (RM)' => $deposit ? (float) $deposit['before'] : '',
            'UNIT PRICE (RM)' => $row['unit_price'] === null || $row['unit_price'] === '' ? '' : (float) $row['unit_price'],
            'SUB TOTAL PRICE (RM)' => (float) $row['sub_total'],
            'BALANCE CREDIT (RM)' => $deposit ? (float) $deposit['after'] : '',
            'DURATION' => $duration,
            'REMARKS' => $row['remarks'],
            'USER ID' => $row['created_by'],
        );
        $lines[] = $line;

        foreach ($summedColumns as $column) {
            $totals[$column] += (float) $line[$column];
        }

        $name = $line['WASTE NAME'];
        if (!isset($summary[$name])) {
            $summary[$name] = array('name' => $name, 'trips' => 0, 'tonnage' => 0.0, 'price' => 0.0);
        }
        $summary[$name]['trips']++;
        $summary[$name]['tonnage'] += (float) $line['NETT WEIGHT (MT)'];
        $summary[$name]['price'] += (float) $line['SUB TOTAL PRICE (RM)'];
    }

    $summaryTotal = array('trips' => 0, 'tonnage' => 0.0, 'price' => 0.0);
    foreach ($summary as $item) {
        $summaryTotal['trips'] += $item['trips'];
        $summaryTotal['tonnage'] += $item['tonnage'];
        $summaryTotal['price'] += $item['price'];
    }

    return array('headers' => $headers, 'rows' => $lines, 'totals' => $totals, 'summary' => array_values($summary), 'summaryTotal' => $summaryTotal);
}

// Label shown in the export title row for a transaction_status value
function weightExportStatusLabel($status) {
    $labels = array('Sales' => 'DISPATCH', 'Purchase' => 'RECEIVING', 'Local' => 'INTERNAL TRANSFER', 'Misc' => 'MISCELLANEOUS');
    return $labels[$status] ?? 'ALL';
}

?>
