<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
// Weighing screen exports use the Weighing export permission; report exports use the Reports one
$exportCategory = (($_POST['source'] ?? '') == 'weighing') ? 'Weighing' : 'Reports';
if (!hasPermission($exportCategory, ['export'])) {
    exit('You do not have permission to export');
}

$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
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

if($_POST["file"] == 'weight'){
    $searchQuery .= transactionStatusExclusion($db, 'Weight.transaction_status');
    $searchQuery .= permissionExclusion($exportCategory, 'Weight.transaction_status', 'Weight.weight_type', 'export');
}
else{
    $searchQuery .= transactionStatusExclusion($db, 'count.transaction_status');
    $searchQuery .= permissionExclusion($exportCategory, 'count.transaction_status', 'count.weight_type', 'export');
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

if($_POST["file"] == 'weight'){
    $searchQuery .= inFilterQuery($db, 'Weight.customer_code', $_POST['customer'] ?? '');
    $searchQuery .= inFilterQuery($db, 'Weight.supplier_code', $_POST['supplier'] ?? '');
}
else{
    $searchQuery .= inFilterQuery($db, 'count.customer_code', $_POST['customer'] ?? '');
    $searchQuery .= inFilterQuery($db, 'count.supplier_code', $_POST['supplier'] ?? '');
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

if($_POST["file"] == 'weight'){
    $searchQuery .= inFilterQuery($db, 'Weight.product_code', $_POST['product'] ?? '');
    $searchQuery .= inFilterQuery($db, 'Weight.raw_mat_code', $_POST['rawMat'] ?? '');
}
else{
    $searchQuery .= inFilterQuery($db, 'count.product_code', $_POST['product'] ?? '');
    $searchQuery .= inFilterQuery($db, 'count.raw_mat_code', $_POST['rawMat'] ?? '');
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

if(isset($_POST['location']) && $_POST['location'] != null && $_POST['location'] != '' && $_POST['location'] != '-' && $_POST["file"] == 'weight'){
    $searchQuery .= " and Weight.location_id = '".intval($_POST['location'])."'";
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

if($_POST['isMulti'] != null && $_POST['isMulti'] != '' && $_POST['isMulti'] != '-'){
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

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        if ($select_stmt = $db->prepare("select * from Weight WHERE status = '0'".$searchQuery.' ORDER BY gross_weight1_date')) {
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    ));
            }
            else{
                $layout = weightExportLayout($db, $select_stmt->get_result()->fetch_all(MYSQLI_ASSOC));
                $statusLabel = weightExportStatusLabel($_POST['transactionStatus'] ?? '');
                $cell = function ($value) {
                    return is_float($value) ? '<td style="text-align: right;">' . number_format($value, 2) . '</td>' : '<td>' . htmlspecialchars((string) $value) . '</td>';
                };

                $message = '<html>
                            <head>
                                <style>
                                    @media print {
                                        @page {
                                            size: A4 landscape;
                                            margin: 0.3in;
                                        }
                                    }
                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                    }
                                    th, td {
                                        border: 1px solid #000000;
                                        font-family: sans-serif;
                                        font-size: 8px;
                                        padding: 3px;
                                    }
                                    th {
                                        background-color: #00B050;
                                        color: #ffffff;
                                        text-align: center;
                                    }
                                    .title {
                                        font-family: sans-serif;
                                        font-size: 11px;
                                        font-weight: bold;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="title">FROM DATE: '.($fromDate ?? '').'   TO DATE: '.($toDate ?? '').'</div>
                                <div class="title">TRANSACTION STATUS : '.$statusLabel.'</div>
                                <br>
                                <table>
                                    <thead>
                                        <tr>';

                foreach ($layout['headers'] as $header) {
                    $message .= '<th>' . htmlspecialchars($header) . '</th>';
                }
                $message .= '</tr>
                                    </thead>
                                    <tbody>';

                foreach ($layout['rows'] as $line) {
                    $message .= '<tr>';
                    foreach ($line as $value) {
                        $message .= $cell($value);
                    }
                    $message .= '</tr>';
                }

                $message .= '<tr style="font-weight: bold;">';
                foreach ($layout['totals'] as $value) {
                    $message .= $cell($value);
                }
                $message .= '</tr>
                                    </tbody>
                                </table>
                                <br>
                                <table style="width: 40%;">
                                    <thead>
                                        <tr><th>SUMMARY DETAILS</th><th>TRIPS</th><th>TONNAGE (MT)</th><th>PRICE (RM)</th></tr>
                                    </thead>
                                    <tbody>';

                foreach ($layout['summary'] as $item) {
                    $message .= '<tr><td>' . htmlspecialchars('SUBTOTAL (' . $item['name'] . ')') . '</td><td>' . $item['trips'] . '</td><td style="text-align: right;">' . number_format($item['tonnage'], 2) . '</td><td style="text-align: right;">' . number_format($item['price'], 2) . '</td></tr>';
                }
                $message .= '<tr style="font-weight: bold;"><td>GRAND TOTAL</td><td>' . $layout['summaryTotal']['trips'] . '</td><td style="text-align: right;">' . number_format($layout['summaryTotal']['tonnage'], 2) . '</td><td style="text-align: right;">' . number_format($layout['summaryTotal']['price'], 2) . '</td></tr>
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
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>