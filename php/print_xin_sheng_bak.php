<?php

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
                    
                    if($row['transaction_status'] == 'Sales'){
                        $cid = $row['customer_code'];
                    
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
                    else{
                        $cid = $row['supplier_code'];
                    
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
                    
                    /*$text = "https://speedjin.com/synctronix/qr.php?id=".$id."&compid=".$compids;
  
                    // $path variable store the location where to 
                    // store image and $file creates directory name
                    // of the QR code file by using 'uniqid'
                    // uniqid creates unique id based on microtime
                    $path = 'images/';
                    $file = $path.uniqid().".png";
                      
                    // $ecc stores error correction capability('L')
                    $ecc = 'L';
                    $pixel_Size = 10;
                    $frame_Size = 10;
                      
                    // Generates QR Code and Stores it in directory given
                    QRcode::png($text, $file, $ecc, $pixel_Size, $frame_size);*/
                    
                    $message = '<html>
    <head>
        <style>
            @media print {
                @page {
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
        <table style="width:100%">
            <tr>
                <td style="width: 60%;">
                    <p>
                        <span style="font-weight: bold;font-size: 16px;">'.$compname.'</span><br><br>
                        <span style="font-size: 12px;">'.$compaddress.'</span><br>
                        <span style="font-size: 12px;">'.$compaddress2.'</span><br>
                        <span style="font-size: 12px;">'.$compaddress3.'</span><br>
                        <span style="font-size: 12px;">TEL: '.$compphone.' / FAX: '.$compiemail.'</span>
                    </p>
                </td>
                <td>
                    <p>
                        <span style="font-weight: bold;font-size: 12px;">Transaction Date. : '.$row['transaction_date'].'</span><br>
                        <span style="font-weight: bold;font-size: 12px;">Transaction No. &nbsp;&nbsp;&nbsp;: '.$row['transaction_id'].'</span><br>
                        <span style="font-size: 12px;">Transaction Status: '.$row['transaction_status'].'</span><br>';
                        
                    if($row['manual_weight'] == 'true'){
                        $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Manual Weighing</span><br>';
                    }
                    else{
                        $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Auto Weighing</span><br>';
                    }
                    
                    $message .= '<span style="font-size: 12px;">Invoice No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['invoice_no'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Delivery No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['delivery_no'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Purchase No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['purchase_order'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Container No. &nbsp;&nbsp;&nbsp;&nbsp;: '.($row['container_no'] ?? '').'</span>
                    </p>
                </td>
            </tr>
        </table>
        <hr>
        <table style="width:100%">
        <tr>
            <td style="width: 40%;">
                <p>
                    <span style="font-weight: bold;font-size: 16px;">'.$customer.'</span><br>
                </p>
            </td>
            <td style="width: 20%;">
                <p>&nbsp;</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span style="font-size: 12px;">'.$customerA.'</span><br>
                    <span style="font-size: 12px;">'.$customerA2.'</span><br>
                    <span style="font-size: 12px;">'.$customerA3.'</span><br>
                    <span style="font-size: 12px;">TEL: '.$customerP.'/ FAX: '.$customerE.'</span>
                </p>
            </td>
            <td style="width: 20%;"></td>
            <td>
                <p>
                    <span style="font-size: 12px;">Weight Date & Time : '.($row['gross_weight1_date'] ?? '').'</span><br>
                    <span style="font-size: 12px;">User Weight &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$row['created_by'].'</span><br>
                </p>
                <table style="width:100%; border:1px solid black;">
                    <tr>';
                if($row['transaction_status'] == 'Sales'){
                    $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Order Weight</th>
                    <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
                    <th style="border:1px solid black; font-size: 14px;">Variance</th>';
                }
                else{
                    $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Supply Weight</th>
                    <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
                    <th style="border:1px solid black; font-size: 14px;">Variance</th>';
                }

                if($row['transaction_status'] == 'Sales'){
                    $final = $row['final_weight'];
                    $trueWeight = $row['order_weight'] ?? '0';
                    $different = 0;

                    if ($variance == 'W') {
                        if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
                            $different = (float)$final < (float)$trueWeight - (float)$low;
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
                            $different = (float)$final < (float)$trueWeight - (float)$high;
                        }
                    } 
                    elseif ($variance == 'P') {
                        if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
                        }
                    }

                    $message .= '</tr>
                    <tr>
                        <td style="border:1px solid black;">'.($row['order_weight'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
                    </tr>';
                }
                else{
                    $final = $row['final_weight'];
                    $trueWeight = $row['supplier_weight'] ?? '0';
                    $different = 0;

                    if ($variance == 'W') {
                        if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
                            $different = (float)$final < (float)$trueWeight - (float)$low;
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
                            $different = (float)$final < (float)$trueWeight - (float)$high;
                        }
                    } 
                    elseif ($variance == 'P') {
                        if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
                        }
                    }

                    $message .= '</tr>
                    <tr>
                        <td style="border:1px solid black;">'.($row['supplier_weight'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
                    </tr>';
                }
                        
                $message .= '</table>
            </td>
        </tr>
        </table><br>
        <table style="width:100%; border:1px solid black;">
            <tr>
                <th style="border:1px solid black;font-size: 14px;">Vehicle No.</th>
                <th style="border:1px solid black;font-size: 14px;">Product Name</th>
                <th style="border:1px solid black;font-size: 14px;">Unit Price</th>
                <th colspan="2" style="border:1px solid black;font-size: 14px;">Total Weight</th>
                <th style="border:1px solid black;font-size: 14px;">Total Price</th>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no1'].'</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
                <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight1'].'</td>
                <td style="border:1px solid black;font-size: 14px;">kg</td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight1']), 2, '.', '').'</td>
            </tr>';

            if($row['weight_type'] == 'Container'){
                $message .= '<tr>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no2'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight2'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">kg</td>
                    <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight2']), 2, '.', '').'</td>
                </tr>';
            }

            $message .= '<tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Subtotal</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['final_weight'].'</td>
                <td style="border:1px solid black;font-size: 14px;">kg</td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sub_total'].'</td>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">SST</td>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sst'].'</td>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Total Price</td>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['total_price'].'</td>
            </tr>
        </table>
        <p>
            <span style="font-size: 12px;font-weight: bold;">Remark: </span>
            <span style="font-size: 12px;">'.$row['remarks'].'</span>
        </p>
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