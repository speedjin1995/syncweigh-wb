<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';

if(isset($_POST['userID'])){
    $userID = $_POST['userID'];
    
    // Get company details
    $compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
    $compreg = '123456789-X';
    $compaddress = 'No.34, Jalan Bagan 1, Taman Bagan, 13400 Butterworth. Penang.';
    $compaddress1 = '';
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
    
    // Get payslip details
    if ($stmt = $db->prepare('SELECT * FROM Payslip WHERE id=?')) {
        $stmt->bind_param('s', $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $date = date('d/m/Y', strtotime($row['date']));
            $month = date('F Y', strtotime($row['date']));
            $staff = searchUserById($row['user_id'], $db);
            $earnings = !empty($row['earnings_detail']) ? json_decode($row['earnings_detail'], true) : [];
            $deductions = !empty($row['deductions_detail']) ? json_decode($row['deductions_detail'], true) : [];
            
            // Type mapping
            $typeMap = [
                'BASIC' => 'BASIC SALARY',
                'ALLOWANCE' => 'ALLOWANCE',
                'OVERTIME' => 'OVERTIME',
                'BONUS' => 'BONUS',
                'COMMISSION' => 'COMMISSION',
                'CLAIMS' => 'CLAIMS',
                'EMP_EPF' => 'EMPLOYEE EPF',
                'EMP_SOCSO' => 'EMPLOYEE SOCSO',
                'TAX' => 'TAX',
                'EMP_EIS' => 'EMPLOYEE EIS'
            ];
            
            // Build earnings HTML
            $earningsHtml = '';
            foreach($earnings as $earning) {
                $label = isset($typeMap[$earning['type']]) ? $typeMap[$earning['type']] : strtoupper($earning['type']);
                $earningsHtml .= $label.'<span style="float: right;">'.number_format($earning['amt'], 2).'</span><br>';
            }
            
            // Build deductions HTML
            $deductionsHtml = '';
            foreach($deductions as $deduction) {
                $label = isset($typeMap[$deduction['type']]) ? $typeMap[$deduction['type']] : strtoupper($deduction['type']);
                $deductionsHtml .= $label.'<span style="float: right;">'.number_format($deduction['amt'], 2).'</span><br>';
            }

            $message = '
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Payment Slip</title>
                    <style>
                        @page {
                            size: landscape;
                        }
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            background-color: #f5f5f5;
                        }
                        .payment-slip {
                            background-color: white;
                            max-width: 1000px;
                            margin: 0 auto;
                            padding: 30px;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                        }
                        .company-name {
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        .company-details {
                            font-size: 14px;
                            margin-bottom: 20px;
                        }
                        .slip-title {
                            font-size: 16px;
                            font-weight: bold;
                            margin-bottom: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                            font-size: 12px;
                        }
                        .info-table td {
                            padding: 3px 5px;
                            font-size: 14px;
                        }
                        .info-label {
                            font-weight: bold;
                            width: 120px;
                        }
                        .earnings-table {
                            border: 1px solid #000;
                        }
                        .earnings-table th {
                            background-color: #f0f0f0;
                            padding: 10px;
                            font-weight: bold;
                            text-align: center;
                            border: 1px solid #000;
                            font-size: 14px;
                        }
                        .earnings-table td {
                            padding: 10px;
                            border: 1px solid #000;
                            vertical-align: top;
                            font-size: 14px;
                        }
                        .earnings-table td:first-child {
                            width: 50%;
                        }
                        .totals-table td {
                            padding: 5px 10px;
                            font-weight: bold;
                        }
                        .totals-table {
                            border-top: 1px solid #000;
                            padding-top: 10px;
                        }
                        .annual-leave {
                            margin-top: 20px;
                            font-size: 12px;
                        }
                        .signatures {
                            display: flex;
                            justify-content: space-between;
                            margin-top: 50px;
                            padding-top: 20px;
                        }
                        .signature-box {
                            text-align: center;
                            width: 30%;
                        }
                        .signature-line {
                            border-bottom: 1px solid #000;
                            margin-bottom: 10px;
                            height: 40px;
                        }
                    </style>
                </head>
                <body>
                    <div class="payment-slip">
                        <div class="header">
                            <div class="company-name">'.$compname.' ('.$compreg.')</div>
                            <div class="company-details">
                                '.$compaddress1.'<br>
                                '.$compaddress2.'<br>
                                '.$compaddress3.'<br>
                                TEL:'.$compphone.'
                            </div>
                            <div class="slip-title">PAYMENT - SLIP (MONTH END)</div>
                        </div>

                        <table class="info-table">
                            <tr>
                                <td class="info-label">STAFF NO.</td>
                                <td>: '.$staff['employee_code'].'</td>
                                <td class="info-label">VOUCHER NO.</td>
                                <td>: '.$row['payslip_no'].'</td>
                            </tr>
                            <tr>
                                <td class="info-label">NAME</td>
                                <td>: '.$staff['name'].'</td>
                                <td class="info-label">PAYMENT TYPE</td>
                                <td>: '.$row['payment_type'].'</td>
                            </tr>
                            <tr>
                                <td class="info-label">I/C NO.</td>
                                <td>: '.$staff['nric'].'</td>
                                <td class="info-label">CHEQUE NO.</td>
                                <td>: '.$row['cheque_no'].'</td>
                            </tr>
                            <tr>
                                <td class="info-label">POSITION</td>
                                <td>: '.$staff['position'].'</td>
                                <td class="info-label">DATE</td>
                                <td>: '.$date.'</td>
                            </tr>
                            <tr>
                                <td class="info-label">DEPARTMENT</td>
                                <td>: '.$staff['department'].'</td>
                                <td class="info-label">MONTH</td>
                                <td>: '.$month.'</td>
                            </tr>
                        </table>

                        <table class="earnings-table">
                            <tr>
                                <th>EARNINGS</th>
                                <th>DEDUCTIONS</th>
                            </tr>
                            <tr>
                                <td style="height: 150px;">
                                    '.$earningsHtml.'
                                </td>
                                <td style="height: 150px;">
                                    '.$deductionsHtml.'
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    GROSS PAY : <span style="float: right;">RM '.number_format($row['gross_pay'], 2).'</span>
                                </td>
                                <td>
                                    TOTAL DEDUCTIONS : <span style="float: right;">RM '.number_format($row['total_deductions'], 2).'</span>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>NET PAY : <span style="float: right;">RM '.number_format($row['net_pay'], 2).'</span></td>
                            </tr>
                        </table>

                        <div class="signatures">
                            <div class="signature-box">
                                <div class="signature-line"></div>
                                <div>Prepared By</div>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line"></div>
                                <div>Approved By</div>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line"></div>
                                <div>Received By</div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>           
            ';
            
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
