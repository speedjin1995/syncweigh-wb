<?php
## Database configuration
session_start();
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
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery .= " and transaction_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and transaction_date <= '".$toDateTime."'";
}

if($_POST['weighingType'] != null && $_POST['weighingType'] != '' && $_POST['weighingType'] != '-'){
	$searchQuery .= " and weight_type = '".$_POST['weighingType']."'";
}
if($_POST['transactionStatus'] != null && $_POST['transactionStatus'] != '' && $_POST['transactionStatus'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['transactionStatus']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier_code = '".$_POST['supplier']."'";
}

if($_POST['invoiceNo'] != null && $_POST['invoiceNo'] != '' && $_POST['invoiceNo'] != '-'){
  $searchQuery .= " and invoice_no = '".mysqli_real_escape_string($db, $_POST['invoiceNo'])."'";
}

if($searchValue != ''){
  $searchQuery = " and (transaction_id like '%".$searchValue."%' or lorry_plate_no1 like '%".$searchValue."%')";
}

$allQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND transaction_status = '".$_POST['transactionStatus']."' group by invoice_no, customer_code";
if($_POST['transactionStatus'] == 'Purchase'){
	$allQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND transaction_status = '".$_POST['transactionStatus']."' group by invoice_no, supplier_code";
}
$sel = mysqli_query($db, $allQuery); 
$totalRecords = mysqli_num_rows($sel);
## Total number of record with filtering
$filteredQuery = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by invoice_no, customer_code";
if($_POST['transactionStatus'] == 'Purchase'){
	$filteredQuery = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by invoice_no, supplier_code";
}
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = mysqli_num_rows($sel);

## Fetch records
$empQuery = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by invoice_no, customer_code order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
if($_POST['transactionStatus'] == 'Purchase'){
	$empQuery = "select * from Weight where is_complete = 'Y' AND is_cancel <> 'Y'".$searchQuery." group by invoice_no, supplier_code order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}
$empRecords = mysqli_query($db, $empQuery); 
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "transaction_status"=>$row['transaction_status'],
    "weight_type"=>$row['weight_type'],
    "customer"=>($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['customer_name'] : $row['supplier_name']),
    "invoice_no"=>$row['invoice_no'],
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "sql" => $empQuery
);

echo json_encode($response);

?>