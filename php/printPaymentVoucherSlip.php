<?php
session_start();
require_once 'db_connect.php';

if(isset($_POST['customerSupplier'], $_POST['invoiceNo'])) {
    $customerSupplier = $_POST['customerSupplier'];
    $invoiceNo = mysqli_real_escape_string($db, $_POST['invoiceNo']);
    
    // Get company details
    $compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
    $compreg = '123456789-X';
    $compaddress = 'No.34, Jalan Bagan 1, Taman Bagan, 13400 Butterworth. Penang.';
    $compphone = '6043325822';
    
    $stmt = $db->prepare("SELECT * FROM Company WHERE id = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $compname = $row['name'];
        $compreg = $row['company_reg_no'];
        $compaddress = $row['address_line_1'] . ', ' . $row['address_line_2'] . ', ' . $row['address_line_3'];
        $compphone = $row['phone_no'];
    }
    
    // Get payment voucher details
    $sql = "SELECT * FROM Payment_Voucher WHERE customer_supplier=? AND invoice_no=? AND deleted=0";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ss', $customerSupplier, $invoiceNo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $voucherDate = date('d/m/Y', strtotime($row['voucher_date']));
            
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
            <head>
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
                        text-align: center;
                        margin-bottom: 15px;
                    }
                    .header h3 {
                        margin: 0;
                        font-size: 13px;
                        font-weight: bold;
                    }
                    .header p {
                        margin: 2px 0;
                        font-size: 10px;
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
            </head>
            <body>
                <div class="header">
                    <h3>'.$compname.' (No. Daftar: '.$compreg.')</h3>
                    <p>'.$compaddress.'</p>
                    <p>TEL: '.$compphone.'</p>
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
                        <span class="info-value">'.$accountNo.'</span>
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
