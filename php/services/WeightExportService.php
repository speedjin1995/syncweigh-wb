<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../requires/functions.php';
require_once __DIR__ . '/../requires/lookup.php';

/**
 * Weighing export (Excel and PDF) in the Alam Flora layout, weights in tonnes.
 * Used by WeightController::exportExcel and WeightController::exportPdf (weighing screen and weighing report).
 */
class WeightExportService extends BaseService {

    /**
     * Weight + Weight_Container rows matching the export filters.
     * $plants is null for ADMIN / SADMIN (all plants), otherwise the user's plant codes.
     * $category is the permission category of the calling screen (Weighing or Reports).
     */
    public function rows($input, $plants, $category) {
        $where = '';

        if ($plants !== null) {
            $plantCodes = array_map(function ($p) { return $this->db->real_escape_string($p); }, $plants);
            $where .= " and plant_code IN ('" . implode("', '", $plantCodes) . "')";
        }

        $where .= transactionStatusExclusion($this->db, 'transaction_status');
        $where .= permissionExclusion($category, 'transaction_status', 'weight_type', 'export');

        if (($input['isMulti'] ?? '') == 'Y') {
            // Selected rows: weighing records only (the screens send Weight ids)
            $ids = is_array($input['ids'] ?? null) ? $input['ids'] : explode(',', (string) ($input['ids'] ?? ''));
            $ids = array_filter(array_map('intval', $ids));
            $idSql = $ids ? implode(',', $ids) : '0';
            $sql = "SELECT * FROM Weight WHERE status = '0'{$where} and id IN ({$idSql}) ORDER BY created_date ASC";
        } else {
            $where .= $this->filters($input);
            $sql = "SELECT * FROM Weight WHERE status = '0'{$where}
                UNION ALL
                SELECT * FROM Weight_Container WHERE status = '0'{$where}
                ORDER BY created_date ASC";
        }

        $result = $this->db->query($sql);
        if (!$result) {
            error_log('WeightExportService::rows - ' . $this->db->error);
            throw new Exception('Something went wrong');
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Filter clause shared by both tables (unqualified columns)
     */
    private function filters($input) {
        $value = function ($key) use ($input) {
            $v = $input[$key] ?? '';
            if (is_array($v)) return null;
            $v = trim((string) $v);
            return ($v === '' || $v === '-') ? null : $v;
        };
        $esc = function ($v) { return $this->db->real_escape_string($v); };
        $sql = '';

        if ($value('fromDate') !== null && ($date = DateTime::createFromFormat('d-m-Y', $value('fromDate')))) {
            // Tare (out) date; records without a tare yet fall back to the transaction date
            $sql .= " and COALESCE(tare_weight1_date, transaction_date) >= '" . $date->format('Y-m-d 00:00:00') . "'";
        }
        if ($value('toDate') !== null && ($date = DateTime::createFromFormat('d-m-Y', $value('toDate')))) {
            $sql .= " and COALESCE(tare_weight1_date, transaction_date) <= '" . $date->format('Y-m-d 23:59:59') . "'";
        }

        if ($value('transactionStatus') !== null) $sql .= " and transaction_status = '" . $esc($value('transactionStatus')) . "'";
        $sql .= inFilterQuery($this->db, 'customer_code', $input['customer'] ?? '');
        $sql .= inFilterQuery($this->db, 'supplier_code', $input['supplier'] ?? '');
        if ($value('vehicle') !== null) $sql .= " and lorry_plate_no1 like '%" . $esc($value('vehicle')) . "%'";
        if ($value('weighingType') !== null) $sql .= " and weight_type like '%" . $esc($value('weighingType')) . "%'";
        if ($value('customerType') !== null) $sql .= " and customer_type like '%" . $esc($value('customerType')) . "%'";
        $sql .= inFilterQuery($this->db, 'product_code', $input['product'] ?? '');
        $sql .= inFilterQuery($this->db, 'raw_mat_code', $input['rawMat'] ?? '');
        if ($value('destination') !== null) $sql .= " and destination = '" . $esc($value('destination')) . "'";
        if ($value('plant') !== null) $sql .= " and plant_code = '" . $esc($value('plant')) . "'";
        if ($value('location') !== null) $sql .= " and location_id = '" . intval($value('location')) . "'";
        if ($value('transactionId') !== null) $sql .= " and transaction_id like '%" . $esc($value('transactionId')) . "%'";

        if ($value('containerNo') !== null) {
            $c = $esc($value('containerNo'));
            $sql .= " and (container_no like '%{$c}%' OR container_no2 like '%{$c}%')";
        }
        if ($value('sealNo') !== null) {
            $s = $esc($value('sealNo'));
            $sql .= " and (seal_no like '%{$s}%' OR seal_no2 like '%{$s}%')";
        }
        if ($value('invDelPo') !== null) {
            $v = $esc($value('invDelPo'));
            $sql .= " and (purchase_order like '%{$v}%' OR invoice_no like '%{$v}%' OR delivery_no like '%{$v}%')";
        }

        $status = $value('status');
        if ($status !== null) {
            if ($status == 'Cancelled' || $status == 'Cancel') {
                $sql .= " and is_cancel = 'Y'";
            } elseif ($status == 'Pending') {
                $sql .= " and is_complete = 'N' AND is_cancel = 'N'";
            } else {
                // Complete: cancelled records also carry is_complete = 'Y' (same as filterReports.php)
                $sql .= " and is_complete = 'Y' AND is_cancel = 'N'";
            }
        }

        return $sql;
    }

    /**
     * Headers, one assoc row per weighing (keyed by header), the totals (summed columns only, others blank),
     * the per-waste summary and the per-supplier-group / waste code breakdown.
     */
    public function layout($rows) {
        $gates = array();
        $gateQuery = $this->db->query("SELECT id, location_code FROM Location");
        while ($gate = $gateQuery->fetch_assoc()) {
            $gates[$gate['id']] = $gate['location_code'];
        }

        $vehicles = array();
        $vehicleQuery = $this->db->query("SELECT veh_number, vehicle_type, bin_no, bin_type FROM Vehicle WHERE status = '0'");
        while ($vehicle = $vehicleQuery->fetch_assoc()) {
            $vehicles[$vehicle['veh_number']] = $vehicle;
        }

        // Deposit movements are logged with remark 'Weighing <transaction_id>' (see DepositService::settle)
        $deposits = array();
        $remarks = array();
        foreach ($rows as $row) {
            $remarks[] = "'" . $this->db->real_escape_string('Weighing ' . $row['transaction_id']) . "'";
        }
        $depositQuery = empty($remarks) ? false : $this->db->query("SELECT remark, balance_before, balance_after FROM Deposit_Transaction WHERE remark IN (" . implode(',', $remarks) . ") ORDER BY id ASC");
        while ($depositQuery && $deposit = $depositQuery->fetch_assoc()) {
            if (!isset($deposits[$deposit['remark']])) {
                $deposits[$deposit['remark']] = array('before' => $deposit['balance_before']);
            }
            $deposits[$deposit['remark']]['after'] = $deposit['balance_after'];
        }

        $headers = array('TRANSACTION ID', 'SUPPLIER/CUSTOMER CODE', 'SUPPLIER/CUSTOMER NAME', 'VEHICLE NO.', 'VEHICLE TYPE', 'WASTE/PRODUCT CODE', 'WASTE/PRODUCT NAME', 'BIN NO', 'BIN TYPE',
            'GATE IN', 'DATE IN', 'TIME IN', 'WEIGHT IN (MT)', 'GATE OUT', 'DATE OUT', 'TIME OUT', 'WEIGHT OUT (MT)', 'NETT WEIGHT (MT)',
            'DEPOSIT (RM)', 'UNIT PRICE (RM)', 'SUB TOTAL PRICE (RM)', 'BALANCE CREDIT (RM)', 'DURATION', 'REMARKS', 'USER ID');
        $summedColumns = array('WEIGHT IN (MT)', 'WEIGHT OUT (MT)', 'NETT WEIGHT (MT)', 'SUB TOTAL PRICE (RM)', 'BALANCE CREDIT (RM)');

        $lines = array();
        $totals = array_fill_keys($headers, '');
        foreach ($summedColumns as $column) {
            $totals[$column] = 0.0;
        }
        $summary = array();
        $breakdown = array();

        foreach ($rows as $row) {
            $isSalesOrMisc = $row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc';
            $vehicle = $vehicles[$row['lorry_plate_no1']] ?? array();
            $deposit = $deposits['Weighing ' . $row['transaction_id']] ?? null;

            // Duration from gross to tare capture, H:MM
            $duration = '';
            if (!empty($row['gross_weight1_date']) && !empty($row['tare_weight1_date'])) {
                $minutes = intdiv(max(0, strtotime($row['tare_weight1_date']) - strtotime($row['gross_weight1_date'])), 60);
                $duration = sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
            }

            $line = array(
                'TRANSACTION ID' => $row['transaction_id'],
                'SUPPLIER/CUSTOMER CODE' => $isSalesOrMisc ? $row['customer_code'] : $row['supplier_code'],
                'SUPPLIER/CUSTOMER NAME' => $isSalesOrMisc ? $row['customer_name'] : $row['supplier_name'],
                'VEHICLE NO.' => $row['lorry_plate_no1'],
                'VEHICLE TYPE' => $vehicle['vehicle_type'] ?? '',
                'WASTE/PRODUCT CODE' => $isSalesOrMisc ? $row['product_code'] : $row['raw_mat_code'],
                'WASTE/PRODUCT NAME' => $isSalesOrMisc ? $row['product_name'] : $row['raw_mat_name'],
                'BIN NO' => $vehicle['bin_no'] ?? '',
                'BIN TYPE' => $vehicle['bin_type'] ?? '',
                'GATE IN' => $gates[$row['location_id']] ?? '',
                'DATE IN' => empty($row['gross_weight1_date']) ? '' : date('d/m/Y', strtotime($row['gross_weight1_date'])),
                'TIME IN' => empty($row['gross_weight1_date']) ? '' : date('h:i:s A', strtotime($row['gross_weight1_date'])),
                'WEIGHT IN (MT)' => (float) $row['gross_weight1'] / 1000,
                'GATE OUT' => $gates[$row['location_id2']] ?? '',
                'DATE OUT' => empty($row['tare_weight1_date']) ? '' : date('d/m/Y', strtotime($row['tare_weight1_date'])),
                'TIME OUT' => empty($row['tare_weight1_date']) ? '' : date('h:i:s A', strtotime($row['tare_weight1_date'])),
                'WEIGHT OUT (MT)' => (float) $row['tare_weight1'] / 1000,
                'NETT WEIGHT (MT)' => (float) $row['final_weight'] / 1000,
                'DEPOSIT (RM)' => $deposit ? (float) $deposit['before'] : '',
                'UNIT PRICE (RM)' => $row['unit_price'] === null || $row['unit_price'] === '' ? '' : (float) $row['unit_price'],
                'SUB TOTAL PRICE (RM)' => (float) $row['sub_total'],
                'BALANCE CREDIT (RM)' => $deposit ? (float) $deposit['after'] : '',
                'DURATION' => $duration,
                'REMARKS' => $row['remarks'],
                'USER ID' => $row['created_by'],
            );
            $lines[] = $line;

            foreach ($summedColumns as $column) {
                $totals[$column] += (float) $line[$column];
            }

            $name = $line['WASTE/PRODUCT NAME'];
            if (!isset($summary[$name])) {
                $summary[$name] = array('name' => $name, 'trips' => 0, 'tonnage' => 0.0, 'price' => 0.0);
            }
            $summary[$name]['trips']++;
            $summary[$name]['tonnage'] += (float) $line['NETT WEIGHT (MT)'];
            $summary[$name]['price'] += (float) $line['SUB TOTAL PRICE (RM)'];

            $code = (string) $line['SUPPLIER/CUSTOMER CODE'];
            $waste = (string) $line['WASTE/PRODUCT CODE'];
            // One group per supplier/customer code and waste/product code
            $key = $code . '|' . $waste;
            if (!isset($breakdown[$key])) {
                $breakdown[$key] = array('supplier' => $code, 'waste' => $waste, 'trips' => 0, 'tonnage' => 0.0);
            }
            $breakdown[$key]['trips']++;
            $breakdown[$key]['tonnage'] += (float) $line['NETT WEIGHT (MT)'];
        }
        uksort($breakdown, 'strnatcmp');

        $summaryTotal = array('trips' => 0, 'tonnage' => 0.0, 'price' => 0.0);
        foreach ($summary as $item) {
            $summaryTotal['trips'] += $item['trips'];
            $summaryTotal['tonnage'] += $item['tonnage'];
            $summaryTotal['price'] += $item['price'];
        }

        return array('headers' => $headers, 'rows' => $lines, 'totals' => $totals, 'summary' => array_values($summary),
            'summaryTotal' => $summaryTotal, 'breakdown' => array_values($breakdown));
    }

    /**
     * Label shown in the export title row for a transaction_status value
     */
    public function statusLabel($status) {
        $labels = array('Sales' => 'DISPATCH', 'Purchase' => 'RECEIVING', 'Local' => 'INTERNAL TRANSFER', 'Misc' => 'MISCELLANEOUS');
        return $labels[$status] ?? 'ALL';
    }

    /**
     * d-m-Y filter date to d/m/Y for the title row ('' when blank or invalid)
     */
    public function dateLabel($value) {
        $date = empty($value) || is_array($value) ? false : DateTime::createFromFormat('d-m-Y', $value);
        return $date ? $date->format('d/m/Y') : '';
    }

    /**
     * Send the layout as an .xlsx download
     */
    public function streamXlsx($layout, $fromLabel, $toLabel, $statusLabel) {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $green = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('rgb' => '00B050')),
        );

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Weight');
        $sheet->setCellValue('A1', 'FROM DATE: ' . $fromLabel . '   TO DATE: ' . $toLabel);
        $sheet->setCellValue('A2', 'TRANSACTION STATUS : ' . $statusLabel);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        // Header, weighings, totals
        $headerRow = 4;
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($layout['headers']));
        $sheet->fromArray($layout['headers'], null, 'A' . $headerRow, true);
        $row = $headerRow + 1;
        $text = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
        foreach ($layout['rows'] as $line) {
            $sheet->fromArray(array_values($line), null, 'A' . $row, true);
            // Codes stay text (1, 888, 001 would otherwise become numbers)
            $sheet->setCellValueExplicit('B' . $row, (string) $line['SUPPLIER/CUSTOMER CODE'], $text);
            $sheet->setCellValueExplicit('F' . $row, (string) $line['WASTE/PRODUCT CODE'], $text);
            $row++;
        }
        $totals = array_values($layout['totals']);
        $totals[0] = 'TOTAL';
        $totalRow = $row;
        $sheet->fromArray($totals, null, 'A' . $totalRow, true);

