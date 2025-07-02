<?php
## Database configuration
require_once 'db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " and (veh_number like '%".$searchValue."%' 
  or vehicle_weight like '%".$searchValue."%' 
  or transporter_code like '%".$searchValue."%'
  or customer_code like '%".$searchValue."%'
  )";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from Vehicle");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from Vehicle WHERE status IN (0,1)".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from Vehicle WHERE status IN (0,1)".$searchQuery."order by status ASC, ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "id"=>$row['id'],
      "veh_number"=>$row['veh_number'],
      "vehicle_weight"=>$row['vehicle_weight'],
      "ex_del"=>$row['ex_del'],
      "transporter_name"=>$row['transporter_name'],
      "customer_name"=>$row['customer_name'],
      "status"=>(($row['status'] == '0') ? 'Active' : 'Inactive')
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>