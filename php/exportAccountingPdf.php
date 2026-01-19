<?php
session_start();
require_once 'db_connect.php';

function groupWeighingByProduct($weighingRecords, $transactionStatus) {
    $grouped = [];
    
    foreach ($weighingRecords as $record) {
        if ($transactionStatus == 'Sales' || $transactionStatus == 'Local') {
            $productRawMatCode = $record['product_code'] ?? 'N/A';
        } else {
            $productRawMatCode = $record['raw_mat_code'] ?? 'N/A';
        }
        
        if (!isset($grouped[$productRawMatCode])) {
            $grouped[$productRawMatCode] = [
                'records' => [],
            ];
        }
        
        $grouped[$productRawMatCode]['records'][] = $record;
    }
    
    return $grouped;
}

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
        $searchQuery .= " and is_complete = 'Y'";
    }elseif ($_POST['status'] == 'Cancelled'){
        $searchQuery .= " and is_cancel = 'Y'";
    }elseif ($_POST['status'] == 'Pending'){
        $searchQuery .= " and is_complete='N' AND is_cancel='N'";
    }
    else{
        $searchQuery .= " and is_complete = 'Y'";
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
        if ($_POST['reportType'] == 'DR') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY transaction_id ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y'".$searchQuery.' ORDER BY transaction_id ASC';
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
                                    <style>
                                        @media print {
                                            @page {
                                                margin: 0.5in;
                                            }
                                            #printButton {
                                                display: none;
                                            }
                                        }
                                        body { font-family: Arial, sans-serif; font-size: 12px; }
                                        .header { text-align: left; margin: 30px 0 10px 20px; }
                                        .company-name { font-weight: bold; font-size: 20px; }
                                        .report-title { font-weight: bold; font-size: 18px;  margin-bottom: 10px; }
                                        .period-info { font-size: 12px; margin-bottom: 10px; }
                                        table { width: 100%; border-collapse: collapse; font-size: 10px; }
                                        th, td { padding: 3px; text-align: center; }
                                        th { font-weight: bold; }
                                        .total-row { font-weight: bold; }
                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 5px;
                                            cursor: pointer;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <!--button id="printButton" class="print-button" onclick="window.print()">Print Report</button-->
                                    <div class="header">
                                        <div class="company-name">'.$companyName.'</div>
                                        <div class="report-title">RECORD : DAILY CASH INTAKE</div>
                                        <div class="period-info">
                                            Period : '.date('d M Y', strtotime($_POST['fromDate'])).'<br>
                                            Time : '.date('H:i:s').'<br>
                                        </div>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr style="border-bottom: 1px solid black;">
                                                <th style="text-align:left;">Name</th>
                                                <th>Time In</th>
                                                <th>Time Out</th>
                                                <th>Ticket No</th>
                                                <th>Vehicle No</th>
                                                <th>Net WT<br>(MT)</th> 
                                                <th>Unit<br>Price</th>
                                                <th>Transport</th>
                                                <th>Harvesting</th>
                                                <th>Total<br>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        
                                        $grandTotalNet = 0;
                                        $grandTotalAmount = 0;
                                        $grandTotalTransport = 0;
                                        $grandTotalHarvesting = 0;
                                        
                                        while ($row = $result->fetch_assoc()) {
                                            $grandTotalNet += (float)$row['nett_weight1'];
                                            $grandTotalAmount += (float)$row['total_price'];
                                            // $grandTotalTransport += (float)$row['transport_charges'];
                                            // $grandTotalHarvesting += (float)$row['harvesting_charges'];
                                            
                                            $timeIn = date('d-m-Y H:i', strtotime($row['gross_weight1_date']));
                                            $timeOut = date('d-m-Y H:i', strtotime($row['tare_weight1_date']));
                                            
                                            $customerName = $row['transaction_status'] == 'Sales' ? $row['customer_name'] : $row['supplier_name'];

                                            $message .= '<tr>
                                                <td style="text-align:left;">' . $customerName . '</td>
                                                <td>' . $timeIn . '</td>
                                                <td>' . $timeOut . '</td>
                                                <td>' . $row['transaction_id'] . '</td>
                                                <td>' . $row['lorry_plate_no1'] . '</td>
                                                <td>' . number_format($row['nett_weight1']/1000, 2) . '</td>
                                                <td>' . number_format($row['unit_price'], 2) . '</td>
                                                <td>0.00</td>
                                                <td>0.00</td>
                                                <td>' . number_format($row['total_price'], 2) . '</td>
                                            </tr>';
                                        }
                                                                                
                                    $message .= '
                                        </tbody>
                                        <tfoot>
                                            <tr class="total-row">
                                                <td colspan="3"></td>
                                                <td colspan="2" style="text-align:left;border-top: 1px solid black;">Daily Grand Total :</td>
                                                <td style="border-top: 1px solid black;">'.number_format($grandTotalNet/1000, 2).'</td>
                                                <td style="border-top: 1px solid black;"></td>
                                                <td style="border-top: 1px solid black;">'.number_format($grandTotalTransport, 2).'</td>
                                                <td style="border-top: 1px solid black;">'.number_format($grandTotalHarvesting, 2).'</td>
                                                <td style="border-top: 1px solid black;">'.number_format($grandTotalAmount, 2).'</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div style="margin-top: 20px; margin-left: 20px;">
                                        <div><b>Checked By :  &nbsp;&nbsp;___________________________</b></div><br>
                                        <div><b>Remark : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;___________________________</b></div>
                                    </div>
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
        else if ($_POST['reportType'] == 'DMR') {
            if ($isMulti == 'Y'){
                $id = $_POST['id'];
                $sql = "select * from Weight WHERE id IN ($id) ORDER BY transaction_id ASC";
            }else{
                $sql = "select * from Weight WHERE is_complete = 'Y'".$searchQuery.' ORDER BY transaction_id ASC';
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
                    $allRecords = $result->fetch_all(MYSQLI_ASSOC);
                    $groupedData = groupWeighingByProduct($allRecords, $_POST['transactionStatus']);
    
                    $message = '<html>
                                <head>
                                    <style>
                                        @media print {
                                            @page {
                                                margin: 0.5in;
                                            }
                                            #printButton {
                                                display: none;
                                            }
                                        }
                                        body { font-family: Arial, sans-serif; font-size: 12px; }
                                        .header { text-align: left; margin: 30px 0 10px 20px; }
                                        .company-name { font-weight: bold; font-size: 20px; }
                                        .report-title { font-weight: bold; font-size: 18px;  margin-bottom: 10px; }
                                        .period-info { font-size: 14px; margin-bottom: 10px; }
                                        table { width: 100%; border-collapse: collapse; font-size: 10px; }
                                        th, td { padding: 3px; text-align: center; }
                                        th { font-weight: bold; }
                                        .total-row { font-weight: bold; }
                                        .print-button {
                                            position: fixed;
                                            bottom: 20px;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 12px 20px;
                                            border-radius: 5px;
                                            cursor: pointer;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="header">
                                        <div class="company-name">'.$companyName.'</div>
                                        <div class="report-title">MANAGEMENT REPORT : DAILY INTAKE</div>
                                        <div class="period-info">
                                            <br>
                                            Date: '.date('d M Y', strtotime($_POST['fromDate'])).' To '.date('d M Y', strtotime($_POST['toDate'])).'<br>
                                        </div>
                                    </div>

                                    <table>
                                        <thead>
                                            <tr>
                                                <th colspan="5"></th>
                                                <th colspan="6">Weight (MT)</th>
                                                <th></th>
                                                <th colspan="2">Mode</th>
                                            </tr>
                                            <tr style="border-bottom: 1px solid black;">
                                                <th style="text-align:left;">Code</th>
                                                <th>'.($_POST['transactionStatus'] == 'Sales' || $_POST['transactionStatus'] == 'Local' ? 'Customer Name' : 'Supplier Name').'</th>
                                                <th>Time In/Time Out</th>
                                                <th>Ticket No</th>
                                                <th>Vehicle <br>No</th>
                                                <th>Act. <br>Gross</th>
                                                <th>Gross</th>
                                                <th>Act. <br>Tare</th>
                                                <th>Tare</th>
                                                <th>Act. <br>Net WT.</th> 
                                                <th>Net WT.</th> 
                                                <th>Earn/Loss</th>
                                                <th>IN</th>
                                                <th>OUT</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                                        if (isset($groupedData) && !empty($groupedData)) {
                                            $grandTotalActGross = 0;
                                            $grandTotalGross = 0;
                                            $grandTotalActTare = 0;
                                            $grandTotalTare = 0;
                                            $grandTotalActNet = 0;
                                            $grandTotalNet = 0;
                                            $grandTotalEarnLoss = 0;

                                            foreach ($groupedData as $productRawMatCode => $data) {
                                                $totalActGross = 0;
                                                $totalGross = 0;
                                                $totalActTare = 0;
                                                $totalTare = 0;
                                                $totalActNet = 0;
                                                $totalNet = 0;
                                                $totalEarnLoss = 0;

                                                // Product/Raw Material Code Header Row
                                                $message .= '
                                                    <tr style="background-color: #f0f0f0;">
                                                        <td colspan="14" style="text-align:left; font-weight:bold; padding: 8px;"><span style="border-bottom: 1px solid black;">' . $productRawMatCode . '</span></td>
                                                    </tr>
                                                ';

                                                foreach ($data['records'] as $record) {
                                                    $actNettWeight = (float)$record['nett_weight1'] ?? 0;
                                                    $nettWeight = (float)$record['nett_deduction1'] ?? 0;
                                                    $earnLoss = $actNettWeight - $nettWeight;

                                                    $totalActGross += (float)$record['gross_weight1'];
                                                    $totalGross += (float)$record['gross_deduction1'];
                                                    $totalActTare += (float)$record['tare_weight1'];
                                                    $totalTare += (float)$record['tare_deduction1'];
                                                    $totalActNet += (float)$record['nett_weight1'];
                                                    $totalNet += (float)$record['nett_deduction1'];
                                                    $totalEarnLoss += $earnLoss;

                                                    $timeInOut = date('d-m-Y H:i', strtotime($record['gross_weight1_date'])) . ' - ' . date('H:i', strtotime($record['tare_weight1_date']));

                                                    $message .= '
                                                        <tr>
                                                            <td style="text-align:left;"></td>
                                                            <td>' . ($record['transaction_status'] == 'Sales' ? $record['customer_name'] : $record['supplier_name']) . '</td>
                                                            <td>' . $timeInOut . '</td>
                                                            <td>' . $record['transaction_id'] . '</td>
                                                            <td>' . $record['lorry_plate_no1'] . '</td>
                                                            <td>' . number_format($record['gross_weight1']/1000, 2) . '</td>
                                                            <td>' . number_format($record['gross_deduction1']/1000, 2) . '</td>
                                                            <td>' . number_format($record['tare_weight1']/1000, 2) . '</td>
                                                            <td>' . number_format($record['tare_deduction1']/1000, 2) . '</td>
                                                            <td>' . number_format($record['nett_weight1']/1000, 2) . '</td>
                                                            <td>' . number_format($record['nett_deduction1']/1000, 2) . '</td>
                                                            <td>' . number_format($earnLoss/1000, 2) . '</td>
                                                            <td>' . ($record['manual_weight'] == 'true' ? 'M' : 'A') . '</td>
                                                            <td>' . ($record['manual_weight'] == 'true' ? 'M' : 'A') . '</td>
                                                        </tr>
                                                    ';
                                                }

                                                // Totals for each product/raw material
                                                $message .= '
                                                    <tr class="total-row">
                                                        <td colspan="3"></td>
                                                        <td colspan="2" style="text-align:left;border-top: 1px solid black;">Total :</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalActGross/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalGross/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalActTare/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalTare/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalActNet/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalNet/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;">' . number_format($totalEarnLoss/1000, 2) . '</td>
                                                        <td style="border-top: 1px solid black;"></td>
                                                        <td style="border-top: 1px solid black;"></td>
                                                    </tr>
                                                ';

                                                $grandTotalActGross += $totalActGross;
                                                $grandTotalGross += $totalGross;
                                                $grandTotalActTare += $totalActTare;
                                                $grandTotalTare += $totalTare;
                                                $grandTotalActNet += $totalActNet;
                                                $grandTotalNet += $totalNet;
                                                $grandTotalEarnLoss += $totalEarnLoss;
                                            }

                                            // Grand Totals
                                            $message .= '
                                                <tr class="total-row">
                                                    <td colspan="3"></td>
                                                    <td colspan="2" style="text-align:left; border-top: 1px solid black; border-bottom: 1px solid black;">Daily Grand Total :</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalActGross/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalGross/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalActTare/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalTare/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalActNet/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalNet/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">' . number_format($grandTotalEarnLoss/1000, 2) . '</td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;"></td>
                                                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;"></td>
                                                </tr>
                                            ';
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