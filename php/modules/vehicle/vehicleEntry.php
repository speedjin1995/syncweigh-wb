<?php
// Vehicle daily entry limit helpers. Functions only: the caller owns the session and $db.
// Limit for a date = active Vehicle_Entry_Limit row for that date, else Vehicle.max_entry_per_day.
// NULL = no limit, 0 = blocked (no entry allowed without the password override).
// Vehicle_Entry_Log only holds IN (new weighing) and REVERSE (weighing cancelled) rows.
// Count for a date = IN minus REVERSE rows.

// Active limit row whose date range covers the date, or null. No unique key on the table (an
// overlapping range can be imported later), so the latest active row wins.
function getVehicleDateLimitRow($db, $vehicleId, $date){
    $row = null;

    if ($stmt = $db->prepare("SELECT id, max_entry FROM Vehicle_Entry_Limit WHERE vehicle_id = ? AND start_date <= ? AND end_date >= ? AND status = '0' ORDER BY id DESC LIMIT 1")) {
        $stmt->bind_param('sss', $vehicleId, $date, $date);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        $stmt->close();
    }

    return $row;
}

function getVehicleEntryLimit($db, $vehicleId, $defaultMax, $date){
    $row = getVehicleDateLimitRow($db, $vehicleId, $date);

    return empty($row) ? ($defaultMax === null ? null : (int)$defaultMax) : (int)$row['max_entry'];
}

function getVehicleEntryCount($db, $vehicleId, $date){
    $count = 0;

    if ($stmt = $db->prepare("SELECT COALESCE(SUM(CASE WHEN action = 'IN' THEN 1 WHEN action = 'REVERSE' THEN -1 ELSE 0 END), 0) AS entry_count FROM Vehicle_Entry_Log WHERE vehicle_id = ? AND entry_date = ?")) {
        $stmt->bind_param('ss', $vehicleId, $date);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $count = max(0, (int)$row['entry_count']);
            }
        }

        $stmt->close();
    }

    return $count;
}

// Returns null when the plate is not in the Vehicle master (manual plates have no limit).
function getVehicleEntryStatus($db, $vehNumber, $date){
    if (empty($vehNumber)) {
        return null;
    }

    $vehicle = null;

    if ($stmt = $db->prepare("SELECT id, veh_number, max_entry_per_day FROM Vehicle WHERE veh_number = ? AND status = '0' LIMIT 1")) {
        $stmt->bind_param('s', $vehNumber);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $vehicle = $result->fetch_assoc();
        }

        $stmt->close();
    }

    if (empty($vehicle)) {
        return null;
    }

    $max = getVehicleEntryLimit($db, $vehicle['id'], $vehicle['max_entry_per_day'], $date);
    $count = getVehicleEntryCount($db, $vehicle['id'], $date);

    return array(
        "vehicle_id" => $vehicle['id'],
        "veh_number" => $vehicle['veh_number'],
        "date" => $date,
        "max" => $max,
        "count" => $count,
        "limited" => $max !== null,
        "exceeded" => $max !== null && $count >= $max
    );
}

