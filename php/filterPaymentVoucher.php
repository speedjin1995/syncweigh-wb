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

$allQuery = "select MAX(id) as id from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' AND transaction_status = '".$_POST['transactionStatus']."' AND customer_code IS NOT NULL AND transaction_date IS NOT NULL group by DATE(transaction_date), customer_code";
if($_POST['transactionStatus'] == 'Purchase'){
	$allQuery = "select MAX(w.id) as id from Weight w INNER JOIN Supplier s ON w.supplier_code = s.supplier_code where w.is_complete = 'Y' AND w.is_cancel <> 'Y' AND w.transaction_status = '".$_POST['transactionStatus']."' AND w.supplier_code IS NOT NULL AND w.transaction_date IS NOT NULL AND s.payment_term = 'Term' group by DATE(w.transaction_date), w.supplier_code";
}
$sel = mysqli_query($db, $allQuery); 
$totalRecords = mysqli_num_rows($sel);
## Total number of record with filtering
$filteredQuery = "select MAX(id) as id from Weight where is_complete = 'Y' AND is_cancel <> 'Y' AND customer_code IS NOT NULL AND transaction_date IS NOT NULL".$searchQuery." group by DATE(transaction_date), customer_code";
if($_POST['transactionStatus'] == 'Purchase'){
	$filteredQuery = "select MAX(w.id) as id from Weight w INNER JOIN Supplier s ON w.supplier_code = s.supplier_code where w.is_complete = 'Y' AND w.is_cancel <> 'Y' AND w.supplier_code IS NOT NULL AND w.transaction_date IS NOT NULL AND s.payment_term = 'Term'".$searchQuery." group by DATE(w.transaction_date), w.supplier_code";
}
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = mysqli_num_rows($sel);

## Fetch records
$empQuery = "select MAX(w.id) as id, w.transaction_status, w.weight_type, MAX(w.customer_name) as customer_name, MAX(w.supplier_name) as supplier_name, MAX(w.customer_code) as customer_code, MAX(w.invoice_no) as invoice_no, MAX(w.transaction_date) as transaction_date, MAX(pv.voucher_no) as voucher_no, pv.outstanding_amount as outstanding_amount from Weight w LEFT JOIN Payment_Voucher pv ON DATE(w.transaction_date) = DATE(pv.voucher_date) AND w.customer_name = pv.customer_supplier where w.is_complete = 'Y' AND w.is_cancel <> 'Y' AND w.customer_code IS NOT NULL AND w.transaction_date IS NOT NULL".$searchQuery." group by DATE(w.transaction_date), w.customer_code order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
if($_POST['transactionStatus'] == 'Purchase'){
	$empQuery = "select MAX(w.id) as id, w.transaction_status, w.weight_type, MAX(w.customer_name) as customer_name, MAX(w.supplier_name) as supplier_name, MAX(w.supplier_code) as supplier_code, MAX(w.invoice_no) as invoice_no, MAX(w.transaction_date) as transaction_date, MAX(pv.voucher_no) as voucher_no, pv.outstanding_amount as outstanding_amount from Weight w INNER JOIN Supplier s ON w.supplier_code = s.supplier_code LEFT JOIN Payment_Voucher pv ON DATE(w.transaction_date) = DATE(pv.voucher_date) AND w.supplier_name = pv.customer_supplier where w.is_complete = 'Y' AND w.is_cancel <> 'Y' AND w.supplier_code IS NOT NULL AND w.transaction_date IS NOT NULL AND s.payment_term = 'Term'".$searchQuery." group by DATE(w.transaction_date), w.supplier_code order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
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
    "transaction_date"=>date('d-m-Y', strtotime($row['transaction_date'])),
    "voucher_no"=>$row['voucher_no'] ?? '',
    "outstanding_amount"=>(!empty($row['outstanding_amount']) ? number_format(floatval($row['outstanding_amount']), 2) : ''),
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