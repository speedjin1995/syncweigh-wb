<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../requires/functions.php';

/**
 * Weighing slip (HTML) for a weighing record or an empty container record.
 */
class WeightPrintService extends BaseService {

    /**
     * Report and weighing screens: the record's transaction status must allow Print on that screen
     */
    public function canPrint($id, $isEmptyContainer, $source) {
        $table = $isEmptyContainer == 'Y' ? 'Weight_Container' : 'Weight';
        $status = $this->transactionStatus($table, $id);

        return $source == 'report'
            ? statusPermitted('Reports', $status, 'print')
            : statusPermitted('Weighing', $status, 'print');
    }

    /**
     * Slip HTML for one record. Marks the record printed, unless it was printed before (then it is a DUPLICATE).
     */
    public function slipHtml($id, $isEmptyContainer) {
        $company = $this->company();

        $printTable = $isEmptyContainer == 'Y' ? 'Weight_Container' : 'Weight';
        $row = $this->selectOne("SELECT * FROM {$printTable} WHERE id=?", 's', [$id]);

        if (!$row) {
            throw new Exception('Unable to read data');
        }

        // Already printed before = this is a reprint, mark it as duplicate
        $isDuplicate = ($row['is_printed'] ?? 'N') == 'Y';
        $duplicateLabel = $isDuplicate ? '<b>(DUPLICATE)</b>' : '';

        $isPurchase = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

        if ($isPurchase) {
            $cid = $row['supplier_code'];
            $party = $this->selectOne("SELECT * FROM Supplier WHERE supplier_code=?", 's', [$cid]);
            $pid = $row['raw_mat_code'];
            $item = $this->selectOne("SELECT * FROM Raw_Mat WHERE raw_mat_code=?", 's', [$pid]);
        } else {
            $cid = $row['customer_code'];
            $party = $this->selectOne("SELECT * FROM Customer WHERE customer_code=?", 's', [$cid]);
            $pid = $row['product_code'];
            $item = $this->selectOne("SELECT * FROM Product WHERE product_code=?", 's', [$pid]);
        }

        $customer = $party['name'] ?? '';
        $product = $item['name'] ?? '';

        // Alam Flora slip: weights stored in kg, printed in MT
        $custName = $customer ?: ($isPurchase ? $row['supplier_name'] : $row['customer_name']);
        $wasteName = $product ?: ($isPurchase ? $row['raw_mat_name'] : $row['product_name']);
        $transactionDate = date('d/m/Y', strtotime((string) $row['transaction_date']));
        $timeIn = !empty($row['gross_weight1_date']) ? date('H:i', strtotime($row['gross_weight1_date'])) : '';
        $timeOut = !empty($row['tare_weight1_date']) ? date('H:i', strtotime($row['tare_weight1_date'])) : '';
        $firstWeight = number_format((float) $row['gross_weight1'] / 1000, 2, '.', ',');
        $secondWeight = number_format((float) $row['tare_weight1'] / 1000, 2, '.', ',');
        $nettWeight = number_format((float) $row['final_weight'] / 1000, 2, '.', ',');
        $pricePerTon = number_format((float) $row['unit_price'], 2, '.', ',');
        $amount = number_format((float) $row['total_price'], 2, '.', ',');

        $contractorCode = '';
        $contractorName = '';
        if (!empty($row['contractor_id'])) {
            $contractor = $this->selectOne("SELECT * FROM Contractor WHERE id=?", 'i', [$row['contractor_id']]);
            if ($contractor) {
                $contractorCode = $contractor['contractor_code'];
                $contractorName = $contractor['name'];
            }
        }

        if (!$isDuplicate) {
            $this->run("UPDATE {$printTable} SET is_printed = 'Y' WHERE id = ? AND is_printed = 'N'", 's', [$id]);
        }

        return $this->slipTemplate($company, $row, [
            'duplicateLabel' => $duplicateLabel,
            'transactionDate' => $transactionDate,
            'cid' => $cid,
            'pid' => $pid,
            'custName' => $custName,
            'wasteName' => $wasteName,
            'timeIn' => $timeIn,
            'timeOut' => $timeOut,
            'firstWeight' => $firstWeight,
            'secondWeight' => $secondWeight,
            'nettWeight' => $nettWeight,
            'pricePerTon' => $pricePerTon,
            'amount' => $amount,
            'contractorCode' => $contractorCode,
            'contractorName' => $contractorName,
        ]);
    }

    // Company header (defaults used when the Company row is missing)
    private function company() {
        $company = [
            'name' => 'SYNCTRONIX TECHNOLOGY (M) SDN BHD',
            'address1' => 'No.34, Jalan Bagan 1,',
            'address2' => 'Taman Bagan,',
            'address3' => '13400 Butterworth. Penang. Malaysia.',
            'phone' => '6043325822',
        ];

        $row = $this->selectOne("SELECT * FROM Company WHERE id=?", 's', ['1']);
        if ($row) {
            $company['name'] = $row['name'];
            $company['address1'] = $row['address_line_1'];
            $company['address2'] = $row['address_line_2'];
            $company['address3'] = $row['address_line_3'];
            $company['phone'] = $row['phone_no'];
        }

        return $company;
    }

