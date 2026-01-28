<?php
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
$searchQuery2 = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and transaction_date >= '".$fromDateTime."'";
  $searchQuery2 .= " and transaction_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and transaction_date <= '".$toDateTime."'";
  $searchQuery2 .= " and transaction_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier_code = '".$_POST['supplier']."'";
}

if($_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
	$searchQuery .= " and lorry_plate_no1 like '%".$_POST['vehicle']."%'";
}

if($_POST['invoice'] != null && $_POST['invoice'] != '' && $_POST['invoice'] != '-'){
	$searchQuery .= " and weight_type = '".$_POST['invoice']."'";
}

if($_POST['batch'] != null && $_POST['batch'] != '' && $_POST['batch'] != '-'){
	$searchQuery .= " and is_complete = '".$_POST['batch']."' AND is_cancel = 'N'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_code = '".$_POST['product']."'";
}

if($_POST['rawMaterial'] != null && $_POST['rawMaterial'] != '' && $_POST['rawMaterial'] != '-'){
	$searchQuery .= " and raw_mat_code = '".$_POST['rawMaterial']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['transactionId'] != null && $_POST['transactionId'] != '' && $_POST['transactionId'] != '-'){
	$searchQuery .= " and transaction_id like '%".$_POST['transactionId']."%'";
}

if($_POST['containerNo'] != null && $_POST['containerNo'] != '' && $_POST['containerNo'] != '-'){
	$searchQuery .= " and (container_no like '%".$_POST['containerNo']."%' OR container_no2 like '%".$_POST['containerNo']."%')";
}

if($_POST['sealNo'] != null && $_POST['sealNo'] != '' && $_POST['sealNo'] != '-'){
	$searchQuery .= " and (seal_no like '%".$_POST['sealNo']."%' OR seal_no2 like '%".$_POST['sealNo']."%')";
}

if($_POST['invDelPo'] != null && $_POST['invDelPo'] != '' && $_POST['invDelPo'] != '-'){
	$searchQuery .= " and (purchase_order like '%".$_POST['invDelPo']."%' OR invoice_no like '%".$_POST['invDelPo']."%' OR delivery_no like '%".$_POST['invDelPo']."%')";
}

if($searchValue != ''){
  $searchQuery = " and (transaction_id like '%".$searchValue."%' or lorry_plate_no1 like '%".$searchValue."%')";
}

$salesPendingCount = 0;
$salesCompleteCount = 0;
$salesCancelCount = 0;
$purchasePendingCount = 0;
$purchaseCompleteCount = 0;
$purchaseCancelCount = 0;
$localPendingCount = 0;
$localCompleteCount = 0;
$localCancelCount = 0;
$miscPendingCount = 0;
$miscCompleteCount = 0;
$miscCancelCount = 0;

