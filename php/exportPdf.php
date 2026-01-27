<?php
session_start();
require_once 'db_connect.php';

// Get Company Detail
$stmt = $db->prepare("SELECT * from Company WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();

$includePrice = '';
$includeContainer = '';
if(($row = $result->fetch_assoc()) !== null){
    $includePrice = $row['include_price'];
    $includeContainer = $row['include_container'];
}

$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $searchQuery = "and plant_code IN ('$username')";
}

if(isset($_POST['fromDate']) && $_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
    $formatted_date = $date->format('Y-m-d 00:00:00');
    $fromDate = $date->format('d/m/Y');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date >= '".$formatted_date."'";
    }
}

if(isset($_POST['toDate']) && $_POST['toDate'] != null && $_POST['toDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
    $formatted_date = $date->format('Y-m-d 23:59:59');
    $toDate = $date->format('d/m/Y');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date <= '".$formatted_date."'";
    }
}

if(isset($_POST['transactionStatus']) && $_POST['transactionStatus'] != null && $_POST['transactionStatus'] != '' && $_POST['transactionStatus'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_POST['transactionStatus']."'";

        // if($_POST['status'] == 'Sales'){
        //     $searchQuery .= " and Weight.transaction_status = '".$_POST['status']."'";
        // }
        // else{
        //     $searchQuery .= " and Weight.transaction_status IN ('Purchase', 'Local')";
        // }
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_POST['transactionStatus']."'";
    }	
}

if(isset($_POST['customer']) && $_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_POST['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_POST['customer']."'";
    }
}

if(isset($_POST['supplier']) && $_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.supplier_code = '".$_POST['supplier']."'";
    }
    else{
        $searchQuery .= " and count.supplier_code = '".$_POST['supplier']."'";
    }
}

if(isset($_POST['vehicle']) && $_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
}

if(isset($_POST['weighingType']) && $_POST['weighingType'] != null && $_POST['weighingType'] != '' && $_POST['weighingType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_POST['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_POST['weighingType']."%'";
    }
}

if(isset($_POST['customerType']) && $_POST['customerType'] != null && $_POST['customerType'] != '' && $_POST['customerType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_type like '%".$_POST['customerType']."%'";
    }
    else{
        $searchQuery .= " and count.customer_type like '%".$_POST['customerType']."%'";
    }
}

if(isset($_POST['product']) && $_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_POST['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_POST['product']."'";
    }
}

if(isset($_POST['rawMat']) && $_POST['rawMat'] != null && $_POST['rawMat'] != '' && $_POST['rawMat'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.raw_mat_code = '".$_POST['rawMat']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_POST['rawMat']."'";
    }
}

if(isset($_POST['destination']) && $_POST['destination'] != null && $_POST['destination'] != '' && $_POST['destination'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.destination = '".$_POST['destination']."'";
    }
    else{
        $searchQuery .= " and count.destination = '".$_POST['destination']."'";
    }
}

if(isset($_POST['plant']) && $_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_POST['plant']."'";
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_POST['plant']."'";
    }
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
    if ($_POST['status'] == 'Complete'){
        $searchQuery .= " and is_complete = 'Y' AND is_cancel = 'N'";
    }elseif ($_POST['status'] == 'Cancelled'){
        $searchQuery .= " and is_cancel = 'Y'";
    }elseif ($_POST['status'] == 'Pending'){
        $searchQuery .= " and is_complete='N' AND is_cancel='N'";
    }
    else{
        $searchQuery .= " and is_complete = 'Y' AND is_cancel = 'N'";
    }
}

if(isset($_POST['plant']) && $_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_POST['plant']."'";
        $plantSelected = $_POST['plant'];
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_POST['plant']."'";
    }
}else{
    if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
        $username = implode("/", $_SESSION["plant"]);
        $plantSelected = $username;
    }
    else{
        $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
        $plant2 = $plant2->fetch_all(MYSQLI_ASSOC);
        foreach($plant2 as $key => $value){
            $plantCode[$key] = $value['plant_code'];
        }
        $username = implode("/", $plantCode);
        $plantSelected = $username;
    }
}