    private function transactionStatus($table, $id) {
        $row = $this->selectOne("SELECT transaction_status FROM {$table} WHERE id = ?", 's', [$id]);
        return $row ? $row['transaction_status'] : '';
    }

    private function slipTemplate($company, $row, $v) {
        $compname = $company['name'];
        $compaddress = $company['address1'];
        $compaddress2 = $company['address2'];
        $compaddress3 = $company['address3'];
        $compphone = $company['phone'];
        $duplicateLabel = $v['duplicateLabel'];
        $transactionDate = $v['transactionDate'];
        $custName = $v['custName'];
        $cid = $v['cid'];
        $pid = $v['pid'];
        $wasteName = $v['wasteName'];
        $timeIn = $v['timeIn'];
        $timeOut = $v['timeOut'];
        $firstWeight = $v['firstWeight'];
        $secondWeight = $v['secondWeight'];
        $nettWeight = $v['nettWeight'];
        $pricePerTon = $v['pricePerTon'];
        $amount = $v['amount'];
        $contractorCode = $v['contractorCode'];
        $contractorName = $v['contractorName'];

        return '<html>
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
                                position: fixed;
                                bottom: 0;
                            }

                            .sign td {
                                width: 35%;
                                border-top: 1px dashed #000;
                                padding-top: 4px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <span style="font-size: 18px; font-weight: bold;">' . $compname . '</span><br>
                            <span>' . $compaddress . '</span><br>
                            <span>' . $compaddress2 . '</span><br>
                            <span>' . $compaddress3 . '</span><br>
                            <span> Tel : ' . $compphone . '</span>
                        </div>

                        <table class="slip">
                            <tr>
                                <td width="14%">Ticket No.</td><td width="22%">: ' . $row['transaction_id'] . '</td>
                                <td width="14%">Date</td><td width="18%">: ' . $transactionDate . '</td>
                                <td width="20%" style="text-align: center;">' . $duplicateLabel . '</td><td width="12%"></td>
                            </tr>
                            <tr>
                                <td>Vehicle No.</td><td>: ' . $row['lorry_plate_no1'] . '</td>
                                <td>Container No.</td><td colspan="3">: ' . $row['container_no'] . '</td>
                            </tr>
                            <tr>
                                <td>Customer</td><td>: ' . $cid . '</td>
                                <td colspan="4">' . $custName . '</td>
                            </tr>
                            <tr>
                                <td>Contractor</td><td>: ' . $contractorCode . '</td>
                                <td colspan="4">' . $contractorName . '</td>
                            </tr>
                            <tr>
                                <td>Area</td><td>: ' . $row['destination_code'] . '</td>
                                <td colspan="4">' . $row['destination'] . '</td>
                            </tr>
                            <tr>
                                <td>Bin No.</td><td>: ' . $row['container_no2'] . '</td>
                                <td>Bin Type</td><td colspan="3">: ' . $row['seal_no2'] . '</td>
                            </tr>
                            <tr>
                                <td>Waste</td><td>: ' . $pid . '</td>
                                <td colspan="4">' . $wasteName . '</td>
                            </tr>
                            <tr>
                                <td>Time In</td><td>: ' . $timeIn . '</td>
                                <td>Time Out</td><td>: ' . $timeOut . '</td>
                                <td>First Weight (MT)</td><td>: ' . $firstWeight . '</td>
                            </tr>
                            <tr>
                                <td>Price/Ton</td><td>: ' . $pricePerTon . '</td>
                                <td></td><td></td>
                                <td>Second Weight(MT)</td><td>: ' . $secondWeight . '</td>
                            </tr>
                            <tr>
                                <td>Amount (RM)</td><td>: ' . $amount . '</td>
                                <td></td><td></td>
                                <td>Nett Weight (MT)</td><td>: ' . $nettWeight . '</td>
                            </tr>
                            <tr>
                                <td>Remarks</td><td colspan="5" style="white-space: normal;">: ' . htmlspecialchars($row['remarks'] ?? '') . '</td>
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
    }

    private function selectOne($sql, $types, $params) {
        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('WeightPrintService - execute failed');
            throw new Exception('Something went wrong');
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function run($sql, $types, $params) {
        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('WeightPrintService - execute failed');
            throw new Exception('Something went wrong');
        }

        $stmt->close();
    }

    private function prepareOrFail($sql) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log('WeightPrintService - ' . $this->db->error);
            throw new Exception('Something went wrong');
        }

        return $stmt;
    }
}