// $fields: vehicle_id, veh_number, entry_date, action (IN / REVERSE), max_entry, action_by and optionally
// weight_id, weight_table, transaction_id, is_override, approved_by, approved_date, remarks.
function logVehicleEntry($db, $fields){
    $count = getVehicleEntryCount($db, $fields['vehicle_id'], $fields['entry_date']);

    if ($fields['action'] == 'IN') {
        $count++;
    }
    else if ($fields['action'] == 'REVERSE') {
        $count = max(0, $count - 1);
    }

    $weightId = $fields['weight_id'] ?? null;
    $weightTable = $fields['weight_table'] ?? null;
    $transactionId = $fields['transaction_id'] ?? null;
    $isOverride = $fields['is_override'] ?? 'N';
    $approvedBy = $fields['approved_by'] ?? null;
    $approvedDate = $fields['approved_date'] ?? null;
    $remarks = $fields['remarks'] ?? null;
    $eventDate = date('Y-m-d H:i:s');

    if ($stmt = $db->prepare("INSERT INTO Vehicle_Entry_Log (vehicle_id, veh_number, entry_date, action, weight_id, weight_table, transaction_id, max_entry, entry_count, is_override, approved_by, approved_date, remarks, action_by, event_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $stmt->bind_param('sssssssssssssss', $fields['vehicle_id'], $fields['veh_number'], $fields['entry_date'], $fields['action'], $weightId, $weightTable, $transactionId,
        $fields['max_entry'], $count, $isOverride, $approvedBy, $approvedDate, $remarks, $fields['action_by'], $eventDate);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    return false;
}

// $entryStatus is the result of getVehicleEntryStatus() taken before the insert.
function logVehicleEntryIn($db, $entryStatus, $weightTable, $weightId, $transactionId, $isOverride, $username){
    if (empty($entryStatus)) {
        return true;
    }

    return logVehicleEntry($db, array(
        "vehicle_id" => $entryStatus['vehicle_id'],
        "veh_number" => $entryStatus['veh_number'],
        "entry_date" => $entryStatus['date'],
        "action" => 'IN',
        "weight_id" => $weightId,
        "weight_table" => $weightTable,
        "transaction_id" => $transactionId,
        "max_entry" => $entryStatus['max'],
        "is_override" => $isOverride ? 'Y' : 'N',
        "approved_by" => $isOverride ? $username : null,
        "approved_date" => $isOverride ? date('Y-m-d H:i:s') : null,
        "action_by" => $username
    ));
}

// Latest IN row for a weighing record, or null when the record was never counted.
function getVehicleEntryInRow($db, $weightTable, $weightId){
    $row = null;

    if ($stmt = $db->prepare("SELECT * FROM Vehicle_Entry_Log WHERE weight_table = ? AND weight_id = ? AND action = 'IN' ORDER BY id DESC LIMIT 1")) {
        $stmt->bind_param('ss', $weightTable, $weightId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        $stmt->close();
    }

    return $row;
}

function hasVehicleEntryAction($db, $weightTable, $weightId, $action){
    $found = false;

    if ($stmt = $db->prepare("SELECT id FROM Vehicle_Entry_Log WHERE weight_table = ? AND weight_id = ? AND action = ? LIMIT 1")) {
        $stmt->bind_param('sss', $weightTable, $weightId, $action);

        if ($stmt->execute()) {
            $stmt->store_result();
            $found = $stmt->num_rows > 0;
        }

        $stmt->close();
    }

    return $found;
}

// Gives the entry back when weighing records are cancelled. $ids is an id, a comma list or an array.
function reverseVehicleEntry($db, $weightTable, $ids, $username, $reason){
    if (!is_array($ids)) {
        $ids = explode(',', (string)$ids);
    }

    foreach ($ids as $weightId) {
        $weightId = intval($weightId);

        if ($weightId <= 0) {
            continue;
        }

        $inRow = getVehicleEntryInRow($db, $weightTable, $weightId);

        if (empty($inRow) || hasVehicleEntryAction($db, $weightTable, $weightId, 'REVERSE')) {
            continue;
        }

        logVehicleEntry($db, array(
            "vehicle_id" => $inRow['vehicle_id'],
            "veh_number" => $inRow['veh_number'],
            "entry_date" => $inRow['entry_date'],
            "action" => 'REVERSE',
            "weight_id" => $weightId,
            "weight_table" => $weightTable,
            "transaction_id" => $inRow['transaction_id'],
            "max_entry" => $inRow['max_entry'],
            "remarks" => $reason,
            "action_by" => $username
        ));
    }
}

// IN / REVERSE rows of one weighing record, for the weighing screen expandable row
function getVehicleEntryLogs($db, $weightTable, $weightId){
    $logs = array();

    if ($stmt = $db->prepare("SELECT * FROM Vehicle_Entry_Log WHERE weight_table = ? AND weight_id = ? ORDER BY id ASC")) {
        $stmt->bind_param('ss', $weightTable, $weightId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $logs[] = array(
                    "action" => $row['action'],
                    "veh_number" => $row['veh_number'],
                    "entry_date" => date('d/m/Y', strtotime($row['entry_date'])),
                    "entry_count" => $row['entry_count'],
                    "max_entry" => $row['max_entry'],
                    "is_override" => $row['is_override'],
                    "approved_by" => $row['approved_by'] ?? '',
                    "approved_date" => empty($row['approved_date']) ? '' : date('d/m/Y H:i:s', strtotime($row['approved_date'])),
                    "remarks" => $row['remarks'] ?? '',
                    "action_by" => $row['action_by'] ?? '',
                    "event_date" => date('d/m/Y H:i:s', strtotime($row['event_date']))
                );
            }
        }

        $stmt->close();
    }

    return $logs;
}

// Re-confirms the logged-in user's own password (same check as php/login.php).
function verifyUserPassword($db, $userId, $password){
    if (empty($userId) || $password === null || $password === '') {
        return false;
    }

    $valid = false;

    if ($stmt = $db->prepare("SELECT password FROM Users WHERE id = ?")) {
        $stmt->bind_param('s', $userId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $valid = password_verify($password, $row['password']);
            }
        }

        $stmt->close();
    }

    return $valid;
}
?>
