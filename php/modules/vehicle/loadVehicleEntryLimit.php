<?php
session_start();
## Database configuration
require_once '../../db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("draw" => 0, "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => array()));
    exit;
}

## Read value
$draw = $_POST['draw'];
$row = intval($_POST['start']);
$rowperpage = intval($_POST['length']);
$columnIndex = $_POST['order'][0]['column'];
$columnName = $_POST['columns'][$columnIndex]['data'];
$columnSortOrder = $_POST['order'][0]['dir'] == 'asc' ? 'asc' : 'desc';
$searchValue = mysqli_real_escape_string($db, $_POST['search']['value']);
$vehicleId = empty($_POST['vehicleId']) ? 0 : intval($_POST['vehicleId']);

// Only sort on known columns
$sortColumns = array('limit_date', 'max_entry', 'status', 'created_by', 'modified_by', 'modified_date');
if (!in_array($columnName, $sortColumns)) {
    $columnName = 'limit_date';
}

## Search
$searchQuery = " ";
if ($searchValue != '') {
    $searchQuery = " and (max_entry like '%".$searchValue."%'
    or created_by like '%".$searchValue."%'
    or modified_by like '%".$searchValue."%'
    )";
}

## Total number of records without filtering
$sel = mysqli_query($db, "select count(*) as allcount from Vehicle_Entry_Limit WHERE vehicle_id = ".$vehicleId);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db, "select count(*) as allcount from Vehicle_Entry_Limit WHERE vehicle_id = ".$vehicleId.$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from Vehicle_Entry_Limit WHERE vehicle_id = ".$vehicleId.$searchQuery." order by ".$columnName." ".$columnSortOrder.", id desc limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "id"            => $row['id'],
        "limit_date"    => date('d/m/Y', strtotime($row['limit_date'])),
        "max_entry"     => $row['max_entry'],
        "status"        => (($row['status'] == '0') ? 'Active' : 'Deleted'),
        "created_by"    => $row['created_by'],
        "modified_by"   => $row['modified_by'],
        "modified_date" => date('d/m/Y H:i:s', strtotime($row['modified_date']))
    );
}

## Response
$response = array(
    "draw"                 => intval($draw),
    "iTotalRecords"        => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData"               => $data
);

echo json_encode($response);
?>
