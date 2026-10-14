<?php
session_start();
require_once 'db_connect.php';
include 'phpqrcode/qrlib.php';

$language = isset($_POST['prePrint']) ? $_POST['prePrint'] : $_SESSION['language'];
$languageArray = $_SESSION['languageArray'];
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
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($_POST['isEmptyContainer'] == 'Y'){
            $sql = "SELECT * FROM Weight_Container WHERE id=?";
            $printTable = 'Weight_Container';
        }else{
            $sql = "SELECT * FROM Weight WHERE id=?";
            $printTable = 'Weight';
        }

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
                    // Already printed before = this is a reprint, mark it as duplicate
                    $isDuplicate = ($row['is_printed'] ?? 'N') == 'Y';
                    $duplicateWatermark = $isDuplicate ? '<div class="watermark">DUPLICATE</div>' : '';

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
                        $transacationStatus = 'dispatch_code';
                    }elseif ($row['transaction_status'] == 'Purchase'){
                        $transacationStatus = 'receiving_code';
                    }elseif ($row['transaction_status'] == 'Local'){
                        $transacationStatus = 'internal_transfer_code';
                    }else {
                        $transacationStatus = 'miscellaneous_code';
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

                    // Alam Flora slip: weights stored in kg, printed in MT
                    $isPurchase = ($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local');
                    $custName = $customer ?: ($isPurchase ? $row['supplier_name'] : $row['customer_name']);
                    $wasteName = $product ?: ($isPurchase ? $row['raw_mat_name'] : $row['product_name']);
                    $timeIn = !empty($row['gross_weight1_date']) ? date("H:i", strtotime($row['gross_weight1_date'])) : '';
                    $timeOut = !empty($row['tare_weight1_date']) ? date("H:i", strtotime($row['tare_weight1_date'])) : '';
                    $firstWeight = number_format((float)$row['gross_weight1'] / 1000, 2, '.', ',');
                    $secondWeight = number_format((float)$row['tare_weight1'] / 1000, 2, '.', ',');
                    $nettWeight = number_format((float)$row['final_weight'] / 1000, 2, '.', ',');
                    $pricePerTon = number_format((float)$row['unit_price'], 2, '.', ',');
                    $amount = number_format((float)$row['total_price'], 2, '.', ',');

                    $message = 
                    '<html>
                        <head>
                            <style>
                                @media print {
                                    @page {
                                        size: A5 landscape;
                                        margin: 0.3in 0.2in;
                                    }
                                }

                                body {
                                    font-family: "Courier New", Courier, monospace;
                                    font-size: 14px;
                                }

                                .header {
                                    text-align: center;
                                    line-height: 1.3;
                                }

                                .slip {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-top: 20px;
                                }

                                .slip td {
                                    padding: 1px 4px;
                                    vertical-align: top;
                                    white-space: nowrap;
                                }

                                .sign {
                                    width: 100%;
                                    margin-top: 70px;
                                }

                                .sign td {
                                    width: 35%;
                                    border-top: 1px dashed #000;
                                    padding-top: 4px;
                                }

                                .watermark {
                                    position: fixed;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%) rotate(-30deg);
                                    font-size: 110px;
                                    font-weight: bold;
                                    letter-spacing: 10px;
                                    color: rgba(0, 0, 0, 0.12);
                                    z-index: -1;
                                    pointer-events: none;
                                    white-space: nowrap;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                            </style>
                        </head>
                        <body>
                            '.$duplicateWatermark.'
                            <div class="header">
                                <span style="font-size: 18px; font-weight: bold;">'.$compname.'</span><br>
                                <span>'.$compaddress.' '.$compaddress2.'</span><br>
                                <span>'.$compaddress3.' Tel : '.$compphone.'</span>
                            </div>

                            <table class="slip">
                                <tr>
                                    <td width="14%">Ticket No.</td><td width="22%">: '.$row['transaction_id'].'</td>
                                    <td width="14%">Date</td><td width="18%">: '.$transactionDate.'</td>
                                    <td width="20%"></td><td width="12%"></td>
                                </tr>
                                <tr>
                                    <td>Vehicle No.</td><td>: '.$row['lorry_plate_no1'].'</td>
                                    <td>Container No.</td><td colspan="3">: '.$row['container_no'].'</td>
                                </tr>
                                <tr>
                                    <td>Customer</td><td>: '.$cid.'</td>
                                    <td colspan="4">'.$custName.'</td>
                                </tr>
                                <tr>
                                    <td>Contractor</td><td>: '.$row['transporter_code'].'</td>
                                    <td colspan="4">'.$row['transporter'].'</td>
                                </tr>
                                <tr>
                                    <td>Area</td><td>: '.$row['destination_code'].'</td>
                                    <td colspan="4">'.$row['destination'].'</td>
                                </tr>
                                <tr>
                                    <td>Bin No.</td><td>: </td>
                                    <td>Bin Type</td><td colspan="3">: </td>
                                </tr>
                                <tr>
                                    <td>Waste</td><td>: '.$pid.'</td>
                                    <td colspan="4">'.$wasteName.'</td>
                                </tr>
                                <tr>
                                    <td>Time In</td><td>: '.$timeIn.'</td>
                                    <td>Time Out</td><td>: '.$timeOut.'</td>
                                    <td>First Weight (MT)</td><td>: '.$firstWeight.'</td>
                                </tr>
                                <tr>
                                    <td>Price/Ton</td><td>: '.$pricePerTon.'</td>
                                    <td></td><td></td>
                                    <td>Second Weight(MT)</td><td>: '.$secondWeight.'</td>
                                </tr>
                                <tr>
                                    <td>Amount (RM)</td><td>: '.$amount.'</td>
                                    <td></td><td></td>
                                    <td>Nett Weight (MT)</td><td>: '.$nettWeight.'</td>
                                </tr>
                            </table>

                            <table class="sign">
                                <tr>
                                    <td>ADMIN</td>
                                    <td style="width: 30%; border-top: none;"></td>
                                    <td>DRIVER\'S IC AND SIGNATURE</td>
                                </tr>
                            </table>
                        </body>
                    </html>';

                    if (!$isDuplicate) {
                        if ($print_stmt = $db->prepare("UPDATE ".$printTable." SET is_printed = 'Y' WHERE id = ? AND is_printed = 'N'")) {
                            $print_stmt->bind_param('s', $id);
                            $print_stmt->execute();
                            $print_stmt->close();
                        }
                    }

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