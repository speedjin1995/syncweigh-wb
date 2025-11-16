<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
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
  $searchQuery = " and (
    cust.customer_code like '%".$searchValue."%' OR 
    cust.name like '%".$searchValue."%' OR
    c.name like '%".$searchValue."%'
  )";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from Customer");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount, c.name as company, pl.name as plant from Customer cust LEFT JOIN Company c ON cust.company_id = c.id LEFT JOIN Plant pl ON cust.plant_id = pl.id WHERE cust.status IN (0)".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT cust.*, c.name as company, pl.name as plant FROM Customer cust LEFT JOIN Company c ON cust.company_id = c.id LEFT JOIN Plant pl ON cust.plant_id = pl.id WHERE cust.status IN (0)".$searchQuery." ORDER BY cust.status ".$columnSortOrder;

if($columnName == 'company'){
  $empQuery .= ", c.name ".$columnSortOrder;
} elseif($columnName == 'plant'){
  $empQuery .= ", pl.name ".$columnSortOrder;
} else {
  $empQuery .= ", cust.".$columnName." ".$columnSortOrder;
}
$empQuery .= " LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "id"=>$row['id'],
      "customer_code"=>$row['customer_code'],
      "name"=>$row['name'],
      "company_reg_no"=>$row['company_reg_no'],
      "new_reg_no"=>$row['new_reg_no'],
      "address_line_1"=>$row['address_line_1'],
      "address_line_2"=>$row['address_line_2'],
      "address_line_3"=>$row['address_line_3'],
      "address_line_4"=>$row['address_line_4'],
      "phone_no"=>$row['phone_no'],
      "fax_no"=>$row['fax_no'],
      "contact_name"=>$row['contact_name'],
      "ic_no"=>$row['ic_no'],
      "tin_no"=>$row['tin_no'],
      "company"=>$row['company'] ?? '',
      "plant_id"=>$row['plant_id'],
      "status"=>$row['status']
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