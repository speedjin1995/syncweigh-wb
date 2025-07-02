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

if(isset($_POST['userID'], $_POST["file"])){
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
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
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
                    
                    $message = 
                    '<html>
                        <head>
                            <style>
                                @media print {
                                    @page {
                                        size: A5 landscape;
                                        margin-left: 0.5in;
                                        margin-right: 0.5in;
                                        margin-top: 0.1in;
                                        margin-bottom: 0.1in;
                                    }
                                    
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
                            <table style="width:100%;">
                                <tr>
                                    <td style="width: 60%;">
                                        <p>
                                            <span style="font-weight: bold;font-size: 16px;">'.$compname.'</span><br><br>
                                            <span style="font-size: 14px;">'.$compaddress.'</span><br>
                                            <span style="font-size: 14px;">'.$compaddress2.'</span><br>
                                            <span style="font-size: 14px;">'.$compaddress3.'</span><br>
                                            <span style="font-size: 14px;">Tel/Fax: '.$compphone.' / '.$compiemail.'</span>
                                        </p>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="vertical-align: top; margin-left:50px;">
                                            <span style="font-size: 16px; font-weight: bold;">'. $transacationStatus .' Slip</span><br><br>
                                            <span style="font-size: 14px;">Ticket No: <b style="font-size: 14px;">'.$row['transaction_id'].'</b></span><br>
                                            <span style="font-size: 14px;">D/O No: '.$row['delivery_no'].'</span><br>
                                            <span style="font-size: 14px;">P/O No: '.$row['purchase_order'].'</span><br>
                                            <span style="font-size: 14px;">Date: '.$transactionDate.'</span><br>
                                        </p>
                                    </td>
                                </tr>
                                <tr style="visibility:hidden;">
                                    <td style="height: 5px">Placeholder for empty space</td>
                                </tr>
                                <tr style="border-top: 1px solid black;">
                                    <td style="vertical-align: top; width: 65%;">
                                        <p style="margin-top: 5px;">';
                                        if ($row['transaction_status'] == 'Sales'){
                                            $message .= '
                                                <span style="font-size: 14px;">Customer Name: <span style="margin-left: 5px; font-size: 14px;">'.$customer.'</span></span><br>
                                                <span style="font-size: 14px;margin-left: 105px;">'.$customerA.' '.$customerA2.'</span><br>
                                                <span style="font-size: 14px;margin-left: 105px;">'.$customerA3.'</span><br>
                                                <span style="font-size: 14px;margin-left: 105px;"></span><br>
                                            ';
                                        }
                                        elseif ($row['transaction_status'] == 'Local') {
                                            $message .= '
                                                <span style="font-size: 14px;">Transfer To: <span style="margin-left: 7px; font-size: 14px;">'.$customer.'</span></span><br>
                                                <span style="font-size: 14px;margin-left: 80px;">'.$customerA.' '.$customerA2.'</span><br>
                                                <span style="font-size: 14px;margin-left: 80px;">'.$customerA3.'</span><br>
                                                <span style="font-size: 14px;margin-left: 80px;"></span><br>
                                            ';
                                        }
                                        elseif ($row['transaction_status'] == 'Misc') {
                                            $message .= '
                                                <span style="font-size: 14px;">Miscellaneous Slip: <span style="margin-left: 10px; font-size: 14px;">'.$customer.'</span></span><br>
                                                <span style="font-size: 14px;margin-left: 125px;">'.$customerA.' '.$customerA2.'</span><br>
                                                <span style="font-size: 14px;margin-left: 125px;">'.$customerA3.'</span><br>
                                                <span style="font-size: 14px;margin-left: 125px;"></span><br>
                                            ';
                                        }
                                        else{
                                            $message .= '
                                                <span style="font-size: 14px;">Supplier Name: <span style="margin-left: 8px; font-size: 14px;">'.$customer.'</span></span><br>
                                                <span style="font-size: 14px;margin-left: 100px;">'.$customerA.' '.$customerA2.'</span><br>
                                                <span style="font-size: 14px;margin-left: 100px;">'.$customerA3.'</span><br>
                                                <span style="font-size: 14px;margin-left: 100px;"></span><br>
                                            ';
                                        }
                                        $message .= '
                                        </p>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="vertical-align: top; margin-top: 5px; margin-left:50px;">';
                                            if ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc'){
                                                $message .= '<span style="font-size: 14px;">Order Weight: '.($orderSuppWeight != null ? formatWeight($orderSuppWeight).' kg' : '-').'</span>';
                                            }
                                            else{
                                                $message .= '<span style="font-size: 14px;">Supply Weight: '.($orderSuppWeight != null ? formatWeight($orderSuppWeight).' kg' : '-').'</span>';
                                            }
                                        $message .='    
                                            <br><span style="font-size: 14px;">Variance: '.($weightDifference ? formatWeight($weightDifference).' kg' : '-').'</span><br>
                                            <span style="font-size: 14px;">Transporter: '.$row["transporter"].'</span><br>
                                            <span style="font-size: 14px;">Destination: '.$row["destination"].'</span><br>
                                        </p>

                                        <p style="vertical-align: top; margin-left:50px;"><br>';

                                        $message .= '
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <table style="width:100%; border:0px solid black; margin-top:-20px; margin-bottom: 0px;">
                                <tr>
                                    <th style="border:1px solid black;font-size: 14px;text-align: center;">Container No.1</th>
                                    <th style="border:1px solid black;font-size: 14px;text-align: center;">Seal No.1</th>
                                    <th style="border:1px solid black;font-size: 14px;text-align: center;">Container No.2</th>
                                    <th style="border:1px solid black;font-size: 14px;text-align: center;">Seal No.2</th>
                                </tr>
                                <tr>
                                    <td style="border:1px solid black;font-size: 14px;text-align: center;">'.(!empty($row["container_no"]) ? $row["container_no"] : '&nbsp;').'</td>
                                    <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row["seal_no"].'</td>
                                    <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row["container_no2"].'</td>
                                    <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row["seal_no2"].'</td>
                                </tr>
                            </table>';
                            
                            if($row['weight_type'] == 'Container'){
                                $message .= '<table style="width:100%; border:0px solid black;">';

                                if ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc'){
                                    $message .= '
                                        <tr>
                                            <td colspan="6" style="border:1px solid black; text-align: left; font-size: 14px;">Product Code : '.$row['product_code'].'</td>
                                        </tr>
                                    ';
                                }else{
                                    $message .= '
                                        <tr>
                                            <td colspan="6" style="border:1px solid black; text-align: left; font-size: 14px;">Product Code : '.$row['raw_mat_code'].'</td>
                                        </tr>
                                    ';
                                }

                                $message .= '<tr>
                                        <th style="border:1px solid black;font-size: 14px;text-align: center;">Incoming Date/Time</th>
                                        <th style="border:1px solid black;font-size: 14px;text-align: center;">Outgoing Date/Time</th>
                                        <th colspan="2" style="border:1px solid black;font-size: 14px;text-align: center;">Prime Mover No. & Weight (kg)</th>
                                        <th style="border:1px solid black;font-size: 14px;text-align: center;">Tare (kg)</th>
                                        <th style="border:1px solid black;font-size: 14px;text-align: center;">Nett (kg)</th>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$grossWeightTime.'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$tareWeightTime.'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row['lorry_plate_no1'].'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['gross_weight1']).'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['tare_weight1']).' kg</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['nett_weight1']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$grossWeightTime2.'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$tareWeightTime2.'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row['lorry_plate_no2'].'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['gross_weight2']).'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['tare_weight2']).' kg</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['nett_weight2']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">Final Weight</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight(abs((int)$row['nett_weight1'] - (int)$row['nett_weight2'])).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">Remarks: <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">Reduce Weight</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.formatWeight($row['reduce_weight']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="border:1px solid black;font-size: 14px;font-weight:bold;text-align: center;">Nett Weight</td>
                                        <td style="border:1px solid black;font-size: 14px;font-weight:bold;text-align: center;">'.formatWeight($row['final_weight']).' kg</td>
                                    </tr>
                                </table>';
                                /*if(){
                                    $message .= '<tr>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row['lorry_plate_no2'].'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row['product_name'].'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">RM '.$price.'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">'.$row['nett_weight2'].'</td>
                                        <td style="border:1px solid black;font-size: 14px;text-align: center;">kg</td>
                                        <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight2']), 2, '.', '').'</td>
                                    </tr>';
                                }*/
                            }
                            else{
                                $message .= '<br> 
                                <table style="width:100%; border:0px solid black; margin-top: -20px;"> ';

                                if ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc'){
                                    $message .= '
                                        <tr>
                                            <td colspan="6" style="border:1px solid black; text-align: left; font-size: 14px;">Product Code : '.$row['product_code'].'</td>
                                        </tr>
                                    ';
                                }else{
                                    $message .= '
                                        <tr>
                                            <td colspan="6" style="border:1px solid black; text-align: left; font-size: 14px;">Product Code : '.$row['raw_mat_code'].'</td>
                                        </tr>
                                    ';
                                }

                                $message .= '
                                    <tr>
                                        <th style="border:1px solid black;font-size: 18px;text-align: center;">Vehicle No</th>
                                        <th style="border:1px solid black;font-size: 18px;text-align: center;">Product Code</th>
                                        <th style="border:1px solid black;font-size: 18px;text-align: center;">Date/Time</th>
                                        <th colspan="2" style="border:1px solid black;font-size: 18px;text-align: center;">Weight (kg)</th>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">'.$row['lorry_plate_no1'].'</td>';

                                        if ($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local'){
                                            $message .= '<td style="border:1px solid black;font-size: 16px;text-align: center;">'.$row['raw_mat_code'].'</td>';
                                        }else{
                                            $message .= '<td style="border:1px solid black;font-size: 16px;text-align: center;">'.$row['product_code'].'</td>';
                                        }

                                    $message .= '    
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">'.$grossWeightTime.'</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">1st Weight</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: right;">'.formatWeight($row['gross_weight1']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" ></td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">'.$tareWeightTime.'</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">2nd Weight</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: right;">'.formatWeight($row['tare_weight1']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Remarks: <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">Reduce Weight</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: right;">'.formatWeight($row['reduce_weight']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: center;">Nett Weight</td>
                                        <td style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: right;">'.formatWeight($row['final_weight']).' kg</td>
                                    </tr>
                                </table>';
                            }
                            
                            $message .= '<table style="margin-top:55px">
                                <!--<tr style="visibility: hidden; border:0px;">
                                    <th width="24%">Vehicle No</th>
                                    <th width="23%">Product</th>
                                    <th width="23%">Time</th>
                                    <th colspan="2" width="30%">Weight (kg)</th>
                                </tr>-->
                                <tr>
                                    <td style="vertical-align: top; font-size: 14px;">
                                        <hr width="80%" style="margin-left: 0; text-align: left;">
                                        <span>1st Weight by : '.$row['gross_weight_by1'].'</span><br>
                                        <span>2nd Weight by : '.$row['tare_weight_by1'].'</span>
                                    </td>
                                    <td style="vertical-align: top; font-size: 14px;">
                                        <hr width="80%" style="margin-left: 0; text-align: left;">
                                        <span>Acknowledge By <br> Administrator</span>
                                    </td>
                                    <td style="vertical-align: top; font-size: 14px;">
                                        <hr width="80%" style="margin-left: 0; text-align: left;">
                                        <span>Received By</span><br>
                                        <span>Name: </span><br>
                                        <span>I/C: </span>
                                    </td>
                                <tr>
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