        // Section A: per-waste summary below the table (A:D)
        $summaryHeaderRow = $totalRow + 2;
        $summaryRow = $summaryHeaderRow;
        $sheet->fromArray(array('SUMMARY DETAILS', 'TRIPS', 'TONNAGE (MT)', 'PRICE (RM)'), null, 'A' . $summaryRow, true);
        $summaryRow++;
        foreach ($layout['summary'] as $item) {
            $sheet->fromArray(array('SUBTOTAL (' . $item['name'] . ')', $item['trips'], $item['tonnage'], $item['price']), null, 'A' . $summaryRow, true);
            $summaryRow++;
        }
        $sheet->fromArray(array('GRAND TOTAL', $layout['summaryTotal']['trips'], $layout['summaryTotal']['tonnage'], $layout['summaryTotal']['price']), null, 'A' . $summaryRow, true);
        $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('C' . $summaryHeaderRow . ':D' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $summaryHeaderRow . ':D' . $summaryHeaderRow)->applyFromArray($green);

        // Section B: supplier group / waste code breakdown beside it (F:I)
        $breakdownRow = $summaryHeaderRow;
        $sheet->fromArray(array('SUPPLIER/CUSTOMER', 'WASTE/PRODUCT CODE', 'TRIPS', 'TONNAGE (MT)'), null, 'F' . $breakdownRow, true);
        $breakdownRow++;
        foreach ($layout['breakdown'] as $item) {
            $sheet->setCellValueExplicit('F' . $breakdownRow, (string) $item['supplier'], $text);
            $sheet->setCellValueExplicit('G' . $breakdownRow, (string) $item['waste'], $text);
            $sheet->fromArray(array($item['trips'], $item['tonnage']), null, 'H' . $breakdownRow, true);
            $breakdownRow++;
        }
        $sheet->fromArray(array('Total', '', $layout['summaryTotal']['trips'], $layout['summaryTotal']['tonnage']), null, 'F' . $breakdownRow, true);
        $sheet->getStyle('F' . $breakdownRow . ':I' . $breakdownRow)->getFont()->setBold(true);
        $sheet->getStyle('I' . $summaryHeaderRow . ':I' . $breakdownRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . $summaryHeaderRow . ':I' . $summaryHeaderRow)->applyFromArray($green);

