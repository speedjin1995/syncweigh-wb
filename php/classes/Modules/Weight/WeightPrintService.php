<?php
namespace App\Modules\Weight;

use App\Core\BaseService;

/**
 * Weighing slip HTML, ported from the legacy php/print.php. The template is kept as it was, including its spacing.
 */
class WeightPrintService extends BaseService
{
    /**
     * Slip for one Weight record, or a Weight_Container record when $isEmptyContainer is Y.
     * $languageArray is the session message_resource cache; $language the slip language.
     */
    public function slipHtml(string $id, string $isEmptyContainer, array $languageArray, string $language): array
    {
        $compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
        $compreg = '123456789-X';
        $compaddress = 'No.34, Jalan Bagan 1,';
        $compaddress2 = 'Taman Bagan,';
        $compaddress3 = '13400 Butterworth. Penang. Malaysia.';
        $compphone = '6043325822';
        $compiemail = 'admin@synctronix.com.my';

        if ($company = $this->fetchOne("SELECT * FROM Company WHERE id=?", 's', ['1'])) {
            $compname = $company['name'];
            $compreg = $company['company_reg_no'];
            $compaddress = $company['address_line_1'];
            $compaddress2 = $company['address_line_2'];
            $compaddress3 = $company['address_line_3'];
            $compphone = $company['phone_no'];
            $compiemail = $company['fax_no'];
        }

        $row = $this->fetchOne($isEmptyContainer == 'Y' ? "SELECT * FROM Weight_Container WHERE id=?" : "SELECT * FROM Weight WHERE id=?", 's', [$id]);
        if ($row === null) {
            return ['status' => 'failed', 'message' => 'Unable to read data'];
        }

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

            if ($row2 = $this->fetchOne("SELECT * FROM Supplier WHERE supplier_code=?", 's', [$cid])) {
                $customer = $row2['name'];
                $customerR = $row2['company_reg_no'] ?? '';
                $customerP = $row2['phone_no'] ?? '-';
                $customerA = $row2['address_line_1'];
                $customerA2 = $row2['address_line_2'];
                $customerA3 = $row2['address_line_3'];
                $customerE = $row2['fax_no'] ?? '-';
            }

            $pid = $row['raw_mat_code'];
        
            if ($row3 = $this->fetchOne("SELECT * FROM Raw_Mat WHERE raw_mat_code=?", 's', [$pid])) {
                $product = $row3['name'];
                $variance = $row3['variance'] ?? '';
                $high = $row3['high'] ?? '0';
                $low = $row3['low'] ?? '0';
                $price = $row3['price'] ??  '0.00';
            }
        }
        else{
            $cid = $row['customer_code'];
            $orderSuppWeight = floatval($row['order_weight']);
        
            if ($row2 = $this->fetchOne("SELECT * FROM Customer WHERE customer_code=?", 's', [$cid])) {
                $customer = $row2['name'];
                $customerR = $row2['company_reg_no'] ?? '';
                $customerP = $row2['phone_no'] ?? '-';
                $customerA = $row2['address_line_1'];
                $customerA2 = $row2['address_line_2'];
                $customerA3 = $row2['address_line_3'];
                $customerE = $row2['fax_no'] ?? '-';
            }

            $pid = $row['product_code'];
        
            if ($row3 = $this->fetchOne("SELECT * FROM Product WHERE product_code=?", 's', [$pid])) {
                $product = $row3['name'];
                $variance = $row3['variance'] ?? '';
                $high = $row3['high'] ?? '0';
                $low = $row3['low'] ?? '0';
                $price = $row3['price'] ??  '0.00';
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
                                        padding-left: 0.2in;
                                        padding-right: 0.2in;
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

                                .table-border {
                                    border: 1px solid #000000;
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
                                    <td style="width: 70%;">
                                        <p style="font-size: 14px;">
                                            <span style="font-weight: bold;font-size: 16px; margin-bottom: 10px; display: inline-block;">'.$compname.'</span><br>
                                            <span> Reg No.: '.$compreg.'</span><br>
                                            <span>'.$compaddress.'</span><br>
                                            <span>'.$compaddress2.'</span><br>
                                            <span>'.$compaddress3.'</span><br>
                                            <span>Tel/Fax: '.$compphone.' / '.$compiemail.'</span>
                                        </p>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="vertical-align: top; font-size: 14px;">
                                            <span style="font-size: 24px; font-weight: bold; margin-bottom: 10px; display: inline-block;">'. $languageArray[$transacationStatus][$language] . '' . $languageArray['slip_code'][$language].'</span><br>
                                            <span>'. $languageArray['ticket_no_code'][$language].' &nbsp;:&nbsp; <b>'.$row['transaction_id'].'</b></span><br>
                                            <span>'. $languageArray['date_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 1.5px;">:&nbsp;&nbsp;'.$transactionDate.'</span><br>
                                            <span>'. $languageArray['do_no_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;</span><span>:&nbsp;&nbsp;'.$row['delivery_no'].'</span><br>
                                            <span>'. $languageArray['po_no_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left:2.5px">:&nbsp;&nbsp;'.$row['purchase_order'].'</span><br>
                                        </p>
                                    </td>
                                </tr>
                                <tr style="visibility:hidden;">
                                    <td style="font-size: 3px;">Placeholder for empty space</td>
                                </tr>
                                <tr style="border-top: 1px solid black;">
                                    <td style="vertical-align: top;">
                                        <p style="margin-top: 5px; font-size: 14px;">
                                            <span">'.$languageArray['customer_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 23px">:&nbsp; <b>'.$customer.'</b></span><br>
                                            <span">'.$languageArray['transporter_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 12.5px">:&nbsp; '.$row['transporter'].'</span><br>
                                            <span">'.$languageArray['destination_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 12.5px">:&nbsp; '.$row['destination'].'</span>
                                        </p>';

                                        // $message .= '
                                        //     <br>
                                        //     <!-- <span><b>Net Weight &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 17.5px">:&nbsp; '.($finalWeight ? $this->formatWeight($finalWeight).' kg' : '-').'</b></span><br>-->
                                        //     <span">Variance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 12.5px">:&nbsp; '.($weightDifference ? $this->formatWeight($weightDifference).' kg' : '-').'</span><br>
                                        //     <span>Product &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="margin-left: 21px">:&nbsp; '.($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc' ? $row['product_code'] . ' - ' . $row['product_name'] : $row['raw_mat_code'] . ' - ' . $row['raw_mat_name']) .'</span><br>';

                                        //     if ($row['weight_type'] == 'Different Container' && $isEmptyContainer == 'N'){
                                        //         $message .= '<span>Destination &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="margin-left: 3px">:&nbsp;</span>'.$row['destination_code']. ' - '.$row['destination'].'</span>';
                                        //     }

                                        // $message .= '</p>';

                                        $message .= '
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="margin-top: 5px; font-size: 14px;">
                                            <span">'.$languageArray['order_weight_code'][$language].'</span><span style="margin-left: 10px">:&nbsp; <b>'.$row['order_weight'].'</b></span><br>
                                            <span">'.$languageArray['variance_code'][$language].'</span><span style="margin-left: 37px">:&nbsp; '.$row['weight_different'].'</span><br>
                                            <span">% '.$languageArray['variance_code'][$language].'</span><span style="margin-left: 22px">:&nbsp; '.$row['weight_different_perc'].'</span>
                                        </p>
                                    </td>
                                </tr>';
                            $message .= '
                            </table>';

                            if ($row['weight_type'] == 'Different Container' && $isEmptyContainer == 'N'){
                                $message .= '
                                    <table style="width:100%; border:0px solid black; margin-top: 10px;">
                                        <tr>
                                            <th style="border:1px solid black;">New Empty Entrance Bin</th>
                                        </tr>
                                        <tr>
                                            <td style="border:1px solid black;">'.$row['replacement_container'].'</td>
                                        </tr>
                                        <tr>
                                            <th style="border:1px solid black;">Weight</th>
                                        </tr>
                                        <tr>
                                            <td style="border:1px solid black;">'.$row['empty_container2_weight'].'</td>
                                        </tr>
                                    </table>
                                ';
                            }else{
                                $message .= '
                                    <table style="width:100%; border:0px solid black; margin-top: 10px;">
                                        <tr style="font-size: 14px;text-align: center;">
                                            <th width="25%" style="border:1px solid black;">'.$languageArray['container_no1_code'][$language].'</th>
                                            <th width="25%" style="border:1px solid black;">'.$languageArray['seal_no1_code'][$language].'</th>
                                            <th width="25%" style="border:1px solid black;">'.$languageArray['container_no2_code'][$language].'</th>
                                            <th width="25%" style="border:1px solid black;">'.$languageArray['seal_no2_code'][$language].'</th>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;">
                                            <td style="border:1px solid black;">'.(!empty($row["container_no"]) ? $row["container_no"] : '&nbsp;').'</td>
                                            <td style="border:1px solid black;">'.$row["seal_no"].'</td>
                                            <td style="border:1px solid black;">'.(!empty($row["container_no2"]) ? $row["container_no2"] : '&nbsp;').'</td>
                                            <td style="border:1px solid black;">'.$row["seal_no2"].'</td>    
                                        </tr>
                                    </table>
                                ';
                            }
                            
                            if($row['weight_type'] == 'Container' && $isEmptyContainer == 'N'){
                                $message .= '
                                <table style="width:100%; border:0px solid black; margin-top: 10px;">
                                    <tr style="font-size: 14px;text-align: center;">
                                        <th style="border:1px solid black;">Incoming Date/Time</th>
                                        <th style="border:1px solid black;">Outgoing Date/Time</th>
                                        <th colspan="2" style="border:1px solid black;">Prime Mover No. & Weight (kg)</th>
                                        <th style="border:1px solid black;">Tare (kg)</th>
                                        <th style="border:1px solid black;">Nett (kg)</th>
                                    </tr>
                                    <tr style="font-size: 14px;text-align: center;">
                                        <td style="border:1px solid black;">'.$grossWeightTime.'</td>
                                        <td style="border:1px solid black;">'.$tareWeightTime.'</td>
                                        <td style="border:1px solid black;">'.$row['lorry_plate_no1'].'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['gross_weight1']).'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['tare_weight1']).' kg</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['nett_weight1']).' kg</td>
                                    </tr>
                                    <tr style="font-size: 14px;text-align: center;">
                                        <td style="border:1px solid black;">'.$grossWeightTime2.'</td>
                                        <td style="border:1px solid black;">'.$tareWeightTime2.'</td>
                                        <td style="border:1px solid black;">'.$row['lorry_plate_no2'].'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['gross_weight2']).'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['tare_weight2']).' kg</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['nett_weight2']).' kg</td>
                                    </tr>
                                    <tr style="font-size: 14px;text-align: center;">
                                        <td colspan="4" style="text-align: left;"><b>Transporter &nbsp;:&nbsp;</b> <span style="margin-left: 10px">'.$row['transporter'].'</span></td>
                                        <td style="border:1px solid black;">Final Weight</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight(abs((int)$row['nett_weight1'] - (int)$row['nett_weight2'])).' kg</td>
                                    </tr>
                                    <tr style="font-size: 14px;text-align: center;">
                                        <td colspan="4" style="text-align: left;"><b>Destination &nbsp&nbsp;:&nbsp;</b> <span style="margin-left: 10px">'.$row['destination'].'</span></td>
                                        <td style="border:1px solid black;">Reduce Weight</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['reduce_weight']).' kg</td>
                                    </tr>
                                    <tr style="font-size: 14px;text-align: center;font-weight:bold;">
                                        <td colspan="4" style="text-align: left;">Remarks &nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                        <td style="border:1px solid black;">Nett Weight</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['final_weight']).' kg</td>
                                    </tr>
                                </table>';
                            }
                            else if ($row['weight_type'] == 'Different Container' && $isEmptyContainer == 'N'){
                                $message .= '
                                    <table style="width:100%; border: 1px solid black; text-align: left; font-size: 14px; margin-top: 5px;">
                                        <tr style="text-align: center; border: 1px solid black;">
                                            <th rowspan="2" class="table-border" width="10%">Bin</th>
                                            <th rowspan="2" class="table-border" width="25%">Date/Time</th>
                                            <th rowspan="2" class="table-border">Vehicle</th>
                                            <th rowspan="2" class="table-border" width="10%">Gross Weight</th>
                                            <th class="table-border" colspan="2">Tare Weight</th>
                                            <th rowspan="2" class="table-border">Nett Weight</th>
                                        </tr>
                                        <tr style="text-align: center;">
                                            <th class="table-border">Vehicle</th>
                                            <th class="table-border">Bin</th>
                                        </tr>
                                        <tr style="text-align: center;">
                                            <td class="table-border">'.$row['container_no'].'</td>
                                            <td class="table-border">
                                                In: '.$row['gross_weight1_date'].'<br>
                                                Out: '.$row['tare_weight2_date'].'
                                            </td>
                                            <td class="table-border">
                                                In: '.$row['lorry_plate_no1'].'<br>
                                                Out: '.$row['lorry_plate_no2'].'
                                            </td>
                                            <td class="table-border">
                                                '.$row['gross_weight1'].' kg<br>
                                                '.$row['tare_weight2'].' kg
                                            </td>
                                            <td class="table-border">
                                                '.$row['tare_weight1'].' kg<br>
                                                '.$row['lorry_no2_weight'].' kg
                                            </td>
                                            <td class="table-border">
                                                -<br>
                                                '.$row['nett_weight1'].' kg
                                            </td>
                                            <td class="table-border">
                                                -<br>
                                                '.$row['nett_weight2'].' kg
                                            </td>
                                        </tr>
                                        <!--<tr style="text-align: center;">
                                            <td class="table-border">'.$row['replacement_container'].'</td>
                                            <td class="table-border">
                                                Date In: '.$row['gross_weight2_date'].'
                                            </td>
                                            <td class="table-border">
                                                In: '.$row['lorry_plate_no2'].'
                                            </td>
                                            <td class="table-border">
                                                '.$row['gross_weight2'].' kg
                                            </td>
                                            <td class="table-border">
                                                '.$row['lorry_no2_weight'].' kg
                                            </td>
                                            <td class="table-border">
                                                '.$row['empty_container2_weight'].' kg
                                            </td>
                                            <td class="table-border">
                                                -
                                            </td>
                                        </tr>-->
                                        <tr style="text-align: center;">
                                            <td class="table-border" colspan="6" style="text-align: right; padding-right: 20px;">
                                                Final Weight
                                            </td>
                                            <td class="table-border">
                                                '.$row['final_weight'].' kg
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 5px; font-size: 14px; margin-bottom: 60px;">
                                        <span>Transporter&nbsp;&nbsp;:&nbsp;&nbsp; '.$row['transporter'].'</span><br>
                                        <span>Remarks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;'.$row['remarks'].'</span>
                                    </div>
                                ';
                            }
                            else{
                                $message .= '<br>
                                <table style="width:100%; border:0px solid black; margin-top: -10px;">
                                    <tr>
                                        <th style="border:1px solid black;font-size: 18px;text-align: center;" width="20%">'.$languageArray['vehicle_no_code'][$language].'</th>
                                        <th colspan="2" style="border:1px solid black;font-size: 18px;text-align: center;" width="30%">'.$languageArray['product_description_code'][$language].'</th>
                                        <th style="border:1px solid black;font-size: 18px;text-align: center;" width="25%">'.$languageArray['datetime_code'][$language].'</th>
                                        <th colspan="2" style="border:1px solid black;font-size: 18px;text-align: center;" width="20%">'.$languageArray['weight_code'][$language].' ('.$languageArray['kg_code'][$language].')</th>
                                    </tr>
                                    <tr style="font-size: 16px;text-align: center;">
                                        <td rowspan="2" style="border:1px solid black;">'.$row['lorry_plate_no1'].'</td>';

                                        if ($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local'){
                                            $message .= '<td rowspan="2" colspan="2" style="border:1px solid black;">'.$row['raw_mat_name'].'</td>';
                                        }else{
                                            $message .= '<td rowspan="2" colspan="2" style="border:1px solid black;">'.$row['product_name'].'</td>';
                                        }

                                    $message .= '    
                                        <td style="border:1px solid black;">'.$grossWeightTime.'</td>
                                        <td style="border:1px solid black; font-weight: bold;">'.$languageArray['in_code'][$language].'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['gross_weight1']).' kg</td>
                                    </tr>
                                    <tr style="font-size: 16px;text-align: center;">
                                        <td style="border:1px solid black;">'.$tareWeightTime.'</td>
                                        <td style="border:1px solid black; font-weight: bold;">'.$languageArray['out_code'][$language].'</td>
                                        <td style="border:1px solid black;">'.$this->formatWeight($row['tare_weight1']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">'.$languageArray['remarks_code'][$language].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">'.$languageArray['reduce_code'][$language].'</td>
                                        <td style="border:1px solid black;font-size: 16px;text-align: center;">'.$this->formatWeight($row['reduce_weight']).' kg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: center;">'.$languageArray['nett_code'][$language].'</td>
                                        <td style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: center;">'.$this->formatWeight($row['final_weight']).' kg</td>
                                    </tr>
                                </table><br>';                           
                            }
                            
                            $message .= '
                            <table style="width: 100%; position: fixed; bottom: 0; left: 0;">
                                <tr>
                                    <td style="vertical-align: top; font-size: 14px; width: 25%;">
                                        <hr width="100%" style="margin-left: 0; text-align: left;">
                                        <span>'.$languageArray['first_weight_by_code'][$language].': '.$row['gross_weight_by1'].'<br> '.$languageArray['second_weight_by_code'][$language].': '.$row['tare_weight_by1'].'</span>
                                    </td>
                                    <td style="width: 2%;"></td>
                                    <td style="vertical-align: top; font-size: 14px; width: 25%;">
                                        <hr width="100%" style="margin-left: 0; text-align: left;">
                                        <span>'.$languageArray['acknowledge_by_admin_code'][$language].'</span>
                                    </td>
                                    <td style="width: 2%;"></td>
                                    <td style="vertical-align: top; font-size: 14px; width: 25%;">
                                        <hr width="100%" style="margin-left: 0; text-align: left;">
                                        <span>'.$languageArray['received_by_code'][$language].'</span><br>
                                        <span>'.$languageArray['name_code'][$language].': </span><br>
                                        <span>I/C: </span>
                                    </td>
                                </tr>
                            </table>
                        </body>
                    </html>';

        return ['status' => 'success', 'message' => $message];
    }

    /**
     * Weight with thousands separators and no trailing .00; zero is returned as it is
     */
    private function formatWeight($weight)
    {
        if ($weight != 0){
            $formatted = number_format(ltrim($weight, '0'), 2, '.', ',');
            $formatted = preg_replace('/\.00$/', '', $formatted);
        }else{
            $formatted = $weight;
        }

        return $formatted;
    }
}