if ($_POST['batch'] == 'N') { //if pending
  ## Total number of records without filtering
  $allQuery = "select COUNT(*) as allcount FROM (SELECT * FROM Weight WHERE status = '0' UNION ALL SELECT * FROM Weight_Container WHERE status = '0') AS combined";
    
  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $allQuery = "select COUNT(*) as allcount FROM (SELECT * FROM Weight WHERE status = '0' and plant_code IN ('$username') UNION ALL SELECT * FROM Weight WHERE status = '0' and plant_code IN ('$username')) AS combined";
  }

  $sel = mysqli_query($db, $allQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecords = $records['allcount'];

  ## Total number of record with filtering
  $filteredQuery = "select count(*) as allcount from (SELECT * FROM Weight where status = '0'".$searchQuery." UNION ALL SELECT * FROM Weight_Container where status = '0'".$searchQuery.") AS combined"; 
  $filteredQuery2 = "select * from (SELECT * FROM Weight where status = '0'".$searchQuery2." UNION ALL SELECT * FROM Weight_Container where status = '0'".$searchQuery2.") AS combined"; 
  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $filteredQuery = "select count(*) as allcount from (SELECT * FROM Weight where status = '0' and plant_code IN ('$username')".$searchQuery." UNION ALL SELECT * FROM Weight_Container where status = '0' and plant_code IN ('$username')".$searchQuery.") AS combined";
    $filteredQuery2 = "select * from (SELECT * FROM Weight where status = '0' and plant_code IN ('$username')".$searchQuery2." UNION ALL SELECT * FROM Weight_Container where status = '0' and plant_code IN ('$username')".$searchQuery2.") AS combined";
  }

  $sel = mysqli_query($db, $filteredQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecordwithFilter = $records['allcount'];

  $countQuery = mysqli_query($db, $filteredQuery2);
  while($countRow = mysqli_fetch_assoc($countQuery)) {
    if ($countRow['transaction_status'] == 'Sales') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $salesPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $salesCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $salesCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Purchase') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $purchasePendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $purchaseCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $purchaseCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Local') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $localPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $localCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $localCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Misc') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $miscPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $miscCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $miscCancelCount++;
      }
    }
  }

  ## Fetch records
  $empQuery = "(select * from Weight where status = '0'".$searchQuery.") UNION ALL (select * from Weight_Container where status = '0'".$searchQuery.") order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $empQuery = "(select * from Weight where status = '0' and plant_code IN ('$username')".$searchQuery.") UNION ALL (select * from Weight_Container where status = '0' and plant_code IN ('$username')".$searchQuery.") order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
  }
}else{
  ## Total number of records without filtering
  $allQuery = "select count(*) as allcount from Weight where status = '0'";
  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $allQuery = "select count(*) as allcount from Weight where status = '0' and plant_code IN ('$username')";
  } 

  $sel = mysqli_query($db, $allQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecords = $records['allcount'];

  ## Total number of record with filtering
  $filteredQuery = "select count(*) as allcount from Weight where status = '0'".$searchQuery;
  $filteredQuery2 = "select * from Weight where status = '0'".$searchQuery2;
  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $filteredQuery = "select count(*) as allcount from Weight where status = '0' and plant_code IN ('$username')".$searchQuery;
    $filteredQuery2 = "select * from Weight where status = '0' and plant_code IN ('$username')".$searchQuery2;
  }

  $sel = mysqli_query($db, $filteredQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecordwithFilter = $records['allcount'];

  $countQuery = mysqli_query($db, $filteredQuery2);
  while($countRow = mysqli_fetch_assoc($countQuery)) {
    if ($countRow['transaction_status'] == 'Sales') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $salesPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $salesCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $salesCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Purchase') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $purchasePendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $purchaseCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $purchaseCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Local') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $localPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $localCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $localCancelCount++;
      }
    } elseif ($countRow['transaction_status'] == 'Misc') {
      if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
        $miscPendingCount++;
      } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
        $miscCompleteCount++;
      } elseif ($countRow['is_cancel'] == 'Y') {
        $miscCancelCount++;
      }
    }
  }

  ## Fetch records
  $empQuery = "select * from Weight where status = '0'".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

  if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $empQuery = "select * from Weight where status = '0' and plant_code IN ('$username')".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
  }
}
// var_dump($empQuery);
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $transactionStatus = '';
  if($row['transaction_status'] == 'Sales'){
    $transactionStatus = 'Sales';
  }
  else if($row['transaction_status'] == 'Purchase'){
    $transactionStatus = 'Purchase';
  }
  else if($row['transaction_status'] == 'Misc'){
    $transactionStatus = 'Miscellaneous';
  }
  else{
    $transactionStatus = 'Public';
  }

  if($row['weight_type'] == 'Container'){
    $weightType = 'Primer Mover';
  }elseif($row['weight_type'] == 'Empty Container'){
    $weightType = 'Primer Mover + Container';
  }else if($row['weight_type'] == 'Different Container'){
    $weightType = 'Primer Mover + Different Bins';
  } else{
    $weightType = $row['weight_type'];
  }

  $data[] = array( 
    "id"=>$row['id'],
    "transaction_id"=>$row['transaction_id'],
    "transaction_status"=>$transactionStatus,
    "weight_type"=>$weightType,
    "transaction_date"=>$row['transaction_date'],
    "lorry_plate_no1"=>$row['lorry_plate_no1'],
    "lorry_plate_no2"=>$row['lorry_plate_no2'],
    "supplier_weight"=>$row['supplier_weight'],
    "customer_code"=>$row['customer_code'],
    "customer_name"=>$row['customer_name'],
    "plant_code"=>$row['plant_code'],
    "plant_name"=>$row['plant_name'],
    "agent_code"=>$row['agent_code'],
    "agent_name"=>$row['agent_name'],
    "supplier_code"=>$row['supplier_code'],
    "supplier_name"=>$row['supplier_name'],
    "customer"=>($row['transaction_status'] == 'Purchase' ? $row['supplier_name'] : $row['customer_name']),
    "product_code"=>($row['transaction_status'] == 'Purchase' ? $row['raw_mat_code'] : $row['product_code']), 
    "product_name"=>($row['transaction_status'] == 'Purchase' ? $row['raw_mat_name'] : $row['product_name']), 
    "container_no"=>$row['container_no'],
    "seal_no"=>$row['seal_no'],
    "invoice_no"=>$row['invoice_no'],
    "purchase_order"=>$row['purchase_order'],
    "delivery_no"=>$row['delivery_no'],
    "transporter_code"=>$row['transporter_code'],
    "transporter"=>$row['transporter'],
    "destination_code"=>$row['destination_code'],
    "destination"=>$row['destination'],
    "remarks"=>$row['remarks'],
    "gross_weight1"=>$row['gross_weight1'],
    "gross_weight1_date"=>$row['gross_weight1_date'],
    "tare_weight1"=>$row['tare_weight1'],
    "tare_weight1_date"=>$row['tare_weight1_date'],
    "nett_weight1"=>$row['nett_weight1'],
    "gross_weight2"=>$row['gross_weight2'],
    "gross_weight2_date"=>$row['gross_weight2_date'],
    "tare_weight2"=>$row['tare_weight2'],
    "tare_weight2_date"=>$row['tare_weight2_date'],
    "nett_weight2"=>$row['nett_weight2'],
    "final_weight"=>$row['final_weight'],
    "weight_different"=>$row['weight_different'],
    "is_complete"=>$row['is_complete'],
    "is_cancel"=>$row['is_cancel'],
    "is_approved"=>$row['is_approved'],
    "approved_by"=>$row['approved_by'],
    "approved_reason"=>$row['approved_reason'],
    "manual_weight"=>$row['manual_weight'],
    "indicator_id"=>$row['indicator_id'],
    "weighbridge_id"=>$row['weighbridge_id'],
    "created_date"=>$row['created_date'],
    "created_by"=>$row['created_by'],
    "modified_date"=>$row['modified_date'],
    "modified_by"=>$row['modified_by'],
    "indicator_id_2"=>$row['indicator_id_2'],
    "product_description"=>$row['product_description']
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "salesTotalPending" => $salesPendingCount,
  "salesTotalComplete" => $salesCompleteCount,
  "salesTotalCancel" => $salesCancelCount,
  "purchaseTotalPending" => $purchasePendingCount,
  "purchaseTotalComplete" => $purchaseCompleteCount,
  "purchaseTotalCancel" => $purchaseCancelCount,
  "localTotalPending" => $localPendingCount,
  "localTotalComplete" => $localCompleteCount,
  "localTotalCancel" => $localCancelCount,
  "miscTotalPending" => $miscPendingCount,
  "miscTotalComplete" => $miscCompleteCount,
  "miscTotalCancel" => $miscCancelCount
);

echo json_encode($response);

?>