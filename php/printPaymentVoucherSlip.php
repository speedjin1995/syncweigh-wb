<?php
session_start();
require_once 'db_connect.php';

if(isset($_POST['slipType'], $_POST['transactionStatus'], $_POST['weightType'], $_POST['customerSupplier'], $_POST['transactionDate'])){
    $slipType = $_POST['slipType'];
    $transactionStatus = $_POST['transactionStatus'];
    $weightType = $_POST['weightType'];
    $customerSupplier = $_POST['customerSupplier'];
    $transactionDate = DateTime::createFromFormat('d-m-Y', $_POST['transactionDate'])->format('Y-m-d');
    
    // Check if payment voucher exists
    $check_sql = "SELECT id FROM Payment_Voucher WHERE customer_supplier=? AND DATE(voucher_date)=? AND deleted=0";
    if ($check_stmt = $db->prepare($check_sql)) {
        $check_stmt->bind_param('ss', $customerSupplier, $transactionDate);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode(array(
                "status" => "failed",
                "message" => "No payment voucher found for this date and supplier"
            ));
            exit;
        }
        $check_stmt->close();
    }
    
    // Get company details
    $compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
    $compreg = '123456789-X';
    $compaddress = 'No.34, Jalan Bagan 1, Taman Bagan, 13400 Butterworth. Penang.';
    $compaddress1 = 'No.34, Jalan Bagan 1, Taman Bagan, 13400 Butterworth. Penang.';
    $compaddress2 = '';
    $compaddress3 = '';
    $compphone = '6043325822';
    
    $stmt = $db->prepare("SELECT * FROM Company WHERE id = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $compname = $row['name'];
        $compreg = $row['company_reg_no'];
        $compaddress = $row['address_line_1'] . ', ' . $row['address_line_2'] . ', ' . $row['address_line_3'];
        $compaddress1 = $row['address_line_1'];
        $compaddress2 = $row['address_line_2'];
        $compaddress3 = $row['address_line_3'];
        $compphone = $row['phone_no'];
    }
    
    // Get payment voucher details
    $sql = "SELECT * FROM Payment_Voucher WHERE customer_supplier=? AND DATE(voucher_date)=? AND deleted=0";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ss', $customerSupplier, $transactionDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $voucherDate = date('d/m/Y', strtotime($row['voucher_date']));
            $pvLogoPath = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "pv_logo.png";
            $pvLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pvLogoPath));

            // Format date to Malay month and year
            $malayMonths = array(
                1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MAC', 4 => 'APRIL',
                5 => 'MEI', 6 => 'JUN', 7 => 'JULAI', 8 => 'OGOS',
                9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DISEMBER'
            );
            $month = date('n', strtotime($row['voucher_date']));
            $year = date('Y', strtotime($row['voucher_date']));
            $formatVoucherDate = $malayMonths[$month] . ' ' . $year;
            
            $supplierName = $row['customer_supplier'];
            $voucherDate = date('d/m/Y', strtotime($row['voucher_date']));
            $invoiceNo = $row['invoice_no'] ?? '';
            $accountNo = $row['account_no'] ?? '';
            $deductions = json_decode($row['deduction_details'], true);
            $additions = json_decode($row['addition_details'], true);
            $unitPrice = floatval($row['unit_price']);
            $totalNettWeight = floatval($row['total_nett_weight']);
            $totalAmount = floatval(str_replace('RM ', '', $row['total_amount']));
            $totalDeductions = floatval($row['deduction_amount']);
            $totalAdditions = floatval($row['addition_amount']);
            $finalAmount = floatval($row['final_amount']);
            
            $message = '
            <html>
            <style>
                @media print {
                    @page {
                        size: A5 landscape;
                        margin: 1in 1in;
                    }
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 20px;
                    padding: 0;
                }
                .header {
                    position: relative;
                    margin-bottom: 25px;
                    text-align: center;
                }
                .header-logo {
                    position: absolute;
                    left: 50px;
                    top: 10px;
                    width: 80px;
                    height: auto;
                }
                .header-text {
                    text-align: center;
                }
                .header h3 {
                    margin: 0;
                    padding: 3px 0;
                    font-size: 13px;
                    font-weight: bold;
                }
                .header p {
                    margin: 0;
                    padding: 3px 0;
                    font-size: 12px;
                }
                .title {
                    text-align: center;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 10px 0;
                    text-decoration: underline;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    gap: 20px;
                }
                .info-item {
                    display: flex;
                    align-items: baseline;
                    flex: 1;
                }
                .info-label {
                    font-weight: bold;
                    width: 100px;
                    display: inline-block;
                }
                .info-value {
                    border-bottom: 1px solid #000;
                    flex: 1;
                    display: inline-block;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 5px;
                    text-align: center;
                    font-size: 10px;
                }
                th {
                    font-weight: bold;
                    background-color: #f0f0f0;
                }
                .text-right {
                    text-align: right;
                }
                .text-left {
                    text-align: left;
                }
                .total-row {
                    font-weight: bold;
                }
                .footer {
                    margin-top: 15px;
                    font-size: 10px;
                }
                .signature {
                    margin-top: 50px;
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end;
                }
                .signature p {
                    text-align: left;
                    width: 200px;
                    margin: 0;
                }
                .signature-line {
                    border-top: 1px solid #000;
                    width: 200px;
                    margin-top: 50px;
                }
            </style>
            <body>
                <div class="header">
                    <img src="'.$pvLogoBase64.'" alt="Logo" class="header-logo">
                    
                    <div class="header-text">
                        <h3>'.$compname.' (No. Daftar: '.$compreg.')</h3>
                        <p>'.$compaddress1.' '.$compaddress2.'</p>
                        <p>'.$compaddress3.' TEL: '.$compphone.'</p>
                    </div>
                </div>
                
                <div class="title">BAUCER BAYARAN</div>
                
                <div class="info-row">
                    <div class="info-item">
                        <span class="info-label">NAMA :</span>
                        <span class="info-value">'.$supplierName.'</span>
                    </div>
                    
                    <div class="title">BAUCER BAYARAN</div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">NAMA :</span>
                            <span class="info-value">'.$supplierName.'</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">TARIKH :</span>
                            <span class="info-value">'.$voucherDate.'</span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">NO AKAUN :</span>
                            <span class="info-value">'.$invoiceNo.'</span>
                        </div>
                        <div class="info-item" style="visibility: hidden;">
                            <span class="info-label">TARIKH :</span>
                            <span class="info-value">'.$voucherDate.'</span>
                        </div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>BIL</th>
                                <th>PERKARA</th>
                                <th>UNIT (M/T)</th>
                                <th>HARGA (RM)</th>
                                <th>JUMLAH (RM)</th>
                            </tr>
                        </thead>
                        <tbody>';

                $bil = 1;
                $message .= '
                    <tr>
                        <td class="text-center">'.$bil.'</td>
                        <td class="text-left">BTS '.$formatVoucherDate.'</td>
                        <td>'.number_format($totalNettWeight, 2).'</td>
                        <td class="text-center">RM'.number_format($unitPrice, 2).'</td>
                        <td class="text-center">RM'.number_format($totalAmount, 2).'</td>
                    </tr>
                ';
                $bil++;
            
                foreach ($additions as $addition) {
                    $message .= '
                            <tr>
                                <td class="text-center">'.$bil.'</td>
                                <td class="text-left">'.$addition['addition_desc'].'</td>
                                <td></td>
                                <td></td>
                                <td class="text-center">RM'.number_format($addition['addition_amount'], 2).'</td>
                            </tr>';
                    $bil++;
                }

                foreach ($deductions as $deduction) {
                    $message .= '
                            <tr>
                                <td class="text-center">'.$bil.'</td>
                                <td class="text-left">'.$deduction['deduction_desc'].'</td>
                                <td></td>
                                <td></td>
                                <td class="text-center">-RM'.number_format($deduction['deduction_amount'], 2).'</td>
                            </tr>';
                    $bil++;
                }
                
                $message .= '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-left" style="border-right:none"><strong>NO AKAUN BANK : '.$accountNo.'</strong></td>
                                <td class="text-right" style="border-left:none"><strong>JUMLAH (RM)</strong></td>
                                <td class="text-center"><strong>RM'.number_format($finalAmount, 2).'</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="signature">
                        <p>DITERIMA OLEH :</p>
                        <div class="signature-line"></div>
                    </div>
                </body>
                </html>';
            } elseif ($slipType == 'ffbStatement') {
                $voucherDateMonthYear = date('F Y', strtotime($row['voucher_date']));
                $voucherDateFormat = date('d M Y', strtotime($row['voucher_date']));

                ###### Supplier details ######
                $supplierName = $row['customer_supplier'];
                $supplierMpob = '';
                $supplierPhone = '';
                $supplier_stmt = $db->prepare("SELECT * FROM Supplier WHERE name = ? AND status = 0");
                $supplier_stmt->bind_param("s", $supplierName);
                $supplier_stmt->execute();
                $supplier_result = $supplier_stmt->get_result();
                if ($supplier_row = $supplier_result->fetch_assoc()) {
                    $supplierName = $supplier_row['name'];
                    $supplierMpob = $supplier_row['mpob'];
                    $supplierPhone = $supplier_row['phone_no'];
                }

                ###### Weighing details ######
                $weighingData = array();
                $totalNettWeight = 0;
                $totalAmount = 0;
                if ($weight_stmt = $db->prepare("SELECT * FROM Weight WHERE transaction_status = ? AND weight_type = ? AND supplier_name = ? AND DATE(transaction_date) = ? AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'")) {
                    $weight_stmt->bind_param('ssss', $transactionStatus, $weightType, $customerSupplier, $transactionDate);
                    $weight_stmt->execute();
                    $weight_result = $weight_stmt->get_result();
            
                    while ($weight_row = $weight_result->fetch_assoc()) {
                        $weighingData[] = array(
                            "id" => $weight_row['id'],
                            "transaction_id" => $weight_row['transaction_id'],
                            "lorry_plate_no1" => $weight_row['lorry_plate_no1'],
                            "nett_weight1" => $weight_row['nett_weight1'],
                            "unit_price" => $weight_row['unit_price'],
                            "sub_total" => $weight_row['sub_total'],
                            "sst" => $weight_row['sst'],
                            "total_price" => $weight_row['total_price'],
                            "invoice_no" => $weight_row['invoice_no'],
                        );
                        $totalNettWeight += floatval($weight_row['nett_weight1']);
                        $totalAmount += floatval($weight_row['total_price']);
                    }
                }

                $message = '
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
                        <style>
                            @page {
                                size: A4;
                                margin-top: 120px;
                                margin-bottom: 250px;
                                margin-left: 15mm;
                                margin-right: 15mm;
                                
                                @top-center {
                                    content: element(page-header);
                                }
                                
                                @bottom-center {
                                    content: element(page-footer);
                                }
                            }
                            body {
                                font-family: "Times New Roman", serif;
                                font-size: 14px;
                                margin: 0;
                                padding: 0;
                            }
                            .page-header {
                                position: running(page-header);
                                text-align: center;
                                font-size: 13px;
                                line-height: 1.3;
                                padding-top: 150px;
                            }
                            .page-header h2 {
                                margin: 0 0 5px 0;
                                font-size: 18px;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                                font-size: 13px;
                                line-height: 1.3;
                            }
                            .header h2 {
                                margin: 0 0 5px 0;
                                font-size: 18px;
                            }
                            .title {
                                text-align: center;
                                font-weight: bold;
                                font-size: 18px;
                                margin: 20px 0;
                            }
                            .title span {
                                font-weight: normal;
                                font-size: 14px;
                            }
                            .title-underline {
                                border-bottom: 1px solid #000;
                                margin: 10px 0 20px 0;
                            }
                            .info-section {
                                margin: 15px 0;
                            }
                            .info-row {
                                display: flex;
                                margin-bottom: 5px;
                            }
                            .info-row > span:first-child {
                                width: 60%;
                            }
                            .info-row > span:nth-child(2) {
                                width: 20%;
                            }
                            .info-row > span:nth-child(3) {
                                width: 20%;
                            }
                            .customer-name {
                                font-weight: bold;
                                font-size: 16px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin: 15px 0;
                            }
                            td {
                                padding: 4px 8px;
                                font-size: 13px;
                            }
                            .table-border {
                                border-top: 1px solid #000;
                                border-bottom: 1px solid #000;
                            }
                            .border-top {
                                border-top: 1px solid #000;
                            }
                            .border-bottom {
                                border-bottom: 1px solid #000;
                            }
                            .text-right {
                                text-align: right;
                            }
                            .text-center {
                                text-align: center;
                            }
                            .summary-section {
                                margin-top: 20px;
                            }
                            .content-wrapper {
                                margin-top: 110px;
                            }
                            .page-footer {
                                position: running(page-footer);
                                width: 100%;
                            }
                            .page-footer table {
                                margin: 10px 0;
                            }
                            .footer-signatures {
                                display: flex;
                                justify-content: space-between;
                            }
                            .footer-signatures > div {
                                width: 45%;
                                text-align: center;
                            }
                            .payment-section {
                                margin-top: 40px;
                            }
                            
                            .footer-left {
                                position: running(footer-left);
                                width: 200px;
                            }
                            
                            .footer-right {
                                position: running(footer-right);
                                width: 200px;
                            }
                            
                            .signature-line {
                                border-top: 1px solid #000;
                                width: 200px;
                                margin: 50px auto 0 auto;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="page-header">
                            <h2>'.$compname.'</h2>
                            ('.$compreg.')<br>
                            '.$compaddress1.'<br>
                            '.$compaddress2.'<br>
                            '.$compaddress3.'<br>
                            Tel: '.$compphone.'
                            <div style="margin-top: 15px; font-weight: bold; font-size: 18px;">
                                FFB STATEMENT<br>
                                <span style="font-weight: normal; font-size: 14px;">For The Month Of '.$voucherDateMonthYear.'</span>
                            </div>
                            <div style="border-bottom: 1px solid #000; margin: 10px 0;"></div>
                        </div>

                        <div class="page-footer">
                            <table>
                                <tr>
                                    <td>Cheque No:</td>
                                    <td class="border-bottom" style="width: 200px;"></td>
                                    <td></td>
                                    <td>Add Total Flat Rate Amount:</td>
                                    <td class="text-right border-bottom" style="width: 150px;">0.00</td>
                                </tr>
                                <tr>
                                    <td>Payment Date:</td>
                                    <td class="border-bottom" style="width: 200px;"></td>
                                    <td></td>
                                    <td>Net Amount (RM):</td>
                                    <td class="text-right border-bottom" style="width: 150px;">'.$outstandingAmount.'</td>
                                </tr>
                            </table>
                            <div class="footer-signatures border-top">
                                <div>
                                    <p style="padding-bottom: 30px;">'.$compname.'</p>
                                    <div class="signature-line"></div>
                                    <p style="text-align: center; margin-top: 5px;">Authorised Signature</p>
                                </div>
                                <div>
                                    <p style="padding-bottom: 30px;">Kindly Acknowledge Receipt</p>
                                    <div class="signature-line"></div>
                                    <p style="text-align: center; margin-top: 5px;">Received By</p>
                                </div>
                            </div>
                        </div>

                        <div class="content-wrapper">
                            <div class="info-section">
                                <div class="info-row">
                                    <span class="customer-name">'.$supplierName.'</span>
                                    <span class="label">Invoice No:</span>
                                    <span class="value">'.$invoiceNo.'</span>
                                </div>
                                <div class="info-row">
                                    <span></span>
                                    <span class="label">Date:</span>
                                    <span class="value">'.$voucherDateFormat.'</span>
                                </div>
                                <div class="info-row">
                                    <span></span>
                                    <span class="label">MPOB Licence No:</span>
                                    <span class="value">'.$supplierMpob.'</span>
                                </div>
                            </div>

                            <div class="info-section">
                                <p>(Flat Rate No: )</p>
                                <p>TEL: '.$supplierPhone.'</p>
                            </div>

                            <table>
                                <tr class="table-border">
                                    <td class="text-left">Date</td>
                                    <td class="text-center">Ticket No</td>
                                    <td class="text-center">Vehicle No</td>
                                    <td class="text-center">POER %</td>
                                    <td class="text-center">Net Weight<br>(MT)</td>
                                    <td class="text-center">Price (RM)<br>(MT)</td>
                                    <td class="text-center">Total<br>Amount (RM)</td>
                                </tr>';

                                if (!empty($weighingData)) {
                                    foreach ($weighingData as $weight) {
                                        $message .= '
                                            <tr>
                                                <td>'.$voucherDateFormat.'</td>
                                                <td>'.$weight['transaction_id'].'</td>
                                                <td>'.$weight['lorry_plate_no1'].'</td>
                                                <td class="text-center">0.00</td>
                                                <td class="text-center">'.number_format($weight['nett_weight1']/1000, 2).'</td>
                                                <td class="text-center">'.number_format($weight['unit_price'], 2).'</td>
                                                <td class="text-right">'.number_format($weight['total_price'], 2).'</td>
                                            </tr>
                                        ';
                                    }
                                }

                            $message .= '
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>Total</td>
                                    <td></td>
                                    <td class="text-center border-top">'.number_format($totalNettWeight/1000, 2).'</td>
                                    <td class="border-top"></td>
                                    <td class="text-right border-top">'.number_format($totalAmount, 2).'</td>
                                </tr>
                            </table>

                            <table class="summary-section">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-left" width="15%">Date</td>
                                        <td class="text-left" width="25%">Details</td>
                                        <td class="text-left" width="15%">Reference</td>
                                        <td class="text-center" width="15%">Sub Total <br> Excluding GST</td>
                                        <td class="text-center" width="15%">GST Amount <br> (6%)</td>
                                        <td class="text-center" width="15%">Total <br> Amount (RM)</td>
                                    </tr>
                                    <tr>
                                        <td><b><u>Low Cash</u></b></td>
                                    </tr>';

                                    $paymentTotal = 0;
                                    if (!empty($outstandingDetails)) {
                                        foreach ($outstandingDetails as $outstanding) {
                                            $paymentTotal += floatval($outstanding['amount']);
                                            $message .= '
                                                <tr>
                                                    <td>'.($outstanding['cashbook_date'] !='' ? date('d M Y', strtotime($outstanding['cashbook_date'])) : '').'</td>
                                                    <td>'.$outstanding['desc'].'</td>
                                                    <td>'.$outstanding['cashbook_no'].'</td>
                                                    <td class="text-center">'.number_format($outstanding['amount'], 2).'</td>
                                                    <td class="text-center">0.00</td>
                                                    <td class="text-center">'.number_format($outstanding['amount'], 2).'</td>
                                                </tr>
                                            ';
                                        }
                                    }

                        $message .= '
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-center"><b>SubTotal :</b></td>
                                        <td class="text-right"><b>'.number_format($paymentTotal, 2).'</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">Total(Add/Loss) (RM) :</td>
                                        <td class="text-right">'.number_format(-$paymentTotal, 2).'</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </body>
                    </html>
                ';
            }
            
            echo json_encode(array(
                "status" => "success",
                "message" => $message
            ));
        } else {
            echo json_encode(array(
                "status" => "failed",
                "message" => "Payment voucher not found"
            ));
        }
    }
} else {
    echo json_encode(array(
        "status" => "failed",
        "message" => "Missing parameters"
    ));
}
?>