        // Styling: green header, borders, number format for the weight and money columns
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($green + array(
            'alignment' => array('horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true),
        ));
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $totalRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('M' . ($headerRow + 1) . ':V' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $totalRow . ':' . $lastColumn . $totalRow)->getFont()->setBold(true);
        for ($column = 1; $column <= count($layout['headers']); $column++) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Weight-data_' . date('Y-m-d') . '.xlsx"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Printable HTML (A4 landscape) of the layout
     */
    public function html($layout, $fromLabel, $toLabel, $statusLabel) {
        $cell = function ($value) {
            return is_float($value) ? '<td style="text-align: right;">' . number_format($value, 2) . '</td>' : '<td>' . htmlspecialchars((string) $value) . '</td>';
        };
        $right = function ($value) {
            return '<td style="text-align: right;">' . number_format($value, 2) . '</td>';
        };

        $message = '<html>
                    <head>
                        <style>
                            @media print {
                                @page {
                                    size: A4 landscape;
                                    margin: 0.3in;
                                }
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            th, td {
                                border: 1px solid #000000;
                                font-family: sans-serif;
                                font-size: 8px;
                                padding: 3px;
                            }
                            th {
                                background-color: #00B050;
                                color: #ffffff;
                                text-align: center;
                            }
                            .title {
                                font-family: sans-serif;
                                font-size: 11px;
                                font-weight: bold;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="title">FROM DATE: ' . htmlspecialchars($fromLabel) . '   TO DATE: ' . htmlspecialchars($toLabel) . '</div>
                        <div class="title">TRANSACTION STATUS : ' . htmlspecialchars($statusLabel) . '</div>
                        <br>
                        <table>
                            <thead>
                                <tr>';

        foreach ($layout['headers'] as $header) {
            $message .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $message .= '</tr>
                            </thead>
                            <tbody>';

        foreach ($layout['rows'] as $line) {
            $message .= '<tr>';
            foreach ($line as $value) {
                $message .= $cell($value);
            }
            $message .= '</tr>';
        }

        $message .= '<tr style="font-weight: bold;">';
        foreach ($layout['totals'] as $value) {
            $message .= $cell($value);
        }
        $message .= '</tr>
                            </tbody>
                        </table>
                        <br>
                        <table style="width: 40%; display: inline-table; vertical-align: top;">
                            <thead>
                                <tr><th>SUMMARY DETAILS</th><th>TRIPS</th><th>TONNAGE (MT)</th><th>PRICE (RM)</th></tr>
                            </thead>
                            <tbody>';

        foreach ($layout['summary'] as $item) {
            $message .= '<tr><td>' . htmlspecialchars('SUBTOTAL (' . $item['name'] . ')') . '</td><td>' . $item['trips'] . '</td>' . $right($item['tonnage']) . $right($item['price']) . '</tr>';
        }
        $message .= '<tr style="font-weight: bold;"><td>GRAND TOTAL</td><td>' . $layout['summaryTotal']['trips'] . '</td>' . $right($layout['summaryTotal']['tonnage']) . $right($layout['summaryTotal']['price']) . '</tr>
                            </tbody>
                        </table>
                        <table style="width: 35%; margin-left: 5%; display: inline-table; vertical-align: top;">
                            <thead>
                                <tr><th>SUPPLIER/CUSTOMER</th><th>WASTE/PRODUCT CODE</th><th>TRIPS</th><th>TONNAGE (MT)</th></tr>
                            </thead>
                            <tbody>';

        foreach ($layout['breakdown'] as $item) {
            $message .= '<tr><td>' . htmlspecialchars($item['supplier']) . '</td><td>' . htmlspecialchars($item['waste']) . '</td><td>' . $item['trips'] . '</td>' . $right($item['tonnage']) . '</tr>';
        }
        $message .= '<tr style="font-weight: bold;"><td>Total</td><td></td><td>' . $layout['summaryTotal']['trips'] . '</td>' . $right($layout['summaryTotal']['tonnage']) . '</tr>
                            </tbody>
                        </table>
                    </body>
                </html>';

        return $message;
    }
}
?>
