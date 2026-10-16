<?php
session_start();
require_once '../../db_connect.php';
require_once 'vehicleEntry.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("status" => "failed", "message" => "Session expired, please login again"));
    exit;
}

// Same roles that can see Master Data in layouts/sidebar.php
if (!in_array($_SESSION['roles'], array('MANAGER', 'ADMIN', 'SADMIN'))) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to set the entry limit"));
    exit;
}

$username = $_SESSION['username'];
$vehicleId = empty($_POST['id']) ? 0 : intval($_POST['id']);
$limitDateInput = empty($_POST['limitDate']) ? date('d-m-Y') : trim($_POST['limitDate']);
$limitDateTime = DateTime::createFromFormat('d-m-Y', $limitDateInput);

if ($vehicleId <= 0) {
    echo json_encode(array("status" => "failed", "message" => "Vehicle not found"));
    exit;
}

if (!$limitDateTime || $limitDateTime->format('d-m-Y') != $limitDateInput) {
    echo json_encode(array("status" => "failed", "message" => "Invalid date"));
    exit;
}

$limitDate = $limitDateTime->format('Y-m-d');

// Load vehicle
$vehicle = null;

if ($stmt = $db->prepare("SELECT id, veh_number, max_entry_per_day FROM Vehicle WHERE id = ? AND status = '0'")) {
    $stmt->bind_param('s', $vehicleId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $vehicle = $result->fetch_assoc();
    }

    $stmt->close();
}

if (empty($vehicle)) {
    echo json_encode(array("status" => "failed", "message" => "Vehicle not found"));
    exit;
}

// Change history of one date limit record (expandable row in the modal)
if (isset($_POST['mode']) && $_POST['mode'] == 'logs') {
    $limitId = empty($_POST['limitId']) ? 0 : intval($_POST['limitId']);
    $logs = array();

    if ($stmt = $db->prepare("SELECT * FROM Vehicle_Entry_Limit_Log WHERE limit_id = ? AND vehicle_id = ? ORDER BY id ASC")) {
        $stmt->bind_param('ss', $limitId, $vehicleId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $action = 'Update';

                if ($row['action_id'] == '1') {
                    $action = 'Create';
                }
                else if ($row['action_id'] == '3') {
                    $action = 'Delete';
                }

                $logs[] = array(
                    "action" => $action,
                    "limit_date" => date('d/m/Y', strtotime($row['limit_date'])),
                    "max_entry" => $row['max_entry'],
                    "action_by" => $row['action_by'],
                    "event_date" => date('d/m/Y H:i:s', strtotime($row['event_date']))
                );
            }
        }

        $stmt->close();
    }

    echo json_encode(array("status" => "success", "message" => $logs));
    $db->close();
    exit;
}

if (isset($_POST['mode']) && $_POST['mode'] == 'get') {
    $dateLimitRow = getVehicleDateLimitRow($db, $vehicleId, $limitDate);
    $currentDateMax = empty($dateLimitRow) ? null : (int)$dateLimitRow['max_entry'];
    $entryStatus = getVehicleEntryStatus($db, $vehicle['veh_number'], $limitDate);

    echo json_encode(array(
        "status" => "success",
        "message" => array(
            "id" => $vehicle['id'],
            "veh_number" => $vehicle['veh_number'],
            "max_entry_per_day" => $vehicle['max_entry_per_day'] === null ? null : (int)$vehicle['max_entry_per_day'],
            "date_max_entry" => $currentDateMax,
            "max" => $entryStatus['max'],
            "count" => $entryStatus['count']
        )
    ));
    $db->close();
    exit;
}

// Save
$maxEntryInput = isset($_POST['maxEntryPerDay']) ? trim($_POST['maxEntryPerDay']) : '';
$dateMaxInput = isset($_POST['dateMaxEntry']) ? trim($_POST['dateMaxEntry']) : '';

if ($maxEntryInput !== '' && !ctype_digit($maxEntryInput)) {
    echo json_encode(array("status" => "failed", "message" => "Max Entry / Day must be blank or 0 or more"));
    exit;
}

if ($dateMaxInput !== '' && !ctype_digit($dateMaxInput)) {
    echo json_encode(array("status" => "failed", "message" => "Max entry for the date must be blank or 0 or more"));
    exit;
}

$maxEntryPerDay = $maxEntryInput === '' ? null : (int)$maxEntryInput;
$dateMax = $dateMaxInput === '' ? null : (int)$dateMaxInput;
$saved = true;

$db->begin_transaction();

// Lock the vehicle row so two saves for the same vehicle can't both insert a date limit
if ($stmt = $db->prepare("SELECT max_entry_per_day FROM Vehicle WHERE id = ? FOR UPDATE")) {
    $stmt->bind_param('s', $vehicleId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $vehicle['max_entry_per_day'] = $row['max_entry_per_day'];
        }
    } else {
        $saved = false;
    }

    $stmt->close();
} else {
    $saved = false;
}

$dateLimitRow = getVehicleDateLimitRow($db, $vehicleId, $limitDate);
$currentDateMax = empty($dateLimitRow) ? null : (int)$dateLimitRow['max_entry'];

// Default daily limit
$currentDefault = $vehicle['max_entry_per_day'] === null ? null : (int)$vehicle['max_entry_per_day'];

if ($saved && $maxEntryPerDay !== $currentDefault) {
    if ($stmt = $db->prepare("UPDATE Vehicle SET max_entry_per_day = ?, modified_by = ? WHERE id = ?")) {
        $stmt->bind_param('sss', $maxEntryPerDay, $username, $vehicleId);
        $saved = $stmt->execute();
        $stmt->close();
    } else {
        $saved = false;
    }
}

// Date limit: edit the active row, soft delete it when blank, or insert a new row.
// Vehicle_Entry_Limit_Log is written by the TRG_INS/TRG_UPD_VEH_ENTRY_LIMIT triggers.
if ($saved && $dateMax !== $currentDateMax) {
    if (!empty($dateLimitRow) && $dateMax === null) {
        $stmt = $db->prepare("UPDATE Vehicle_Entry_Limit SET status = '1', modified_by = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('ss', $username, $dateLimitRow['id']);
        }
    }
    else if (!empty($dateLimitRow)) {
        $stmt = $db->prepare("UPDATE Vehicle_Entry_Limit SET max_entry = ?, modified_by = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('sss', $dateMax, $username, $dateLimitRow['id']);
        }
    }
    else {
        $stmt = $db->prepare("INSERT INTO Vehicle_Entry_Limit (vehicle_id, veh_number, limit_date, max_entry, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('ssssss', $vehicleId, $vehicle['veh_number'], $limitDate, $dateMax, $username, $username);
        }
    }

    if ($stmt) {
        $saved = $stmt->execute();
        $stmt->close();
    } else {
        $saved = false;
    }

}

if ($saved) {
    $db->commit();
    echo json_encode(array("status" => "success", "message" => "Entry limit saved"));
} else {
    $db->rollback();
    echo json_encode(array("status" => "failed", "message" => "Failed to save entry limit"));
}

$db->close();
?>
