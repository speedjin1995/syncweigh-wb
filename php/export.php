<?php
session_start();
// Load the database configuration file 
require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
if($_GET["file"] == 'weight'){
    $fileName = "Weight-data_" . date('Y-m-d') . ".xls";
}else{
    $fileName = "Count-data_" . date('Y-m-d') . ".xls";
}

## Search 
$searchQuery = "";
$searchContainerQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $searchQuery = "and plant_code IN ('$username')";
    $searchContainerQuery = "and plant_code IN ('$username')";
}

if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_GET['fromDate']);
    $formatted_date = $date->format('Y-m-d 00:00:00');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date >= '".$formatted_date."'";
        $searchContainerQuery = " and Weight_Container.transaction_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date >= '".$formatted_date."'";
    }
}

if($_GET['toDate'] != null && $_GET['toDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_GET['toDate']);
    $formatted_date = $date->format('Y-m-d 23:59:59');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date <= '".$formatted_date."'";
        $searchContainerQuery .= " and Weight_Container.transaction_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date <= '".$formatted_date."'";
    }
}

if($_GET['transactionStatus'] != null && $_GET['transactionStatus'] != '' && $_GET['transactionStatus'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_GET['transactionStatus']."'";
        $searchContainerQuery .= " and Weight_Container.transaction_status = '".$_GET['transactionStatus']."'";
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_GET['transactionStatus']."'";
    }	
}

if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_GET['customer']."'";
        $searchContainerQuery .= " and Weight_Container.customer_code = '".$_GET['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_GET['customer']."'";
    }
}

if(isset($_GET['supplier']) && $_GET['supplier'] != null && $_GET['supplier'] != '' && $_GET['supplier'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.supplier_code = '".$_GET['supplier']."'";
        $searchContainerQuery .= " and Weight_Container.supplier_code = '".$_GET['supplier']."'";
    }
    else{
        $searchQuery .= " and count.supplier_code = '".$_GET['supplier']."'";
    }
}

