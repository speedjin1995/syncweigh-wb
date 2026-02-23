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
$compmpob = '';
$compmpobexpiry = '';
$compmspo = '';
$compmspoexpiry = '';
 
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
        $compmpob = $row['mpob_no'];
        $compmpobexpiry = ($row['mpob_expiry_date'] ? date('d/m/Y', strtotime($row['mpob_expiry_date'])) : '');
        $compmspo = $row['mspo_no'];
        $compmspoexpiry = ($row['mspo_expiry_date'] ? date('d/m/Y', strtotime($row['mspo_expiry_date'])) : '');
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
                    $customerMpob = '';
                    $customerMpso = '';

                    $product = '';
                    $price = '';
                    $variance = '';
                    $high = '';
                    $low = '';

                    $transactionDate = date("d/m/Y", strtotime($row['transaction_date']));
                    $grossWeightTime = $row['gross_weight1_date'] ? date("d M Y H:i:s", strtotime($row['gross_weight1_date'])) : '';
                    $tareWeightTime = $row['tare_weight1_date'] ? date("d M Y H:i:s", strtotime($row['tare_weight1_date'])) : '';

                    $orderSuppWeight = 0;
                    $weightDifference = $row['weight_different'];
                    $finalWeight = $row['final_weight'];

                    $grossWeightTime2 = $row['gross_weight2_date'] ? date("d/m/Y - H:i:s", strtotime($row['gross_weight2_date'])) : "";
                    $tareWeightTime2 = $row['tare_weight2_date'] ? date("d/m/Y - H:i:s", strtotime($row['tare_weight2_date'])) : "";

                    if ($row['transaction_status'] == 'Sales'){
                        $transacationStatus = 'SALE';
                    }elseif ($row['transaction_status'] == 'Purchase'){
                        $transacationStatus = 'PURCHASE';
                    }elseif ($row['transaction_status'] == 'Local'){
                        $transacationStatus = 'Public';
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
                                    $customerMpob = $row2['mpob'] ?? '';
                                    $customerMpso = $row2['mspo_no'];
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
                        
                    $gradingDetail = $row['grade_detail'] ? json_decode($row['grade_detail'], true) : null;

                    $message = '
                        <html>
                        <head>
                            <meta charset="UTF-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                            <title>Weight Ticket</title>
                            <style>
                            @page {
                                size: A5 landscape;
                                margin: 10mm;
                            }

                            body {
                                font-family: Arial, sans-serif;
                                font-size: 12px;
                                margin: 0;
                                padding: 10px;
                                width: 210mm;
                            }

                            .header {
                                text-align: center;
                                margin-bottom: 15px;
                                border-bottom: 2px solid #000;
                                padding-bottom: 10px;
                                position: relative;
                            }

                            .header .sale-badge {
                                position: absolute;
                                right: 30px;
                                bottom: -12px;
                                background: white;
                                padding: 0 10px;
                                font-weight: bold;
                                font-size: 12pt;
                            }

                            .header h2 {
                                margin: 0;
                                font-size: 14pt;
                                font-weight: bold;
                            }

                            .header p {
                                margin: 2px 0;
                                font-size: 9pt;
                            }

                            .section {
                                margin-bottom: 15px;
                            }

                            .row {
                                display: flex;
                                margin-bottom: 5px;
                            }

                            .label {
                                width: 120px;
                                font-weight: normal;
                            }

                            .value {
                                flex: 1;
                            }

                            .two-column {
                                display: flex;
                                gap: 20px;
                            }

                            .column {
                                flex: 1;
                            }

                            .right-align {
                                text-align: right;
                            }

                            .weight-section {
                                margin-top: 20px;
                                border-top: 1px solid #000;
                                padding-top: 10px;
                            }

                            .weight-row {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 8px;
                            }

                            .signature-line {
                                border-bottom: 1px solid #000;
                                margin-top: 30px;
                                padding-top: 40px;
                            }

                            @media print {
                                body {
                                margin: 0;
                                }
                            }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h2>'.$compname.' ('.$compreg.')</h2>
                                <p>'.$compaddress.'</p>
                                <p>'.$compaddress2.'</p>
                                <p>'.$compaddress3.'</p>
                                <p>MPOB License No '.$compmpob.' Expired On '.$compmpobexpiry.'</p>
                                <p>MSPO Cert. No '.$compmspo.' Expired On '.$compmspoexpiry.'</p>
                                <p>Tel: '.$compphone.' Fax: '.$compiemail.'</p>
                                <span class="sale-badge">'.$transacationStatus.'</span>    
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px">
                                <tr>
                                    <td style="width: 120px">Name:</td>
                                    <td style="width: calc(60% - 120px)">'.$customer.'</td>
                                    <td style="width: 100px">WB Ticket:</td>
                                    <td style="width: calc(40% - 100px)">'.$row['transaction_id'].'</td>
                                </tr>
                                <tr>
                                    <td>Address:</td>
                                    <td rowspan="4" style="vertical-align: top">
                                        '.$customerA.'<br>
                                        '.$customerA2.'<br>
                                        '.$customerA3.'
                                    </td>
                                    <td>Time In:</td>
                                    <td>'.$grossWeightTime.'</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Time Out:</td>
                                    <td>'.$tareWeightTime.'</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Vehicle No:</td>
                                    <td>'.$row['lorry_plate_no1'].'</td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>MPOB No:</td>
                                    <td>'.$customerMpob.' MSPO Cert No '.$customerMpso.'</td>
                                    <td>DO No:</td>
                                    <td>'.$row['delivery_no'].'</td>
                                </tr>
                            ';

                        if ($row['transaction_status'] == 'Sales'){
                            $message .= '
                                <tr>
                                    <td>Product:</td>
                                    <td>'.$product.'</td>
                                    <td>Seal No:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Our Contract No:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Buyer Contract No:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Moisture %:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Dirt %:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>FFA %:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </table>
                            ';
                        }else{
                            $message .= '
                                </table>
                                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px">
                                    <tr style="border-top: 1px solid black; padding-top: 10px">
                                        <td width="15%">Product:</td>
                                        <td colspan="4">'.$product.'</td>
                                        <td>Source:</td>
                                        <td colspan="3">'.$row['plant_name'].'</td>
                                    </tr>
                                    <tr>
                                        <td>B.Size:</td>
                                        <td>> 25</td>
                                        <td>> 10</td>
                                        <td>> 9-10</td>
                                        <td>> 8-9</td>
                                        <td>> 7-8</td>
                                        <td>> 6-7</td>
                                        <td>> 5-6</td>
                                        <td>5 <</td>
                                    </tr>
                                    <tr>
                                        <td>Percent (%):</td>
                                        <td>'.$gradingDetail['bunch_size_25'].'</td>
                                        <td>'.$gradingDetail['bunch_size_10'].'</td>
                                        <td>'.$gradingDetail['bunch_size_9_10'].'</td>
                                        <td>'.$gradingDetail['bunch_size_8_9'].'</td>
                                        <td>'.$gradingDetail['bunch_size_7_8'].'</td>
                                        <td>'.$gradingDetail['bunch_size_6_7'].'</td>
                                        <td>'.$gradingDetail['bunch_size_5_6'].'</td>
                                        <td>'.$gradingDetail['bunch_size_5'].'</td>
                                    </tr>
                                    <tr>
                                        <td>Quality Factors:</td>
                                        <td>UR</td>
                                        <td>UD</td>
                                        <td>EM</td>
                                        <td>RB</td>
                                        <td>LS</td>
                                        <td>DB</td>
                                        <td>DD</td>
                                        <td>OB</td>
                                    </tr>
                                    <tr>
                                        <td>Percent (%):</td>
                                        <td>'.$gradingDetail['unripe'].'</td>
                                        <td>'.$gradingDetail['underripe'].'</td>
                                        <td>'.$gradingDetail['empty_bunch'].'</td>
                                        <td>'.$gradingDetail['rotten_bunch'].'</td>
                                        <td>'.$gradingDetail['long_stalks'].'</td>
                                        <td>'.$gradingDetail['dirty_bunch'].'</td>
                                        <td>'.$gradingDetail['dura_bunch'].'</td>
                                        <td>'.$gradingDetail['old_bunch'].'</td>
                                    </tr>
                                    <tr>
                                        <td>Rejected Bunches:</td>
                                        <td>'.number_format($row['reject_weight'], 2).'</td>
                                    </tr>
                                </table>
                            ';
                        }

                        if ($row['transaction_status'] == 'Sales') {
                            $message .= '
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px">
                                    <tr>
                                        <td colspan="6" style="width: 120px; border-bottom: 1px solid black">Broker Contact No:</td>
                                    </tr>
                                    <tr>
                                        <td>Driver Name:</td>
                                        <td>'.$row['driver_name'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td>Gross WT:</td>
                                        <td style="text-align: right">'.number_format($row['gross_weight1'], 0).' Kg</td>
                                    </tr>
                                    <tr>
                                        <td>Driver IC:</td>
                                        <td>'.$row['driver_ic'].'</td>
                                        <td>MSPO</td>
                                        <td>0 KG</td>
                                        <td>Tare WT:</td>
                                        <td style="border-bottom: 1px solid black; text-align: right">'.number_format($row['tare_weight1'], 0).' Kg</td>
                                    </tr>
                                    <tr>
                                        <td>Weighed By:</td>
                                        <td>'.$row['tare_weight_by1'].'</td>
                                        <td>NON MSPO</td>
                                        <td>0 KG</td>
                                        <td>Nett WT:</td>
                                        <td style="border-bottom: 1px solid black; text-align: right">'.number_format($row['nett_weight1'], 0).' Kg</td>
                                    </tr>
                                </table>
                            ';
                        }else{
                            $message .= '
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px">
                                    <tr>
                                        <td>Weighed By:</td>
                                        <td>'.$row['tare_weight_by1'].'</td>
                                        <td>MSPO</td>
                                        <td>'.$gradingDetail['mspo_weight'].' KG</td>
                                        <td>Gross WT:</td>
                                        <td style="text-align: right">'.number_format($row['gross_weight1'], 0).' Kg</td>
                                    </tr>
                                    <tr>
                                        <td>Driver Name:</td>
                                        <td>'.$row['driver_name'].'</td>
                                        <td>NON MSPO</td>
                                        <td>'.number_format($gradingDetail['non_mspo_weight'], 2).' KG</td>
                                        <td>Tare WT:</td>
                                        <td style="border-bottom: 1px solid black; text-align: right">'.number_format($row['tare_weight1'], 0).' Kg</td>
                                    </tr>
                                    <tr>
                                        <td>Remark:</td>
                                        <td colspan="3">'.$row['remarks'].'</td>
                                        <td>F.Material:</td>
                                        <td style="border-bottom: 1px solid black; text-align: right">'.number_format($row['reduce_weight'], 0).' Kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td>Nett WT:</td>
                                        <td style="border-bottom: 1px solid black; text-align: right">'.number_format($row['final_weight'], 0).' Kg</td>
                                    </tr>
                                </table>
                            ';

                        }

                        $message .= '
                            <div style="margin-top: 100px; display: flex; justify-content: space-between">
                                <div style="width: 48%">
                                    <span>Authorised Signature: _____________________</span>
                                </div>
                                <div style="width: 48%; text-align: right">
                                    <span>Received by: _____________________</span>
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