if(isset($_POST['isMulti']) && $_POST['isMulti'] != null && $_POST['isMulti'] != '' && $_POST['isMulti'] != '-'){
    $isMulti = $_POST['isMulti'];

    if ($isMulti == 'Y'){
        if(is_array($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
		}else{
			$ids = $_POST['ids'];
		}

        $searchQuery = " and id IN ($ids)";
    }
}

$isMulti = '';
if(isset($_POST['isMulti']) && $_POST['isMulti'] != null && $_POST['isMulti'] != '' && $_POST['isMulti'] != '-'){
    $isMulti = $_POST['isMulti'];
}

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        // Company Details
        $companyCode = '';
        $companyName = '';

        if ($company_stmt = $db->prepare("SELECT * FROM Company")) {
            if (! $company_stmt->execute()) {
                echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
            }else{
                $result = $company_stmt->get_result();
                $companyData = $result->fetch_assoc();
                $companyCode = $companyData['company_code'];
                $companyName = $companyData['name'];
            }
        }


        $sql = '';
        if ($_POST['reportType'] == 'SUMMARY') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "SELECT DATE(tare_weight1_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, SUM(total_price) AS total_price, COUNT(*) AS total_records FROM Weight WHERE id IN (".$id.") GROUP BY DATE(tare_weight1_date) ORDER BY DATE(tare_weight1_date) ASC";
            }else{
                $sql = "SELECT DATE(tare_weight1_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, SUM(total_price) AS total_price, COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(tare_weight1_date) ORDER BY DATE(tare_weight1_date) ASC";
            }

            if ($select_stmt = $db->prepare($sql)){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $totalRecords = 0;
                    $totalProductWeight = 0;
                    $totalTransportWeight = 0;
                    $grandTotalPrice = 0;

                    $message = '
                        <html>
                            <head>
                                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                        
                                    .print-button {
                                        position: fixed;
                                        bottom: 20px;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        background: #007bff;
                                        color: white;
                                        border: none;
                                        padding: 12px 20px;
                                        border-radius: 50px;
                                        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                        cursor: pointer;
                                        font-size: 14px;
                                        font-weight: 500;
                                        z-index: 1000;
                                        transition: all 0.3s ease;
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                    }
                                    
                                    .print-button:hover {
                                        background: #0056b3;
                                        transform: translateX(-50%);
                                        box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                    }
                                    
                                    .print-button:active {
                                        transform: translateY(0);
                                    }
                                    
                                    .print-icon {
                                        width: 16px;
                                        height: 16px;
                                    }
                                    
                                    @media print {
                                        #printButton {
                                            display: none;
                                        }
                                    }
                                </style>
                            </head>

                            <body>
                                <button id="printButton" class="print-button" onclick="window.print()">
                                    <i class="fas fa-print"></i>
                                    Print Report
                                </button>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">'.$companyName.'</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Date</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : '.$companyCode.' / '.$companyCode.'
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : '.$plantSelected.' - Weighing Only
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" width="15%" class="text-start">Date</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Total Amount (RM)</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    $transactionDate = date("d-m-Y", strtotime($row['transaction_date']));
                                                    $productWeight = number_format($row['product_weight']/1000, 2);
                                                    $transportWeight = number_format($row['transport_weight']/1000, 2);
                                                    $totalPrice = number_format($row['total_price'], 2);

                                                    $totalRecords += $row['total_records'];
                                                    $totalProductWeight += $row['product_weight']/1000;
                                                    $totalTransportWeight += $row['transport_weight']/1000;
                                                    $grandTotalPrice += (float) $row['total_price'];

                                                    $message .= '<tr>
                                                            <td>'.$transactionDate.'</td>
                                                            <td>'.$row['total_records'].'</td>
                                                            <td>'.$productWeight.'</td>
                                                            <td>'.$transportWeight.'</td>
                                                            <td>'.$totalPrice.'</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                        </tr>';
                                                }
                                                
                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$totalRecords.'</td>
                                                        <td>'.number_format($totalProductWeight, 2).'</td>
                                                        <td>'.number_format($totalTransportWeight, 2).'</td>
                                                        <td>'.number_format($grandTotalPrice, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "query" => "SELECT DATE(tare_weight1_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(tare_weight1_date) ORDER BY DATE(tare_weight1_date) ASC",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'PRODUCT'){
            $sql = "SELECT * FROM ( SELECT product_name AS name, SUM(nett_weight1) AS product_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(product_code) IS NOT NULL AND  is_cancel <> 'Y'".$searchQuery." 
            GROUP BY product_code UNION ALL SELECT raw_mat_code AS code, SUM(nett_weight1) AS product_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(raw_mat_code) IS NOT NULL".$searchQuery." GROUP BY raw_mat_code ) AS combined_results ORDER BY name";

            if ($select_stmt = $db->prepare($sql)){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $premixTotalRecords = 0;
                    $premixTotalProductWeight = 0;
                    $premixTotalTransportWeight = 0;
                    $premixGrandTotalPrice = 0;
                    $othersTotalRecords = 0;
                    $othersTotalProductWeight = 0;
                    $othersTotalTransportWeight = 0;
                    $othersGrandTotalPrice = 0;

                    $message = '
                        <html>
                            <head>
                                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                    
                                    .print-button {
                                        position: fixed;
                                        bottom: 20px;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        background: #007bff;
                                        color: white;
                                        border: none;
                                        padding: 12px 20px;
                                        border-radius: 50px;
                                        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                        cursor: pointer;
                                        font-size: 14px;
                                        font-weight: 500;
                                        z-index: 1000;
                                        transition: all 0.3s ease;
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                    }
                                    
                                    .print-button:hover {
                                        background: #0056b3;
                                        transform: translateX(-50%);
                                        box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                    }
                                    
                                    .print-button:active {
                                        transform: translateY(0);
                                    }
                                    
                                    .print-icon {
                                        width: 16px;
                                        height: 16px;
                                    }
                                    
                                    @media print {
                                        #printButton {
                                            display: none;
                                        }
                                    }
                                </style>
                            </head>

                            <body>
                                <button id="printButton" class="print-button" onclick="window.print()">
                                    <i class="fas fa-print"></i>
                                    Print Report
                                </button>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">'.$companyName.'</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Product</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : '.$companyCode.' / '.$companyCode.'
                                                Start Product / Last Product : / QD
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : '.$plantSelected.' - Weighing Only
                                            </p>
                                        </div>
                                    </div>';

                                    $premixProductData = [];
                                    $othersProductData = [];

                                    while ($row = $result->fetch_assoc()) {
                                        $productCode = '';
                                        $productName = '';
                                        $productType = '';
                                        if ($product_stmt = $db->prepare("SELECT * FROM Product WHERE name = ? AND status = '0'")) {
                                            $product_stmt->bind_param('s', $row['name']);
                                            if ($product_stmt->execute()) {
                                                $result_product = $product_stmt->get_result();
                                                $productData = $result_product->fetch_assoc();
                                                $productCode = $productData['product_code'];
                                                $productName = $productData['name'];
                                                $productType = $productData['type'];
                                            }

                                            $product_stmt->close();
                                        }

                                        if ($productType == 'Premix') {
                                            $productWeight = number_format($row['product_weight']/1000, 2);
                                            $transportWeight = number_format($row['transport_weight']/1000, 2);

                                            if ($_SESSION["roles"] == 'NORMAL') {
                                                $totalPrice = number_format(0, 2);
                                            }else{
                                                $totalPrice = number_format($row['total_price'], 2);
                                            }

                                            $premixTotalRecords += $row['total_records'];
                                            $premixTotalProductWeight += $row['product_weight']/1000;
                                            $premixTotalTransportWeight += $row['transport_weight']/1000;
                                            if ($_SESSION["roles"] == 'NORMAL') {
                                                $premixGrandTotalPrice += 0.00;
                                            }else{
                                                $premixGrandTotalPrice += (float) $row['total_price'];
                                            }

                                            $premixProductData[] = '
                                                <tr>
                                                    <td>'.$productName.'</td>
                                                    <td>'.$row['total_records'].'</td>
                                                    <td>'.$productWeight.'</td>
                                                    <td>'.$transportWeight.'</td>
                                                    <td>'.$totalPrice.'</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                </tr>
                                            ';
                                        }else{
                                            $productWeight = number_format($row['product_weight']/1000, 2);
                                            $transportWeight = number_format($row['transport_weight']/1000, 2);

                                            if ($_SESSION["roles"] == 'NORMAL') {
                                                $totalPrice = number_format(0, 2);
                                            }else{
                                                $totalPrice = number_format($row['total_price'], 2);
                                            }

                                            $othersTotalRecords += $row['total_records'];
                                            $othersTotalProductWeight += $row['product_weight']/1000;
                                            $othersTotalTransportWeight += $row['transport_weight']/1000;
                                            if ($_SESSION["roles"] == 'NORMAL') {
                                                $othersGrandTotalPrice += 0.00;
                                            }else{
                                                $othersGrandTotalPrice += (float) $row['total_price'];
                                            }

                                            $othersGrandTotalPrice += (float) $row['total_price'];

                                            $othersProductData[] = '
                                                <tr>
                                                    <td>'.$productName.'</td>
                                                    <td>'.$row['total_records'].'</td>
                                                    <td>'.$productWeight.'</td>
                                                    <td>'.$transportWeight.'</td>
                                                    <td>'.$totalPrice.'</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                </tr>
                                            ';
                                        }
                                    }

                                    $message .= '
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr><th colspan="12" class="text-center" style="border-top: 1px solid black;">Premix Product</th></tr>
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" class="text-start">Product Description</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Average Selling Price</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                foreach ($premixProductData as $data) {
                                                    $message .= $data;
                                                }

                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$premixTotalRecords.'</td>
                                                        <td>'.number_format($premixTotalProductWeight, 2).'</td>
                                                        <td>'.number_format($premixTotalTransportWeight, 2).'</td>
                                                        <td>'.number_format($premixGrandTotalPrice, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>';

                                    $message .= '
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr><th colspan="12" class="text-center" style="border-top: 1px solid black;">Other Product</th></tr>
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" class="text-start">Product Description</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Average Selling Price</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                if(count($othersProductData) > 0){
                                                    foreach ($othersProductData as $data) {
                                                        $message .= $data;
                                                    }
                                                }

                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$othersTotalRecords.'</td>
                                                        <td>'.number_format($othersTotalProductWeight, 2).'</td>
                                                        <td>'.number_format($othersTotalTransportWeight, 2).'</td>
                                                        <td>'.number_format($othersGrandTotalPrice, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'S&PC'){
            if ($isMulti == 'Y'){
                //$id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($ids) ORDER BY transaction_date ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y'".$searchQuery.' ORDER BY transaction_date ASC';
            }

            if ($select_stmt = $db->prepare($sql)){
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }

                                            #printButton {
                                                display: none;
                                            }
                                        } 
                                        
                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 50px;
                                            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                            cursor: pointer;
                                            font-size: 14px;
                                            font-weight: 500;
                                            z-index: 1000;
                                            transition: all 0.3s ease;
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        }
                                        
                                        .print-button:hover {
                                            background: #0056b3;
                                            transform: translateX(-50%);
                                            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                        }
                                        
                                        .print-button:active {
                                            transform: translateY(0);
                                        }
                                        
                                        .print-icon {
                                            width: 16px;
                                            height: 16px;
                                        }
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <button id="printButton" class="print-button" onclick="window.print()">
                                        <i class="fas fa-print"></i>
                                        Print Report
                                    </button>
                                    <div style="margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 15px; text-align: center;">
                                        <h3 class="fw-bold" style="margin: 0; font-size: 24px; color: #000;">'.$companyName.'</h3>
                                        <h5 style="margin: 5px 0 0 0; font-size: 16px; color: #666;">Summary Report By Customer</h5>
                                    </div>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="border-bottom: 1px solid black; font-size: 11px; text-align: center;">
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                                if($_POST['transactionStatus'] == 'Sales' || $_POST['transactionStatus'] == 'Local'){
                                                    $message .= '<th>CUSTOMER</th>';
                                                }
                                                else{
                                                    $message .= '<th>SUPPLIER</th>';
                                                }
                                                    
                                                $message .= '<th>'.($_POST['transactionStatus'] == 'Sales' || $_POST['transactionStatus'] == 'Local' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <th>DESTINATION</th>';

                                                // if($_POST['transactionStatus'] == 'Sales'){
                                                //     $message .= '<th>EXQ/DEL</th>';
                                                // }
                                                
                                                $message .= '
                                                <th>PO NO.</th>
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>';

                                                $message .= '
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                                <th>USER</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['customer_name'] : $row['supplier_name']);
                                        
                                            if (!isset($groupedData[$productName])) {
                                                $groupedData[$productName] = [];
                                            }
                                        
                                            $groupedData[$productName][] = $row;
                                        }
                                        
                                        // Initialize total values
                                        $grandTotalGross = 0;
                                        $grandTotalTare = 0;
                                        $grandTotalNet = 0;
                                        $grandTotalUnitPrice = 0;
                                        $grandTotalPricing = 0;

                                        // Generate table grouped by product
                                        foreach ($groupedData as $product => $rows) {
                                            $message .= '<tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>';
                                        
                                            $totalGross = 0;
                                            $totalTare = 0;
                                            $totalNet = 0;
                                            $totalUnitPrice = 0;
                                            $totalPricing = 0;
                                        
                                            foreach ($rows as $row) {
                                                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                                                $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                                                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedTareWeightDate = $tareWeightDate->format('H:i');
                                                $transactionDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
                                                $message .= '<tr style="font-size: 10px; text-align: center;">
                                                    <td>' . $row['transaction_id'] . '</td>
                                                    <td>' . $formattedtransactionDate . '</td>
                                                    <td>' . $row['lorry_plate_no1'] . '</td>';
                                                    
                                                    if($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local'){
                                                        $message .= '<td>' . $row['customer_name'] . '</td>';
                                                    }
                                                    else{
                                                        $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                    }
                                                    
                                                    $message .= '<td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <td>' . $row['destination'] . '</td>';

                                                    // if($_POST['transactionStatus'] == 'Sales'){
                                                    //     $message .= '<td>' . $exDel . '</td>';
                                                    // }
                                                    
                                                    $message .= '
                                                    <td>' . $row['purchase_order'] . '</td>
                                                    <td>' . $row['delivery_no'] . '</td>
                                                    <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>';

                                                    $message .= '
                                                    <td>' . $formattedGrossWeightDate . '</td>
                                                    <td>' . $formattedTareWeightDate . '</td>
                                                    <td>' . $row['created_by'] . '</td>
                                                </tr>';
                                        
                                                // Calculate subtotals
                                                $totalGross += (float)$row['gross_weight1'];
                                                $totalTare += (float)$row['tare_weight1'];
                                                $totalNet += (float)$row['nett_weight1'];
                                                $totalUnitPrice += (float)$row['unit_price'];
                                                $totalPricing += (float)$row['total_price'];
                                            }
                                        
                                            // Add product-wise subtotal
                                            $message .= '<tr style="font-size: 11px;">
                                                <th colspan="8">Subtotal (' . $product . ')</th>
                                                <th style="border:1px solid black;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;">' . number_format($totalNet/1000, 2) . '</th>';

                                                $message .= '
                                            </tr>';
                                        
                                            // Add to grand total
                                            $grandTotalGross += $totalGross;
                                            $grandTotalTare += $totalTare;
                                            $grandTotalNet += $totalNet;
                                            $grandTotalUnitPrice += $totalUnitPrice;
                                            $grandTotalPricing += $totalPricing;
                                        }
                                        
                                        $message .= '</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="font-size: 11px;" colspan="8">Grand Total</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>';
                                            $message .= '
                                                </tr>
                                            </tfoot>';
                                        $message .= '</tbody>';
                                        
                                    $message .= '</table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'DO') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY delivery_no ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y'".$searchQuery.' ORDER BY delivery_no ASC';
            }

            if ($select_stmt = $db->prepare($sql)) {
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }

                                            #printButton {
                                                display: none;
                                            }
                                        } 
                                        
                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 50px;
                                            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                            cursor: pointer;
                                            font-size: 14px;
                                            font-weight: 500;
                                            z-index: 1000;
                                            transition: all 0.3s ease;
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        }
                                        
                                        .print-button:hover {
                                            background: #0056b3;
                                            transform: translateX(-50%);
                                            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                        }
                                        
                                        .print-button:active {
                                            transform: translateY(0);
                                        }
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <button id="printButton" class="print-button" onclick="window.print()">
                                        <i class="fas fa-print"></i>
                                        Print Report
                                    </button>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="font-size: 9px;">
                                                <th>NO</th>
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                            if($_POST['status'] == 'Sales'){
                                                $message .= '<th>CUSTOMER</th>';
                                            }
                                            else{
                                                $message .= '<th>SUPPLIER</th>';
                                            }
                                                
                                                $message .= '<th>'.($_POST['status'] == 'Sales' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <th>EXQ/DEL</th>
                                                <th>BATCH/DRUM</th>
                                                <th>PO NO.</th>
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>
                                                <th>BALANCE <br>(MT)</th>';

                                                if ($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN' || $_SESSION["roles"] == 'AUTHORITY' || $_SESSION["roles"] == 'MANAGER'){
                                                    $message .= '
                                                        <th>UNIT PRICE <br>(RM)</th>
                                                        <th>TOTAL PRICE <br>(RM)</th>
                                                    ';
                                                }
                                                $message .= '
                                                <th>IS CANCEL</th>
                                                <th>CANCEL <br>REASON</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        
                                        $noCount = 0;
                                        $grandTotalGross = 0;
                                        $grandTotalTare = 0;
                                        $grandTotalNet = 0;
                                        $grandTotalUnitPrice = 0;
                                        $grandTotalPrice = 0;
                                        while ($row = $result->fetch_assoc()) {
                                            $noCount++;
                                            $grandTotalGross += (float)$row['gross_weight1'];
                                            $grandTotalTare += (float)$row['tare_weight1'];
                                            $grandTotalNet += (float)$row['nett_weight1'];
                                            $grandTotalUnitPrice += (float)$row['unit_price'];
                                            $grandTotalPrice += (float)$row['total_price'];
                                            $transactionDate =  new DateTime($row['transaction_date']);
                                            $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                            $exDel = '';
                                            
                                            if ($row['ex_del'] == 'EX'){
                                                $exDel = 'E';
                                            }else{
                                                $exDel = 'D';
                                            }

                                            $message .= '<tr style="text-align:center; font-size: 11px;"">
                                                <td>' . $noCount . '</td>
                                                <td>' . $row['transaction_id'] . '</td>
                                                <td>' . $formattedtransactionDate . '</td>
                                                <td>' . $row['lorry_plate_no1'] . '</td>';
                                                
                                                if($_POST['status'] == 'Sales'){
                                                    $message .= '<td>' . $row['customer_name'] . '</td>';
                                                }
                                                else{
                                                    $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                }
                                                
                                                $message .= '
                                                <td>' . ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                <td>' . $row['purchase_order'] . '</td>
                                                <td>' . $row['delivery_no'] . '</td>
                                                <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>';

                                                $message .= '
                                                <td>' . $row['is_cancel'] . '</td>
                                                <td>' . $row['cancelled_reason'] . '</td>
                                            </tr>';
                                        }
                                                                                
                                    $message .= '
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="font-size: 11px;" colspan="10"">Grand Total</th>
                                                <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>';

                                                if ($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN' || $_SESSION["roles"] == 'AUTHORITY' || $_SESSION["roles"] == 'MANAGER'){
                                                    $message .= '
                                                        <th></th>
                                                        <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalUnitPrice, 2).'</th>
                                                        <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalPrice, 2).'</th>
                                                    ';
                                                }
                                                $message .= '
                                            </tr>
                                        </tfoot>
                                    </table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }          
        }
        else if ($_POST['reportType'] == 'CANCEL') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY delivery_no ASC";
            }else{
                $sql = "select * from Weight WHERE is_cancel = 'Y'".$searchQuery.' ORDER BY delivery_no ASC';
            }

            if ($select_stmt = $db->prepare($sql)) {
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }

                                            #printButton {
                                                display: none;
                                            }
                                        } 
                                        
                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 50px;
                                            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                            cursor: pointer;
                                            font-size: 14px;
                                            font-weight: 500;
                                            z-index: 1000;
                                            transition: all 0.3s ease;
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        }
                                        
                                        .print-button:hover {
                                            background: #0056b3;
                                            transform: translateX(-50%);
                                            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                        }
                                        
                                        .print-button:active {
                                            transform: translateY(0);
                                        }
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <button id="printButton" class="print-button" onclick="window.print()">
                                        <i class="fas fa-print"></i>
                                        Print Report
                                    </button>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="font-size: 9px;">
                                                <th>NO</th>
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                            if($_POST['status'] == 'Sales'){
                                                $message .= '<th>CUSTOMER</th>';
                                            }
                                            else{
                                                $message .= '<th>SUPPLIER</th>';
                                            }
                                                
                                                $message .= '<th>'.($_POST['status'] == 'Sales' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <th>PO NO.</th>
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>
                                                <th>IS CANCEL</th>
                                                <th>CANCEL <br>REASON</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        
                                        $noCount = 0;
                                        while ($row = $result->fetch_assoc()) {
                                            $noCount++;
                                            $transactionDate =  new DateTime($row['transaction_date']);
                                            $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                            $exDel = '';
                                            
                                            if ($row['ex_del'] == 'EX'){
                                                $exDel = 'E';
                                            }else{
                                                $exDel = 'D';
                                            }

                                            $message .= '<tr style="text-align:center; font-size: 8px;"">
                                                <td>' . $noCount . '</td>
                                                <td>' . $row['transaction_id'] . '</td>
                                                <td>' . $formattedtransactionDate . '</td>
                                                <td>' . $row['lorry_plate_no1'] . '</td>';
                                                
                                                if($_POST['status'] == 'Sales'){
                                                    $message .= '<td>' . $row['customer_name'] . '</td>';
                                                }
                                                else{
                                                    $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                }
                                                
                                                $message .= '
                                                <td>' . ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                <td>' . $row['purchase_order'] . '</td>
                                                <td>' . $row['delivery_no'] . '</td>
                                                <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>
                                                <td>' . $row['is_cancel'] . '</td>
                                                <td>' . $row['cancelled_reason'] . '</td>
                                            </tr>';
                                            
                                        }
                                                                                
                                    $message .= '
                                        </tbody>
                                    </table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }          
        }
        else{
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY transaction_date ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y' ".$searchQuery.' ORDER BY transaction_date ASC';
            }

            if ($select_stmt = $db->prepare($sql)) {
                // Execute the prepared query.
                if (! $select_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
    
                    $message = '<html>
                                <head>
                                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                                    <style>
                                        @media print {
                                            @page {
                                                margin-left: 0.5in;
                                                margin-right: 0.5in;
                                                margin-top: 0.1in;
                                                margin-bottom: 0.1in;
                                            }

                                            #printButton {
                                                display: none;
                                            }
                                            
                                        } 

                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 50px;
                                            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                                            cursor: pointer;
                                            font-size: 14px;
                                            font-weight: 500;
                                            z-index: 1000;
                                            transition: all 0.3s ease;
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        }
                                        
                                        .print-button:hover {
                                            background: #0056b3;
                                            transform: translateX(-50%);
                                            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
                                        }
                                        
                                        .print-button:active {
                                            transform: translateY(0);
                                        }
                                                
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            
                                        } 
                                        
                                        .table th, .table td {
                                            padding: 0.70rem;
                                            vertical-align: top;
                                            border-top: 1px solid #dee2e6;
                                            
                                        } 
                                        
                                        .table-bordered {
                                            border: 1px solid #000000;
                                            
                                        } 
                                        
                                        .table-bordered th, .table-bordered td {
                                            border: 1px solid #000000;
                                            font-family: sans-serif;
                                            font-size: 12px;
                                            
                                        } 
                                        
                                        .row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                            margin-right: -15px;
                                            margin-left: -15px;
                                            
                                        } 
                                        
                                        .col-md-4{
                                            position: relative;
                                            width: 33.333333%;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <button id="printButton" class="print-button" onclick="window.print()">
                                        <i class="fas fa-print"></i>
                                        Print Report
                                    </button>
                                    <div style="margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 15px; text-align: center;">
                                        <h3 class="fw-bold" style="margin: 0; font-size: 24px; color: #000;">'.$companyName.'</h3>
                                        <h5 style="margin: 5px 0 0 0; font-size: 16px; color: #666;">Summary Report By Product</h5>
                                    </div>
                                    <table style="width:100%;">
                                        <thead>
                                            <tr style="border-bottom: 1px solid black; font-size: 11px; text-align: center;">
                                                <th>TRANSACTION <br>ID</th>
                                                <th>TRANSACTION <br>DATE</th>
                                                <th>LORRY <br>NO.</th>';
                                                
                                                if($_POST['transactionStatus'] == 'Sales' || $_POST['transactionStatus'] == 'Local'){
                                                    $message .= '<th>CUSTOMER</th>';
                                                }
                                                else{
                                                    $message .= '<th>SUPPLIER</th>';
                                                }
                                                
                                                $message .= '<th>'.($_POST['transactionStatus'] == 'Sales' || $_POST['transactionStatus'] == 'Local' ? 'PRODUCT' : 'RAW MATERIAL').'</th>
                                                <th>DESTINATION</th>';

                                                // if($_POST['status'] == 'Sales'){
                                                //     $message .= '<th>EXQ/DEL</th>';
                                                // }
                                                
                                                $message .= '
                                                <th>PO NO.</th>
                                                <th>DO NO.</th>
                                                <th>INCOMING <br>(MT)</th>
                                                <th>OUTGOING <br>(MT)</th>
                                                <th>NETT <br>(MT)</th>';

                                                $message .= '
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                                <th>USER</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']);
                                        
                                            if (!isset($groupedData[$productName])) {
                                                $groupedData[$productName] = [];
                                            }
                                        
                                            $groupedData[$productName][] = $row;
                                        }
                                        
                                        // Initialize total values
                                        $grandTotalGross = 0;
                                        $grandTotalTare = 0;
                                        $grandTotalNet = 0;
                                        $grandTotalUnitPrice = 0;
                                        $grandTotalPricing = 0;
    
                                        // Generate table grouped by product
                                        foreach ($groupedData as $product => $rows) {
                                            $message .= '<tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" style="font-size: 10px;">. </td>
                                            </tr>';
                                        
                                            $totalGross = 0;
                                            $totalTare = 0;
                                            $totalNet = 0;
                                            $totalUnitPrice = 0;
                                            $totalPricing = 0;
                                        
                                            foreach ($rows as $row) {
                                                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                                                $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                                                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                                                $formattedTareWeightDate = $tareWeightDate->format('H:i');
                                                $transactionDate =  new DateTime($row['transaction_date']);
                                                $formattedtransactionDate = $transactionDate->format('d/m/Y');
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
                                                $message .= '<tr style="font-size: 10px; text-align: center;">
                                                    <td>' . $row['transaction_id'] . '</td>
                                                    <td>' . $formattedtransactionDate . '</td>
                                                    <td>' . $row['lorry_plate_no1'] . '</td>';
                                                    
                                                    if($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local'){
                                                        $message .= '<td>' . $row['customer_name'] . '</td>';
                                                    }
                                                    else{
                                                        $message .= '<td>' . $row['supplier_name'] . '</td>';
                                                    }
                                                    
                                                    
                                                    $message .= '<td>' . ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <td>' . $row['destination'] . '</td>';

                                                    // if($_POST['status'] == 'Sales'){
                                                    //     $message .= '<td style="font-size: 10px; text-align: center;">' . $exDel . '</td>';
                                                    // }
                                                    
                                                    $message .= '
                                                    <td>' . $row['purchase_order'] . '</td>
                                                    <td>' . $row['delivery_no'] . '</td>
                                                    <td>' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>';

                                                    $message .= '
                                                    <td>' . $formattedGrossWeightDate . '</td>
                                                    <td>' . $formattedTareWeightDate . '</td>
                                                    <td style="font-size: 10px; text-align: center;">' . $row['created_by'] . '</td>
                                                </tr>';
                                        
                                                // Calculate subtotals
                                                $totalGross += (float)$row['gross_weight1'];
                                                $totalTare += (float)$row['tare_weight1'];
                                                $totalNet += (float)$row['nett_weight1'];
                                                $totalUnitPrice += (float)$row['unit_price'];
                                                $totalPricing += (float)$row['total_price'];
                                            }
                                        
                                            // Add product-wise subtotal
                                            $message .= '<tr>
                                                <th style="font-size: 11px;" colspan="8">Subtotal (' . $product . ')</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;font-size: 11px;">' . number_format($totalNet/1000, 2) . '</th>';
                                                $message .= '
                                            </tr>';
                                        
                                            // Add to grand total
                                            $grandTotalGross += $totalGross;
                                            $grandTotalTare += $totalTare;
                                            $grandTotalNet += $totalNet;
                                            $grandTotalUnitPrice += $totalUnitPrice;
                                            $grandTotalPricing += $totalPricing;
                                        }
                                        
                                        $message .= '</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="font-size: 11px;" colspan="8">Grand Total</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 11px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>';
                                                    $message .= '
                                                </tr>
                                            </tfoot>';
                                        $message .= '</tbody>';
                                        
                                    $message .= '</table>
                                </body>
                            </html>';
    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>