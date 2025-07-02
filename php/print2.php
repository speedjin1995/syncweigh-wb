<?php
session_start();
require_once 'db_connect.php';
include 'phpqrcode/qrlib.php';

$compids = '1';
$compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
$compreg = '123456789-X';
$compaddress = 'No.34, Jalan Bagan 1,';
$compaddress2 = 'Taman Bagan,';
$compaddress3 = '13400 Butterworth. Penang. Malaysia.';
$compphone = '6043325822';
$compiemail = 'admin@synctronix.com.my';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

// Format Weight String
function formatWeight($weight){
    if ($weight != 0){
        $formatted = number_format(ltrim($weight, '0'), 2, '.', ',');
        $formatted = preg_replace('/\.00$/', '', $formatted);    
    }else{
        $formatted = $weight;
    }

    return $formatted;
}

if(isset($_POST['userID'], $_POST["file"], $_POST['isEmptyContainer'])){
    $stmt = $db->prepare("SELECT * FROM Company WHERE id=?");
    $stmt->bind_param('s', $compids);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
            
    if ($row = $result1->fetch_assoc()) {
        $compname = $row['name'];
        $compreg = $row['company_reg_no'];
        $compaddress = $row['address_line_1'];
        $compaddress2 = $row['address_line_2'];
        $compaddress3 = $row['address_line_3'];
        $compphone = $row['phone_no'];
        $compiemail = $row['fax_no'];
    }

    if($_POST["file"] == 'weight'){
        $sql = "SELECT * FROM Weight WHERE id=?";

        if ($select_stmt = $db->prepare($sql)) {
            $select_stmt->bind_param('s', $id);

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
                    
                if ($row = $result->fetch_assoc()) {
                    $customer = '';
                    $customerR = '';
                    $customerP = '';
                    $customerA = '';
                    $customerA2 = '';
                    $customerA3 = '';
                    $customerE = '';

                    $product = '';
                    $price = '';
                    $variance = '';
                    $high = '';
                    $low = '';

                    $transactionDate = date("d/m/Y", strtotime($row['transaction_date']));
                    $grossWeightTime = date("d/m/Y - H:i:s", strtotime($row['gross_weight1_date']));
                    $tareWeightTime = date("d/m/Y - H:i:s", strtotime($row['tare_weight1_date']));

                    $orderSuppWeight = 0;
                    $weightDifference = $row['weight_different'];
                    $finalWeight = $row['final_weight'];

                    $grossWeightTime2 = $row['gross_weight2_date'] != null ? date("d/m/Y - H:i:s", strtotime($row['gross_weight2_date'])) : "";
                    $tareWeightTime2 = $row['tare_weight2_date'] != null ? date("d/m/Y - H:i:s", strtotime($row['tare_weight2_date'])) : "";

                    if ($row['transaction_status'] == 'Sales'){
                        $transacationStatus = 'Dispatch';
                    }elseif ($row['transaction_status'] == 'Purchase'){
                        $transacationStatus = 'Receiving';
                    }elseif ($row['transaction_status'] == 'Local'){
                        $transacationStatus = 'Internal Transfer';
                    }else {
                        $transacationStatus = 'Miscellaneous';
                    }

                    if($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local'){
                        $cid = $row['supplier_code'];
                        $orderSuppWeight = floatval($row['supplier_weight']);

                        if ($update_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=?")) {
                            $update_stmt->bind_param('s', $cid);
                            
                            // Execute the prepared query.
                            if ($update_stmt->execute()) {
                                $result2 = $update_stmt->get_result();
                                
                                if ($row2 = $result2->fetch_assoc()) {
                                    $customer = $row2['name'];
                                    $customerR = $row2['company_reg_no'] ?? '';
                                    $customerP = $row2['phone_no'] ?? '-';
                                    $customerA = $row2['address_line_1'];
                                    $customerA2 = $row2['address_line_2'];
                                    $customerA3 = $row2['address_line_3'];
                                    $customerE = $row2['fax_no'] ?? '-';
                                }
                            }
                        }

                        $pid = $row['raw_mat_code'];
                    
                        if ($update_stmt2 = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=?")) {
                            $update_stmt2->bind_param('s', $pid);
                            
                            // Execute the prepared query.
                            if ($update_stmt2->execute()) {
                                $result3 = $update_stmt2->get_result();
                                
                                if ($row3 = $result3->fetch_assoc()) {
                                    $product = $row3['name'];
                                    $variance = $row3['variance'] ?? '';
                                    $high = $row3['high'] ?? '0';
                                    $low = $row3['low'] ?? '0';
                                    $price = $row3['price'] ??  '0.00';
                                }
                            }
                        }
                    }
                    else{
                        $cid = $row['customer_code'];
                        $orderSuppWeight = floatval($row['order_weight']);
                    
                        if ($update_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=?")) {
                            $update_stmt->bind_param('s', $cid);
                            
                            // Execute the prepared query.
                            if ($update_stmt->execute()) {
                                $result2 = $update_stmt->get_result();
                                
                                if ($row2 = $result2->fetch_assoc()) {
                                    $customer = $row2['name'];
                                    $customerR = $row2['company_reg_no'] ?? '';
                                    $customerP = $row2['phone_no'] ?? '-';
                                    $customerA = $row2['address_line_1'];
                                    $customerA2 = $row2['address_line_2'];
                                    $customerA3 = $row2['address_line_3'];
                                    $customerE = $row2['fax_no'] ?? '-';
                                }
                            }
                        }

                        $pid = $row['product_code'];
                    
                        if ($update_stmt2 = $db->prepare("SELECT * FROM Product WHERE product_code=?")) {
                            $update_stmt2->bind_param('s', $pid);
                            
                            // Execute the prepared query.
                            if ($update_stmt2->execute()) {
                                $result3 = $update_stmt2->get_result();
                                
                                if ($row3 = $result3->fetch_assoc()) {
                                    $product = $row3['name'];
                                    $variance = $row3['variance'] ?? '';
                                    $high = $row3['high'] ?? '0';
                                    $low = $row3['low'] ?? '0';
                                    $price = $row3['price'] ??  '0.00';
                                }
                            }
                        }
                    }

                    $message = '';
                    $message = 
                        '<html>
                            <head>
                                <style>
                                    @page {
                                        size: A5 landscape;
                                        margin: 15mm;
                                    }

                                    * {
                                        margin: 0;
                                        padding: 0;
                                        box-sizing: border-box;
                                    }

                                    body {
                                        font-family: Arial, sans-serif;
                                        font-size: 14px;
                                        line-height: 1.3;
                                        color: #000;
                                        background: white;
                                        padding: 10px;
                                    }

                                    .dispatch-slip {
                                        width: 100%;
                                        height: calc(100vh - 30px);
                                        padding: 5px;
                                        display: flex;
                                        flex-direction: column;
                                        margin: 5px;
                                    }

                                    .header {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: flex-start;
                                        margin-bottom: 8px;
                                        border-bottom: 1px solid #000;
                                        padding-bottom: 5px;
                                    }

                                    .company-info {
                                        flex: 1;
                                    }

                                    .company-name {
                                        font-size: 24px;
                                        font-weight: bold;
                                        margin-bottom: 6px;
                                    }

                                    .company-address {
                                        font-size: 12px;
                                        line-height: 1.4;
                                    }

                                    .slip-info {
                                        text-align: right;
                                        flex-shrink: 0;
                                    }

                                    .slip-title {
                                        font-size: 14px;
                                        font-weight: bold;
                                        text-decoration: underline;
                                        margin-bottom: 6px;
                                    }

                                    .slip-details {
                                        font-size: 12px;
                                        line-height: 1.4;
                                    }

                                    .lorry-section {
                                        margin: 5px 0;
                                        padding-bottom: 5px;
                                    }

                                    .lorry-plate {
                                        display: flex;
                                        align-items: center;
                                        gap: 12px;
                                    }

                                    .lorry-label {
                                        font-weight: bold;
                                        font-size: 12px;
                                    }

                                    .chinese-label {
                                        font-size: 12px;
                                        color: #666;
                                    }

                                    .plate-number {
                                        font-size: 24px;
                                        font-weight: bold;
                                        letter-spacing: 2px;
                                    }

                                    .weight-table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        margin: 5px 0;
                                        flex: 1;
                                    }

                                    .weight-table th,
                                    .weight-table td {
                                        border: 1px solid #000;
                                        padding: 5px;
                                        text-align: center;
                                        vertical-align: middle;
                                    }

                                    .weight-table th {
                                        background-color: #f5f5f5;
                                        font-weight: bold;
                                        font-size: 13px;
                                    }

                                    .weight-table .chinese-row {
                                        font-size: 11px;
                                        color: #666;
                                        background-color: #f9f9f9;
                                    }

                                    .weight-data {
                                        font-size: 14px;
                                        font-weight: 500;
                                    }

                                    .footer {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: flex-end;
                                        margin-top: auto;
                                        padding-top: 3px;
                                    }

                                    .signature-section {
                                        flex: 1;
                                    }

                                    .signature-line {
                                        border-bottom: 1px solid #000;
                                        width: 220px;
                                        margin-bottom: 6px;
                                    }

                                    .signature-label {
                                        text-align: center;
                                        font-size: 13px;
                                        margin-bottom: 10px;
                                    }

                                    .signature-fields {
                                        font-size: 13px;
                                        line-height: 1.6;
                                    }

                                    .disclaimer {
                                        flex: 2;
                                        text-align: left;
                                        font-size: 10px;
                                        line-height: 1.4;
                                        margin-left: 80px;
                                    }

                                    .disclaimer-en {
                                        margin-bottom: 3px;
                                        font-weight: 500;
                                    }

                                    .disclaimer-auth {
                                        margin-bottom: 3px;
                                        font-weight: 500;
                                    }

                                    .disclaimer-cn {
                                        color: black;
                                        font-weight: bold;
                                    }

                                    @media print {
                                        body {
                                            -webkit-print-color-adjust: exact;
                                            print-color-adjust: exact;
                                        }
                                    }

                                    @media screen {
                                        body {
                                            background-color: #f0f0f0;
                                        }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="dispatch-slip">
                                    <!-- Header Section -->
                                    <div class="header">
                                        <div class="company-info">
                                            <div class="company-name">'.$compname.'</div>
                                            <div class="company-address">
                                                '.$compaddress.' '.$compaddress2.'<br>
                                                '.$compaddress3.'
                                            </div>
                                        </div>
                                        <div class="slip-info">
                                            <div class="slip-title">'. $transacationStatus .' SLIP</div>
                                            <div class="slip-details">
                                                '.$row['transaction_id'].'<br>
                                                DATE : '.$transactionDate.'
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lorry Plate Section -->
                                    <div class="lorry-section">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                            <div class="lorry-plate">
                                                <span class="lorry-label">Lorry Plate<br>(车牌号)</span>
                                                <span style="margin: 0 10px; font-size: 13px;">&nbsp;&nbsp;:</span>
                                                <span class="plate-number">'.$row['lorry_plate_no1'].'</span>
                                            </div>
                                            <div class="lorry-plate">
                                                <span class="lorry-label">Container No.<br>(货柜号)</span>
                                                <span style="margin: 0 10px; font-size: 13px;">:</span>
                                                <span class="plate-number">'.$row['container_no'].'</span>
                                            </div>
                                        </div>
                                        <div class="lorry-plate">
                                            <span class="lorry-label">Product<br>(产品)</span>
                                            <span style="margin: 0 10px; font-size: 13px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
                                            <span class="plate-number">'.$product.'</span> 
                                        </div>
                                    </div>

                                    <!-- Weight Table -->
                                    <table class="weight-table">
                                        <thead>
                                            <tr>
                                                <th>In Weight<br>进重(kg)</th>
                                                <th>1st Date / Time<br>进重日期时间</th>
                                                <th>Out Weight<br>出重(kg)</th>
                                                <th>2nd Date / Time<br>出重日期时间</th>
                                                <th>Tare Weight<br>皮重(kg)</th>
                                                <th>Nett Weight<br>净重(kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="weight-data">
                                                <td>'.formatWeight($row['gross_weight1']).' kg</td>
                                                <td>'.$grossWeightTime.'</td>
                                                <td>'.formatWeight($row['tare_weight1']).' kg</td>
                                                <td>'.$tareWeightTime.'</td>
                                                <td>'.formatWeight($row['reduce_weight']).' kg</td>
                                                <td>'.formatWeight($row['final_weight']).' kg</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="font-size:20px;">REMARKS: '.$row['remarks'].'</p>

                                    <!-- Footer Section -->
                                    <div class="footer" style="margin-top: 60px;">
                                        <div class="signature-section">
                                            <div class="signature-line"></div>
                                            <div class="signature-label">(Received By)</div>
                                            <div class="signature-fields">
                                                <strong>Name :</strong><br>
                                                <strong>I/C No :</strong>
                                            </div>
                                        </div>
                                        <div class="disclaimer">
                                            <div class="disclaimer-en">THIS WEIGHING SLIP IS COMPUTER GENERATED AND REQUIRES NO SIGNATURE & CHOP</div>
                                            <div class="disclaimer-auth">AUTHORISED BY: '.$compname.'</div>
                                            <div class="disclaimer-cn">此称重单由电脑生成, 无需签名印章. 授权人: '.$compname.'</div>
                                        </div>
                                    </div>
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
                else{
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => 'Unable to read data'
                        )
                    );
                }
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
        $empQuery = "select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
        count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
        count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
        count.remark, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
        count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
        count.productName = products.id AND status.id=count.status AND units.id=count.unit AND count.deleted = '0' AND count.id=?";

        if ($select_stmt = $db->prepare($empQuery)) {
            $select_stmt->bind_param('s', $id);

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
                

                if ($row = $result->fetch_assoc()) {
                    $message = '<html>
                    <head>
                        <title>Html to PDF</title>
                    </head>
                    <body>
                        <h3>'.$compname.'</h3>
                        <p>No.34, Jalan Bagan 1, <br>Taman Bagan, 13400 Butterworth.<br> Penang. Malaysia.</p>
                        <p>TEL: 6043325822 | EMAIL: admin@synctronix.com.my</p><hr>
                        <table style="width:100%">
                        <tr>
                            <td>
                                <h4>CUSTOMER NAME: '.$row['customer_name'].'</h4>
                            </td>
                            <td>
                                <h4>SERIAL NO: '.$row['serialNo'].'</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>No.34, Jalan Bagan 1, <br>Taman Bagan, <br>13400 Butterworth. Penang. Malaysia.</p>
                            </td>
                            <td>
                                <h4>Status: '.$row['status'].'</h4>
                                <p>Date: 23/03/2022<br>Delivery No: '.$row['deliveryNo'].'</p>
                            </td>
                        </tr>
                        </table>
                        <table style="width:100%; border:1px solid black;">
                        <tr>
                            <th style="border:1px solid black;">Vehicle No.</th>
                            <th style="border:1px solid black;">Product Name</th>
                            <th style="border:1px solid black;">Date & Time</th>
                            <th style="border:1px solid black;">Weight</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">'.$row['veh_number'].'</td>
                            <td style="border:1px solid black;">'.$row['product_name'].'</td>
                            <td style="border:1px solid black;">'.$row['dateTime'].'</td>
                            <td style="border:1px solid black;">'.$row['unitWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Tare Weight</td>
                            <td style="border:1px solid black;">'.$row['tare'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Net Weight</td>
                            <td style="border:1px solid black;">'.$row['actualWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">M.O.Q</td>
                            <td style="border:1px solid black;">'.$row['moq'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Total Weight</td>
                            <td style="border:1px solid black;">'.$row['totalWeight'].' '.$row['units'].'</td>
                        </tr>
                        </table>
                        <p>Remark: '.$row['remark'].'</p>
                    </body>
                </html>';
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
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