if($_GET['vehicle'] != null && $_GET['vehicle'] != '' && $_GET['vehicle'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_GET['vehicle']."'";
        $searchContainerQuery .= " and Weight_Container.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
}

if($_GET['weighingType'] != null && $_GET['weighingType'] != '' && $_GET['weighingType'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_GET['weighingType']."%'";
        $searchContainerQuery .= " and Weight_Container.weight_type like '%".$_GET['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_GET['weighingType']."%'";
    }
}

if($_GET['product'] != null && $_GET['product'] != '' && $_GET['product'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_GET['product']."'";
        $searchContainerQuery .= " and Weight_Container.product_code = '".$_GET['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_GET['product']."'";
    }
}

if(isset($_GET['rawMat']) && $_GET['rawMat'] != null && $_GET['rawMat'] != '' && $_GET['rawMat'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.raw_mat_code = '".$_GET['rawMat']."'";
        $searchContainerQuery .= " and Weight_Container.raw_mat_code = '".$_GET['rawMat']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_GET['rawMat']."'";
    }
}

if(isset($_GET['plant']) && $_GET['plant'] != null && $_GET['plant'] != '' && $_GET['plant'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_GET['plant']."'";
        $searchContainerQuery .= " and Weight_Container.plant_code = '".$_GET['plant']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_GET['plant']."'";
    }
}

if(isset($_GET['status']) && $_GET['status'] != null && $_GET['status'] != '' && $_GET['status'] != '-'){
    if($_GET["file"] == 'weight'){
        if ($_GET['status'] == 'Complete'){
            $searchQuery .= " and Weight.is_complete = 'Y'";
            // $searchContainerQuery .= " and is_complete='Y' and is_cancel='Y'";
        }elseif ($_GET['status'] == 'Cancelled'){
            $searchQuery .= " and Weight.is_cancel = 'Y'";
        }elseif ($_GET['status'] == 'Pending'){
            $searchQuery .= " and is_complete='N' AND is_cancel='N'";
            // $searchContainerQuery .= " and is_complete='Y'";
        }else{
            $searchQuery .= " and Weight.is_complete = 'Y'";
        }
    }
}

if($_GET['isMulti'] != null && $_GET['isMulti'] != '' && $_GET['isMulti'] != '-'){
    $isMulti = $_GET['isMulti'];

    if ($isMulti == 'Y'){
        if(is_array($_GET['ids'])){
			$ids = implode(",", $_GET['ids']);
		}else{
			$ids = $_GET['ids'];
		}

        $searchQuery = " and id IN ($ids)";
    }
}

if($_GET['transactionStatus'] == 'Sales'){
    // Column names 
    $fields = array('TRANSACTION ID', 'COMPANY', 'TRANSACTION STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'LORRY NO. 2', 'CUSTOMER CODE', 'CUSTOMER NAME', 
        'PRODUCT CODE', 'PRODUCT NAME', 'PROJECT CODE', 'INTERNAL DOCS NO.', 'REF NO.', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 'DELIVERED BY', 
        'TRANSPORT CAP.', 'PO NO.', 'DO NO.', 'CONTAINER NO', 'SEAL NO', 'CONTAINER NO 2', 'SEAL NO 2', 'ORDER WEIGHT', 'GROSS WEIGHT', 'TARE WEIGHT', 'NET WEIGHT', 
        'IN TIME', 'OUT TIME', 'GROSS WEIGHT 2', 'TARE WEIGHT 2', 'NET WEIGHT 2', 'IN TIME2', 'OUT TIME2', 'REDUCE WEIGHT', 'VARIANCE', 'SUB TOTAL WEIGHT', 'MANUAL', 
        'CANCELLED', 'PLANT CODE', 'PLANT NAME', 'WEIGHTED BY', 'REMARK'); 
}
else if($_GET['transactionStatus'] == 'Purchase'){
    // Column names 
    $fields = array('TRANSACTION ID', 'COMPANY', 'TRANSACTION STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'LORRY NO. 2', 'SUPPLIER CODE', 'SUPPLIER NAME', 
        'RAW MAT CODE', 'RAW MAT NAME', 'PROJECT CODE', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 'DELIVERED BY', 'TRANSPORT CAP.', 'PO NO.', 'DO NO.', 
        'MRN NO.', 'CONTAINER NO', 'SEAL NO', 'CONTAINER NO 2', 'SEAL NO 2', 'SUPPLIER WEIGHT', 'GROSS WEIGHT', 'TARE WEIGHT', 'NET WEIGHT', 'IN TIME', 'OUT TIME', 
        'GROSS WEIGHT 2', 'TARE WEIGHT 2', 'NET WEIGHT 2', 'IN TIME2', 'OUT TIME2', 'REDUCE WEIGHT', 'VARIANCE', 'SUB TOTAL WEIGHT', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
        'PLANT NAME', 'WEIGHTED BY', 'REMARK'); 
}
else if($_GET['transactionStatus'] == 'Local'){
    // Column names 
    $fields = array('TRANSACTION ID', 'COMPANY', 'TRANSACTION STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'LORRY NO. 2', 'SUPPLIER CODE', 'SUPPLIER NAME', 
        'RAW MAT CODE', 'RAW MAT NAME', 'PROJECT CODE', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 'DELIVERED BY', 'TRANSPORT CAP.', 'PO NO.', 'DO NO.', 
        'CONTAINER NO', 'SEAL NO', 'CONTAINER NO 2', 'SEAL NO 2', 'SUPPLIER WEIGHT', 'GROSS WEIGHT', 'TARE WEIGHT', 'NET WEIGHT', 'IN TIME', 'OUT TIME', 
        'GROSS WEIGHT 2', 'TARE WEIGHT 2', 'NET WEIGHT 2', 'IN TIME2', 'OUT TIME2', 'REDUCE WEIGHT', 'VARIANCE', 'SUB TOTAL WEIGHT', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
        'PLANT NAME', 'WEIGHTED BY', 'REMARK'); 
}
else{
    // Column names 
    $fields = array('TRANSACTION ID', 'COMPANY', 'TRANSACTION STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'LORRY NO. 2', 'CUSTOMER CODE', 'CUSTOMER NAME', 
        'PRODUCT CODE', 'PRODUCT NAME', 'PROJECT CODE', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 'DELIVERED BY', 'TRANSPORT CAP.', 'PO NO.', 'DO NO.', 
        'CONTAINER NO', 'SEAL NO', 'CONTAINER NO 2', 'SEAL NO 2', 'ORDER WEIGHT', 'GROSS WEIGHT', 'TARE WEIGHT', 'NET WEIGHT', 'IN TIME', 'OUT TIME', 
        'GROSS WEIGHT 2', 'TARE WEIGHT 2', 'NET WEIGHT 2', 'IN TIME2', 'OUT TIME2', 'REDUCE WEIGHT', 'VARIANCE', 'SUB TOTAL WEIGHT', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
        'PLANT NAME', 'WEIGHTED BY', 'REMARK'); 
}

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database
if($_GET["file"] == 'weight'){
    $query = $db->query("select * from Weight WHERE status='0'".$searchQuery);
    /*$query = $db->query("
        SELECT * FROM Weight WHERE Weight.status = '0'".$searchQuery."
        UNION ALL
        SELECT * FROM Weight_Container WHERE Weight_Container.status = '0'".$searchContainerQuery."
        ORDER BY created_date ASC
    ");*/
}
else{
    $query = $db->query("select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
    count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
    count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
    count.remark, count.deleted, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
    count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
    count.productName = products.id AND status.id=count.status AND units.id=count.unit ".$searchQuery."");
}

if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){
        $lineData = []; // Ensure it starts as an empty array each iteration

        if($_GET["file"] == 'weight'){
            $productCode = [];
            $productName = [];
            $company = searchCompanyById($row['company'], $db);
            $projectCode = $row['project_code'];
            $projectArray = [];

            if($row['transaction_status'] == 'Sales'){
                $transactionStatus = 'Dispatch';
            }
            else if($row['transaction_status'] == 'Purchase'){
                $transactionStatus = 'Receiving';
                $productCode = json_decode($row['raw_mat_code'], true);
                $productName = json_decode($row['raw_mat_name'], true);
            }
            else if($row['transaction_status'] == 'Misc'){
                $transactionStatus = 'Miscellaneous';
                $productCode = json_decode($row['product_code'], true);
                $productName = json_decode($row['product_name'], true);
            }
            else{
                $transactionStatus = 'Internal Transfer';
                $productCode = json_decode($row['raw_mat_code'], true);
                $productName = json_decode($row['raw_mat_name'], true);
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

            if($projectCode != null){
                $projectCodes = json_decode($projectCode, true);

                for($i=0; $i<count($projectCodes); $i++){
                    $projectArray[] = searchProjectById($projectCodes[$i], $db);
                }
            }

            if($_GET['transactionStatus'] == 'Sales'){
                $query2 = $db->query("select * from Weight_Customer WHERE weight_id='".$row['id']."'");

                if($query2->num_rows > 0){ 
                    // Output each row of the data 
                    while($row2 = $query2->fetch_assoc()){
                        if($row2['product_id'] != null){
                            $productIds = json_decode($row2['product_id'], true);

                            for($i=0; $i<count($productIds); $i++){
                                $productCode[] = searchProductCodeById($productIds[$i], $db);
                                $productName[] = searchProductNameById($productIds[$i], $db);
                            }
                        }

                        if($row2['project_id'] != null){
                            $projectCodes = json_decode($row2['project_id'], true);

                            for($i=0; $i<count($projectCodes); $i++){
                                $projectArray[] = searchProjectById($projectCodes[$i], $db);
                            }
                        }

                        // Column names 
                        $lineData = array($row['transaction_id'], $company, $transactionStatus, $weightType, $row['transaction_date'], $row['lorry_plate_no1'], $row['lorry_plate_no2'], searchCustomerCodeById($row2['customer_id'], $db), searchCustomerNameById($row2['customer_id'], $db), 
                            implode("\\", $productCode), implode("\\", $productName), implode("\\", $projectArray), $row2['internal_doc_no'], $row2['ref_no'], $row['destination_code'], $row['destination'], $row['transporter_code'], $row['transporter'], searchTransportCapById($row['transport_cap'], $db), 
                            $row['purchase_order'], $row2['delivery_no'], $row['container_no'], $row['seal_no'], $row['container_no2'], $row['seal_no2'], $row['order_weight'], $row['gross_weight1'], $row['tare_weight1'], 
                            $row['nett_weight1'], $row['gross_weight1_date'], $row['tare_weight1_date'], $row['gross_weight2'], $row['tare_weight2'], $row['nett_weight2'], $row['gross_weight2_date'], $row['tare_weight2_date'],
                            $row['reduce_weight'], $row['weight_different'], $row['final_weight'], $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks']);

                        # Added checking to fix duplicated issue
                        if (!empty($lineData)) {
                            array_walk($lineData, 'filterData'); 
                            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                        }
                    }
                }
            }
            else if($_GET['transactionStatus'] == 'Purchase'){
                // Column names 
                $lineData = array($row['transaction_id'], $company, $transactionStatus, $weightType, $row['transaction_date'], $row['lorry_plate_no1'], $row['lorry_plate_no2'], $row['supplier_code'], $row['supplier_name'], 
                    implode("\\", $productCode), implode("\\", $productName), implode("\\", $projectArray), $row['destination_code'], $row['destination'], $row['transporter_code'], $row['transporter'], searchTransportCapById($row['transport_cap'], $db), 
                    $row['purchase_order'], $row['delivery_no'], $row['mrn_no'], $row['container_no'], $row['seal_no'], $row['container_no2'], $row['seal_no2'], $row['supplier_weight'], $row['gross_weight1'], $row['tare_weight1'], 
                    $row['nett_weight1'], $row['gross_weight1_date'], $row['tare_weight1_date'], $row['gross_weight2'], $row['tare_weight2'], $row['nett_weight2'], $row['gross_weight2_date'], $row['tare_weight2_date'],
                    $row['reduce_weight'], $row['weight_different'], $row['final_weight'], $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks']);
            
                #Added checking to fix duplicated issue
                if (!empty($lineData)) {
                    array_walk($lineData, 'filterData'); 
                    $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                }
            }
            else if($_GET['transactionStatus'] == 'Local'){
                // Column names 
                $lineData = array($row['transaction_id'], $company, $transactionStatus, $weightType, $row['transaction_date'], $row['lorry_plate_no1'], $row['lorry_plate_no2'], $row['supplier_code'], $row['supplier_name'], 
                    implode("\\", $productCode), implode("\\", $productName), implode("\\", $projectArray), $row['destination_code'], $row['destination'], $row['transporter_code'], $row['transporter'], searchTransportCapById($row['transport_cap'], $db), 
                    $row['purchase_order'], $row['delivery_no'], $row['container_no'], $row['seal_no'], $row['container_no2'], $row['seal_no2'], $row['supplier_weight'], $row['gross_weight1'], $row['tare_weight1'], 
                    $row['nett_weight1'], $row['gross_weight1_date'], $row['tare_weight1_date'], $row['gross_weight2'], $row['tare_weight2'], $row['nett_weight2'], $row['gross_weight2_date'], $row['tare_weight2_date'],
                    $row['reduce_weight'], $row['weight_different'], $row['final_weight'], $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks']);
            
                #Added checking to fix duplicated issue
                if (!empty($lineData)) {
                    array_walk($lineData, 'filterData'); 
                    $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                }
            }
            else{
                // Column names 
                $lineData = array($row['transaction_id'], $company, $transactionStatus, $weightType, $row['transaction_date'], $row['lorry_plate_no1'], $row['lorry_plate_no2'], $row['customer_code'], $row['customer_name'], 
                    implode("\\", $productCode), implode("\\", $productName), implode("\\", $projectArray), $row['destination_code'], $row['destination'], $row['transporter_code'], $row['transporter'], searchTransportCapById($row['transport_cap'], $db), 
                    $row['purchase_order'], $row['delivery_no'], $row['container_no'], $row['seal_no'], $row['container_no2'], $row['seal_no2'], $row['order_weight'], $row['gross_weight1'], $row['tare_weight1'], 
                    $row['nett_weight1'], $row['gross_weight1_date'], $row['tare_weight1_date'], $row['gross_weight2'], $row['tare_weight2'], $row['nett_weight2'], $row['gross_weight2_date'], $row['tare_weight2_date'],
                    $row['reduce_weight'], $row['weight_different'], $row['final_weight'], $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks']);
            
                # Added checking to fix duplicated issue
                if (!empty($lineData)) {
                    array_walk($lineData, 'filterData'); 
                    $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                }
            }
        }
        else{
            $lineData = array($row['serialNo'], $row['product_name'], $row['units'], $row['unitWeight'], $row['tare'], $row['currentWeight'], $row['actualWeight'],
            $row['totalPCS'], $row['moq'], $row['unitPrice'], $row['totalPrice'], $row['veh_number'], $row['lots_no'], $row['batchNo'], $row['invoiceNo']
            , $row['deliveryNo'], $row['purchaseNo'], $row['customer_name'], $row['packages'], $row['dateTime'], $row['remark'], $row['status'], $deleted);
            
            # Added checking to fix duplicated issue
            if (!empty($lineData)) {
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
    } 
}
else{ 
    $excelData .= 'No records found...'. "\n"; 
} 
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>