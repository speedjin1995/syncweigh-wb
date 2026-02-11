<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
require_once 'requires/function.php';
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

if(isset($_POST['userID'])) {
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($select_stmt = $db->prepare("SELECT * FROM Cash_Book WHERE id=?")) {
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
                $companyName = searchCompanyNameById($compids, $db);
                $deductions = json_decode($row['deduction_details'], true);
                $additions = json_decode($row['addition_details'], true);
                $accumDeductions = json_decode($row['accum_deduction'], true);
                $accumAdditions = json_decode($row['accum_addition'], true);

                ######### Calculate Daily Additions #########
                $dailyCashFromHq = 0.00;
                $dailyCash = 0.00;
                $dailyCashRamp = 0.00;
                $dailyCashFfb = 0.00;
                $dailyDebitNote = 0.00;
                $dailyCashIn = 0.00;
                $cashIns = [];
                foreach ($additions as $addition) {
                    $type = $addition['type'];
                    $amt = floatval($addition['amount']);

                    if ($type == 'CASHHQ'){
                        $dailyCashFromHq += $amt;
                    }elseif ($type == 'CASHRAM'){
                        $dailyCashRamp += $amt;
                    }elseif ($type == 'CASHFFBCUST'){
                        $dailyCashFfb += $amt;
                    }elseif ($type == 'DEBITNOTE'){
                        $dailyDebitNote += $amt;
                    }elseif ($type == 'CASHIN'){
                        $dailyCashIn += $amt;
                        $cashIns[] = [
                            'description' => $addition['desc'],
                            'amount' => $amt
                        ];
                    }
                }

                $totalDailyAdditions = $dailyCashFromHq + $dailyCash + $dailyCashRamp + $dailyCashFfb + $dailyDebitNote + $dailyCashIn;
                ############################################

                ######### Calculate Accum Additions #########
                $accumCashFromHq = 0.00;
                $accumCashRamp = 0.00;
                $accumCashFfb = 0.00;
                $accumDebitNote = 0.00;
                $accumCashIn = 0.00;
                foreach ($accumAdditions as $accumAddition) {
                    $type = $accumAddition['type'];
                    $amt = floatval($accumAddition['amount']);

                    if ($type == 'CASHHQ'){
                        $accumCashFromHq += $amt;
                    }elseif ($type == 'CASHRAM'){
                        $accumCashRamp += $amt;
                    }elseif ($type == 'CASHFFBCUST'){
                        $accumCashFfb += $amt;
                    }elseif ($type == 'DEBITNOTE'){
                        $accumDebitNote += $amt;
                    }elseif ($type == 'CASHIN'){
                        $accumCashIn += $amt;
                    }
                }
                ############################################

                ######### Calculate Daily Deductions #########
                $dailyCashOut = 0.00;
                $dailyCreditNote = 0.00;
                $dailyCreditAdvRamp = 0.00;
                $dailyCreditAdvHq = 0.00;
                $dailyCreditAdv = 0.00;
                $dailyGeneral = 0.00;
                $dailyPetrolDiesel = 0.00;
                $dailyStaffAdv = 0.00;
                $dailyStaffSal = 0.00;
                $cashOuts = [];
                $generalExpenses = [];
                $petrolDiesels = [];
                foreach ($deductions as $deduction) {
                    $type = $deduction['type'];
                    $amt = floatval($deduction['amount']);

                    if ($type == 'CASHOUT'){
                        $dailyCashOut += $amt;
                        $cashOuts[] = [
                            'description' => $deduction['desc'],
                            'amount' => $amt
                        ];
                    }elseif ($type == 'CREDITNOTE'){
                        $dailyCreditNote += $amt;
                    }elseif ($type == 'CREDITFFBRAM'){
                        $dailyCreditAdvRamp += $amt;
                    }elseif ($type == 'CREDITFFBHQ'){
                        $dailyCreditAdvHq += $amt;
                    }elseif ($type == 'CREDITFFBADV'){
                        $dailyCreditAdv += $amt;
                    }elseif ($type == 'GENERAL'){
                        $dailyGeneral += $amt;
                        $generalExpenses[] = [
                            'description' => $deduction['desc'],
                            'amount' => $amt
                        ];
                    }elseif ($type == 'PETROL_DIESEL'){
                        $dailyPetrolDiesel += $amt;
                        $petrolDiesels[] = [
                            'description' => $deduction['desc'],
                            'amount' => $amt
                        ];
                    }elseif ($type == 'STAFFADV'){
                        $dailyStaffAdv += $amt;
                    }elseif ($type == 'STAFFSAL'){
                        $dailyStaffSal += $amt;
                    }
                }
                $cashBalance = $totalDailyAdditions - ($dailyCashOut + $dailyCreditNote + $dailyCreditAdvRamp + $dailyCreditAdvHq + $dailyCreditAdv + $dailyGeneral + $dailyPetrolDiesel + $dailyStaffAdv + $dailyStaffSal);
                ##############################################

                ######### Calculate Accum Deductions #########
                $accumCashOut = 0.00;
                $accumCreditNote = 0.00;
                $accumCreditAdvRamp = 0.00;
                $accumCreditAdvHq = 0.00;
                $accumCreditAdv = 0.00;
                $accumGeneral = 0.00;
                $accumPetrolDiesel = 0.00;
                $accumStaffAdv = 0.00;
                $accumStaffSal = 0.00;

                foreach ($accumDeductions as $deduction) {
                    $type = $deduction['type'];
                    $amt = floatval($deduction['amount']);

                    if ($type == 'CASHOUT'){
                        $accumCashOut += $amt;
                    }elseif ($type == 'CREDITNOTE'){
                        $accumCreditNote += $amt;
                    }elseif ($type == 'CREDITFFBRAM'){
                        $accumCreditAdvRamp += $amt;
                    }elseif ($type == 'CREDITFFBHQ'){
                        $accumCreditAdvHq += $amt;
                    }elseif ($type == 'CREDITFFBADV'){
                        $accumCreditAdv += $amt;
                    }elseif ($type == 'GENERAL'){
                        $accumGeneral += $amt;
                    }elseif ($type == 'PETROL_DIESEL'){
                        $accumPetrolDiesel += $amt;
                    }elseif ($type == 'STAFFADV'){
                        $accumStaffAdv += $amt;
                    }elseif ($type == 'STAFFSAL'){
                        $accumStaffSal += $amt;
                    }
                }
                ##############################################

                ############## Weighing Records ##############
                $weights = [];
                $date = date('Y-m-d', strtotime($row['date']));
                if ($weigh_stmt = $db->prepare("SELECT * FROM Weight WHERE DATE(transaction_date)=? AND is_complete='Y' AND is_cancel <> 'Y' AND status=0 ORDER BY id ASC")){
                    $date = date('Y-m-d', strtotime($row['date']));
                    $weigh_stmt->bind_param('s', $date);

                    // Execute the prepared query.
                    if (! $weigh_stmt->execute()) {
                        echo json_encode(
                            array(
                                "status" => "failed",
                                "message" => "Something went wrong"
                            )); 
                    }
                    else{
                        $result2 = $weigh_stmt->get_result();
                    
                        while ($row2 = $result2->fetch_assoc()) {
                            $weights[] = $row2;
                        }

                        $weights = groupByTransactionStatus($weights);
                    }
                }
                
                ##############################################

                $message = '
                    <html>
                    <head>
                        <title>Daily Report</title>
                        <style>
                            @page {
                                size: A4 portrait;
                                margin: 0.5in 0.5in 1in 0.5in;
                            }
                            
                            @media print {
                                body {
                                    margin: 0.5in 0.5in 1in 0.5in;
                                }
                                
                                .detail-section {
                                    page-break-inside: avoid;
                                }
                            }
                            
                            body {
                                font-family: Arial, sans-serif;
                                font-size: 10pt;
                                margin: 0.5in 0.5in 1in 0.5in;
                                padding: 0;
                                padding-bottom: 1in;
                            }
                            
                            .header {
                                margin-bottom: 10px;
                            }
                            
                            .company-name {
                                font-size: 18pt;
                                font-weight: bold;
                                margin: 0;
                            }
                            
                            .report-title {
                                font-size: 12pt;
                                margin: 0;
                            }
                            
                            .date-section {
                                margin: 5px 0 15px 0;
                            }
                            
                            .barcode {
                                float: right;
                                margin-top: -60px;
                            }
                            
                            .main-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 15px;
                            }
                            
                            .main-table th {
                                color: #000;
                                padding: 5px;
                                text-align: left;
                                font-size: 9pt;
                                font-weight: bold;
                                border: 1px solid #000;
                            }
                            
                            .main-table td {
                                padding: 3px 5px;
                                border: 1px solid #000;
                                font-size: 9pt;
                            }
                            
                            .main-table .item-col {
                                width: 30%;
                            }
                            
                            .main-table .amount-col {
                                width: 15%;
                                text-align: right;
                            }
                            
                            .section-header {
                                color: #000;
                                font-weight: bold;
                            }
                            
                            .total-row {
                                font-weight: bold;
                                background-color: #f0f0f0;
                            }
                            
                            .detail-section {
                                margin-bottom: 15px;
                                page-break-inside: avoid;
                                break-inside: avoid;
                            }
                            
                            .main-table {
                                page-break-inside: auto;
                            }
                            
                            .detail-title {
                                font-weight: bold;
                                text-decoration: underline;
                                margin-bottom: 5px;
                            }
                            
                            .detail-item {
                                margin-left: 0;
                                font-size: 9pt;
                                line-height: 1.4;
                            }
                            
                            .amount-right {
                                float: right;
                            }
                            
                            .weight-value {
                                display: inline-block;
                                width: 60px;
                                text-align: right;
                            }
                            
                            .price-value {
                                display: inline-block;
                                width: 80px;
                                text-align: right;
                                margin-left: 30px;
                            }
                            
                            .detail-total {
                                border-top: 1px solid #000;
                                padding-top: 3px;
                                margin-top: 3px;
                            }
                            
                            @media print {
                                body {
                                    padding: 0;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1 class="company-name">'.$companyName.'</h1>
                            <p class="report-title">Daily Report</p>
                            <div class="date-section">Date: '.date('d M Y', strtotime($row['date'])).'</div>
                            <!--div class="barcode">
                                <svg width="150" height="60">
                                    <rect x="0" y="0" width="2" height="60" fill="#000"/>
                                    <rect x="4" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="7" y="0" width="3" height="60" fill="#000"/>
                                    <rect x="12" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="15" y="0" width="2" height="60" fill="#000"/>
                                    <rect x="19" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="22" y="0" width="3" height="60" fill="#000"/>
                                    <rect x="27" y="0" width="2" height="60" fill="#000"/>
                                    <rect x="31" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="34" y="0" width="2" height="60" fill="#000"/>
                                    <rect x="38" y="0" width="3" height="60" fill="#000"/>
                                    <rect x="43" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="46" y="0" width="2" height="60" fill="#000"/>
                                    <rect x="50" y="0" width="1" height="60" fill="#000"/>
                                    <rect x="53" y="0" width="3" height="60" fill="#000"/>
                                </svg>
                            </div-->
                        </div>
                        
                        <table class="main-table">
                            <thead>
                                <tr>
                                    <th width="28%" style="text-align: center">Item</th>
                                    <th width="11%" style="text-align: center">Daily</th>
                                    <th width="11%" style="text-align: center">FFB Accum..</th>
                                    <th width="28%" style="text-align: center">Item</th>
                                    <th width="11%" style="text-align: center">Daily</th>
                                    <th width="11%" style="text-align: center">Accum..</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Cash From HQ</td>
                                    <td style="text-align: right">'.number_format($dailyCashFromHq, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCashFromHq, 2).'</td>
                                    <td>FFB Purchase</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 3px solid black">Cash Variance</td>
                                    <td style="text-align: right; border-bottom: 3px solid black">0.00</td>
                                    <td style="text-align: right; border-bottom: 3px solid black">0.00</td>
                                    <td>C/F</td>
                                    <td style="text-align: right">0.00</td>
                                    <td style="text-align: right">0.00</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Cash</td>
                                    <td>3.04</td>
                                    <td>3.04</td>
                                </tr>
                                <tr>
                                    <td>Cash</td>
                                    <td style="text-align: right">'.number_format($dailyCash, 2).'</td>
                                    <td></td>
                                    <td>Credit</td>
                                    <td>34.41</td>
                                    <td>34.41</td>
                                </tr>
                                <tr>
                                    <td>Cash received by Ramp</td>
                                    <td style="text-align: right">'.number_format($dailyCashRamp, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCashRamp, 2).'</td>
                                    <td>Credit B/F</td>
                                    <td>5.26</td>
                                    <td>5.26</td>
                                </tr>
                                <tr>
                                    <td>Cash received by FFB customer</td>
                                    <td style="text-align: right">'.number_format($dailyCashFfb, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCashFfb, 2).'</td>
                                    <td>FM</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>Debit Note</td>
                                    <td style="text-align: right">'.number_format($dailyDebitNote, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumDebitNote, 2).'</td>
                                    <td></td>
                                    <td>42.71</td>
                                    <td>42.71</td>
                                </tr>
                                <tr>
                                    <td>Cash In</td>
                                    <td style="text-align: right">'.number_format($dailyCashIn, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCashIn, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 3px solid black"><span style="visibility: hidden">sd</span></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td style="text-align: right">'.number_format($totalDailyAdditions, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Cash FFB Purchase</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Cash Out</td>
                                    <td style="text-align: right">'.number_format($dailyCashOut, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCashOut, 2).'</td>
                                    <td>FFB C/F</td>
                                    <td>0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Credit Note</td>
                                    <td style="text-align: right">'.number_format($dailyCreditAdvRamp, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCreditAdvRamp, 2).'</td>
                                    <td>FFB In</td>
                                    <td>42.71</td>
                                    <td>42.71</td>
                                </tr>
                                <tr>
                                    <td>Credit FFB Advance (Ramp)</td>
                                    <td style="text-align: right">'.number_format($dailyCreditAdvRamp, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCreditAdvRamp, 2).'</td>
                                    <td>FFB In (rejected)</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>Credit FFB Advance (HQ)</td>
                                    <td style="text-align: right">'.number_format($dailyCreditAdvHq, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCreditAdvHq, 2).'</td>
                                    <td>FM</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>Cash FFB Advance</td>
                                    <td style="text-align: right">'.number_format($dailyCreditAdv, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumCreditAdv, 2).'</td>
                                    <td>FFB Out</td>
                                    <td>9.23</td>
                                    <td>9.23</td>
                                </tr>
                                <tr>
                                    <td>General Expenses</td>
                                    <td style="text-align: right">'.number_format($dailyGeneral, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumGeneral, 2).'</td>
                                    <td>FFB Balance</td>
                                    <td>33.48</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Petrol/Diesel</td>
                                    <td style="text-align: right">'.number_format($dailyPetrolDiesel, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumPetrolDiesel, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Staff Advance</td>
                                    <td style="text-align: right">'.number_format($dailyStaffAdv, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumStaffAdv, 2).'</td>
                                    <td>Shortage/Return</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>Staff Salary</td>
                                    <td style="text-align: right">'.number_format($dailyStaffSal, 2).'</td>
                                    <td style="text-align: right">'.number_format($accumStaffSal, 2).'</td>
                                    <td>Balance C/F</td>
                                    <td>33.48</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><span style="visibility: hidden">sd</span></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 3px solid black"><span style="visibility: hidden">sd</span></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                    <td style="border-bottom: 3px solid black"></td>
                                </tr>
                                <tr class="total-row">
                                    <td>Cash Balance</td>
                                    <td style="text-align: right">'.number_format($cashBalance, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                ';

                ######## CASH Sections ##########
                if (!empty($weights['Purchase'])) {
                    $message .= '
                        <div class="detail-section">
                            <div class="detail-title">CASH</div>
                    ';

                    $totalCashMt = 0.00;
                    $totalCashPrice = 0.00;
                    foreach ($weights['Purchase'] as $weight) {
                        $totalCashMt += (float) $weight['final_weight']/1000;
                        $totalCashPrice += (float) $weight['total_price'];
                        $message .= '
                            <div class="detail-item">'.$weight['transaction_id'].' FFB '.formatWeight($weight['final_weight']/1000).' MT ('.strtoupper($weight['supplier_name']).') <span class="amount-right"><span class="weight-value">'.formatWeight($weight['final_weight']/1000).'</span><span class="price-value">'.number_format($weight['total_price'], 2).'</span></span></div>
                        ';
                    }

                    $message .= '
                            <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right"><span class="weight-value">'.number_format($totalCashMt, 2).'</span><span class="price-value">'.number_format($totalCashPrice, 2).'</span></span></div>
                        </div>
                    ';
                }
                
                ######## CASH IN Sections ##########
                if (!empty($cashIns)) {
                    $message .= '
                        <div class="detail-section">
                            <div class="detail-title">CASH IN</div>
                    ';

                    $cashInTotal = 0.00;
                    foreach ($cashIns as $cashIn) {
                        $cashInTotal += (float) $cashIn['amount'];
                        $message .= '
                            <div class="detail-item">'.(!empty($cashIn['description']) ? $cashIn['description'] : '<span style="visibility:hidden">Test</span>').' <span class="amount-right">'.number_format($cashIn['amount'], 2).'</span></div>
                        ';
                    }

                    $message .= '
                            <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">'.number_format($cashInTotal, 2).'</span></div>
                        </div>
                    ';
                }

                // <div class="detail-section">
                //             <div class="detail-title">FFB OUT</div>
                //             <div class="detail-item">Sale Eng Hong Palm Oil Mill <span class="amount-right">9.23</span></div>
                //             <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">9.23</span></div>
                //         </div>

                ######## GENERAL EXPENSES Sections ##########
                if (!empty($generalExpenses)) {
                    $message .= '
                        <div class="detail-section">
                            <div class="detail-title">GENERAL EXPENSES</div>
                    ';

                    $generalExpensesTotal = 0.00;
                    foreach ($generalExpenses as $generalExpense) {
                        $generalExpensesTotal += (float) $generalExpense['amount'];
                        $message .= '
                            <div class="detail-item">'.(!empty($generalExpense['description']) ? $generalExpense['description'] : '<span style="visibility:hidden">Test</span>').' <span class="amount-right">'.number_format($generalExpense['amount'], 2).'</span></div>
                        ';
                    }

                    $message .= '
                            <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">'.number_format($generalExpensesTotal, 2).'</span></div>
                        </div>
                    ';
                }

                ######## PETROL/DIESEL Sections ##########
                if (!empty($petrolDiesels)) {
                    $message .= '
                        <div class="detail-section">
                            <div class="detail-title">PETROL/DIESEL</div>
                    ';

                    $petrolDieselsTotal = 0.00;
                    foreach ($petrolDiesels as $petrolDiesel) {
                        $petrolDieselsTotal += (float) $petrolDiesel['amount'];
                        $message .= '
                            <div class="detail-item">'.(!empty($petrolDiesel['description']) ? $petrolDiesel['description'] : '<span style="visibility:hidden">Test</span>').' <span class="amount-right">'.number_format($petrolDiesel['amount'], 2).'</span></div>
                        ';
                    }

                    $message .= '
                            <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">'.number_format($petrolDieselsTotal, 2).'</span></div>
                        </div>
                    ';
                }

                ######## CASH OUT Sections ##########
                // if (!empty($petrolDiesels)) {
                //     $message .= '
                //         <div class="detail-section">
                //             <div class="detail-title">PETROL/DIESEL</div>
                //     ';

                //     $petrolDieselsTotal = 0.00;
                //     foreach ($petrolDiesels as $petrolDiesel) {
                //         $petrolDieselsTotal += (float) $petrolDiesel['amount'];
                //         $message .= '
                //             <div class="detail-item">'.(!empty($petrolDiesel['description']) ? $petrolDiesel['description'] : '<span style="visibility:hidden">Test</span>').' <span class="amount-right">'.number_format($petrolDiesel['amount'], 2).'</span></div>
                //         ';
                //     }

                //     $message .= '
                //             <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">'.number_format($petrolDieselsTotal, 2).'</span></div>
                //         </div>
                //     ';
                // }

                $message .= '
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
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>