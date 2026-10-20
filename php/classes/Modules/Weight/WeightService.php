<?php
namespace App\Modules\Weight;

use App\Core\BaseService;

class WeightService extends BaseService
{
    private const EXCEL_FIELDS = ['TRANSACTION ID', 'TRANSACTION STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME',
        'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE',
        'DELIVERED BY', 'PO NO.', 'DO NO.', 'CONTAINER NO', 'SEAL NO', 'CONTAINER NO 2', 'SEAL NO 2', 'ORDER WEIGHT', 'SUPPLIER WEIGHT', 'GROSS WEIGHT', 'TARE WEIGHT', 'NET WEIGHT', 'IN TIME', 'OUT TIME',
        'GROSS WEIGHT 2', 'TARE WEIGHT 2', 'NET WEIGHT 2', 'IN TIME2', 'OUT TIME2', 'REDUCE WEIGHT', 'VARIANCE', 'SUB TOTAL WEIGHT', 'MANUAL', 'CANCELLED', 'PLANT CODE',
        'PLANT NAME', 'WEIGHTED BY', 'REMARK'];

    /**
     * Excel (tab-separated .xls) of weight records and container records
     */
    public function excelContent(array $input, ?array $plants): string
    {
        [$clauses, $types, $params] = $this->filters($input, $plants);
        $where = implode(' AND ', array_merge(["status = '0'"], $clauses));

        $rows = $this->fetchAll(
            "SELECT * FROM Weight WHERE $where UNION ALL SELECT * FROM Weight_Container WHERE $where ORDER BY created_date ASC",
            $types . $types,
            array_merge($params, $params)
        );

        $content = implode("\t", self::EXCEL_FIELDS) . "\n";
        if ($rows === []) {
            return $content . "No records found...\n";
        }

        foreach ($rows as $row) {
            $cells = array_map([$this, 'escapeExcelCell'], $this->excelLine($row));
            $content .= implode("\t", $cells) . "\n";
        }

        return $content;
    }

    /**
     * Printable HTML of weight records grouped by product, with subtotals
     */
    public function pdfHtml(array $input, ?array $plants): string
    {
        [$clauses, $types, $params] = $this->filters($input, $plants);
        $where = implode(' AND ', array_merge(["status = '0'"], $clauses));
        $rows = $this->fetchAll("SELECT * FROM Weight WHERE $where ORDER BY tare_weight1_date", $types, $params);

        // The header columns depend on the status filter value; kept as the legacy PDF did
        $statusFilter = trim((string)($input['status'] ?? ''));
        $isSalesColumns = $statusFilter === 'Sales' || $statusFilter === 'Misc';

        $grouped = [];
        foreach ($rows as $row) {
            $productName = ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc' ? $row['product_name'] : $row['raw_mat_name']);
            $grouped[$productName][] = $row;
        }

        $html = '<html><head><style>' . self::PDF_STYLE . '</style></head><body>
            <table style="width:100%;">
                <thead>
                    <tr style="font-size: 9px; text-align: center;">
                        <th>TRANSACTION <br>ID</th>
                        <th>TRANSACTION <br>DATE</th>
                        <th>TRANSACTION <br>STATUS</th>
                        <th>LORRY <br>NO.</th>'
            . ($isSalesColumns ? '<th>CUSTOMER <br>CODE</th><th>CUSTOMER</th>' : '<th>SUPPLIER <br>CODE</th><th>SUPPLIER</th>')
            . '<th>' . ($isSalesColumns ? 'PRODUCT <br>CODE' : 'RAW MAT <br>CODE') . '</th>
                        <th>' . ($isSalesColumns ? 'PRODUCT' : 'RAW MAT') . '</th>
                        <th>DESTINATION <br>CODE</th>
                        <th>DESTINATION</th>
                        <th>PO NO.</th>
                        <th>DO NO.</th>
                        <th>CONTAINER <br>NO.</th>
                        <th>SEAL NO.</th>
                        <th>CONTAINER <br>NO. 2</th>
                        <th>SEAL NO. 2</th>
                        <th>ORDER WEIGHT</th>
                        <th>SUPPLIER WEIGHT</th>
                        <th>INCOMING <br>(MT)</th>
                        <th>OUTGOING <br>(MT)</th>
                        <th>NET <br>(MT)</th>
                        <th>IN TIME</th>
                        <th>OUT TIME</th>
                        <th>INCOMING 2 <br>(MT)</th>
                        <th>OUTGOING 2 <br>(MT)</th>
                        <th>NET 2 <br>(MT)</th>
                        <th>IN TIME 2</th>
                        <th>OUT TIME 2</th>
                        <th>VARIANCE</th>
                        <th>SUB TOTAL WEIGHT</th>
                        <th>USER</th>
                    </tr>
                </thead>
                <tbody>';

        $grandGross = 0;
        $grandTare = 0;
        $grandNet = 0;

        foreach ($grouped as $product => $productRows) {
            $html .= '<tr><td colspan="14" style="font-size: 9px;">. </td></tr><tr><td colspan="14" style="font-size: 9px;">. </td></tr>';

            $totalGross = 0;
            $totalTare = 0;
            $totalNet = 0;

            foreach ($productRows as $row) {
                $isSalesRow = $row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc';
                $statusLabel = $this->statusLabel($row['transaction_status']);

                $html .= '<tr style="font-size: 9px; text-align: center;">
                    <td>' . $row['transaction_id'] . '</td>
                    <td>' . (new \DateTime($row['transaction_date']))->format('d/m/Y') . '</td>
                    <td>' . $statusLabel . '</td>
                    <td>' . $row['lorry_plate_no1'] . '</td>'
                    . ($isSalesColumns
                        ? '<td>' . $row['customer_code'] . '</td><td>' . $row['customer_name'] . '</td>'
                        : '<td>' . $row['supplier_code'] . '</td><td>' . $row['supplier_name'] . '</td>')
                    . '<td>' . ($isSalesRow ? $row['product_code'] : $row['raw_mat_code']) . '</td>
                    <td>' . ($isSalesRow ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                    <td>' . $row['destination_code'] . '</td>
                    <td>' . $row['destination'] . '</td>
                    <td>' . $row['purchase_order'] . '</td>
                    <td>' . $row['delivery_no'] . '</td>
                    <td>' . $row['container_no'] . '</td>
                    <td>' . $row['seal_no'] . '</td>
                    <td>' . $this->toMt($row['order_weight']) . '</td>
                    <td>' . $this->toMt($row['supplier_weight']) . '</td>
                    <td>' . number_format($row['gross_weight1'] / 1000, 2) . '</td>
                    <td>' . number_format($row['tare_weight1'] / 1000, 2) . '</td>
                    <td>' . number_format($row['nett_weight1'] / 1000, 2) . '</td>
                    <td>' . (new \DateTime($row['gross_weight1_date']))->format('H:i') . '</td>
                    <td>' . (new \DateTime($row['tare_weight1_date']))->format('H:i') . '</td>
                    <td>' . $this->toMt($row['gross_weight2']) . '</td>
                    <td>' . $this->toMt($row['tare_weight2']) . '</td>
                    <td>' . $this->toMt($row['nett_weight2']) . '</td>
                    <td>' . (new \DateTime($row['gross_weight2_date']))->format('H:i') . '</td>
                    <td>' . (new \DateTime($row['tare_weight2_date']))->format('H:i') . '</td>
                    <td>' . $this->toMt($row['weight_different']) . '</td>
                    <td>' . number_format($row['final_weight'] / 1000, 2) . '</td>
                    <td>' . $row['created_by'] . '</td>
                </tr>';

                $totalGross += (float)$row['gross_weight1'];
                $totalTare += (float)$row['tare_weight1'];
                $totalNet += (float)$row['nett_weight1'];
            }

            $html .= '<tr>
                <th style="font-size: 10px;" colspan="18">Subtotal (' . $product . ')</th>
                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalGross / 1000, 2) . '</th>
                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalTare / 1000, 2) . '</th>
                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalNet / 1000, 2) . '</th>
            </tr>';

            $grandGross += $totalGross;
            $grandTare += $totalTare;
            $grandNet += $totalNet;
        }

        $html .= '</tbody>
                <tfoot>
                    <tr>
                        <th style="font-size: 10px;" colspan="18">Grand Total</th>
                        <th style="border:1px solid black;font-size: 9px;">' . number_format($grandGross / 1000, 2) . '</th>
                        <th style="border:1px solid black;font-size: 9px;">' . number_format($grandTare / 1000, 2) . '</th>
                        <th style="border:1px solid black;font-size: 9px;">' . number_format($grandNet / 1000, 2) . '</th>
                    </tr>
                </tfoot>
            </table>
        </body></html>';

        return $html;
    }

    /**
     * Filter SQL shared by both exports. Plant restriction always applies.
     * Returns [clauses, types, params] without the status = '0' condition.
     */
    private function filters(array $input, ?array $plants): array
    {
        $clauses = [];
        $types = '';
        $params = [];

        if ($plants !== null) {
            $clauses[] = $plants === [] ? '1=0' : 'plant_code IN (' . $this->placeholders(count($plants)) . ')';
            array_push($params, ...$plants);
            $types .= str_repeat('s', count($plants));
        }

        // Selected rows: only these IDs, with the plant restriction still applied
        if (($input['isMulti'] ?? '') === 'Y') {
            $rawIds = is_array($input['ids'] ?? null) ? $input['ids'] : explode(',', (string)($input['ids'] ?? ''));
            $ids = $this->cleanIds($rawIds);
            $clauses[] = $ids === [] ? '1=0' : 'id IN (' . $this->placeholders(count($ids)) . ')';
            array_push($params, ...$ids);
            $types .= str_repeat('i', count($ids));

            return [$clauses, $types, $params];
        }

        $value = fn (string $key): string => trim((string)($input[$key] ?? ''));
        $set = fn (string $key): bool => !in_array($value($key), ['', '-']);
        $add = function (string $sql, string $type, array $values) use (&$clauses, &$types, &$params) {
            $clauses[] = $sql;
            $types .= $type;
            array_push($params, ...$values);
        };

        foreach (['fromDate' => ['>=', '00:00:00'], 'toDate' => ['<=', '23:59:59']] as $key => [$operator, $time]) {
            if ($set($key)) {
                $date = \DateTime::createFromFormat('d-m-Y', $value($key));
                if ($date) {
                    $add("transaction_date $operator ?", 's', [$date->format('Y-m-d') . ' ' . $time]);
                }
            }
        }

        $equals = [
            'transactionStatus' => 'transaction_status',
            'customer' => 'customer_code',
            'supplier' => 'supplier_code',
            'vehicle' => 'lorry_plate_no1',
            'product' => 'product_code',
            'rawMat' => 'raw_mat_code',
            'destination' => 'destination',
            'plant' => 'plant_code'
        ];
        foreach ($equals as $key => $column) {
            if ($set($key)) {
                $add("$column = ?", 's', [$value($key)]);
            }
        }

        if ($set('weighingType')) {
            $add("weight_type LIKE ?", 's', ['%' . $value('weighingType') . '%']);
        }

        if ($set('customerType')) {
            $add("customer_type LIKE ?", 's', ['%' . $value('customerType') . '%']);
        }

        if ($set('status')) {
            if ($value('status') === 'Complete') {
                $clauses[] = "is_complete = 'Y'";
            } elseif ($value('status') === 'Cancelled') {
                $clauses[] = "is_cancel = 'Y'";
            } elseif ($value('status') === 'Pending') {
                $clauses[] = "is_complete = 'N' AND is_cancel = 'N'";
            } else {
                $clauses[] = "is_complete = 'Y'";
            }
        }

        return [$clauses, $types, $params];
    }

    /**
     * One Excel line: the same cells, in the same order, as the legacy export
     */
    private function excelLine(array $row): array
    {
        $usesRawMaterial = !in_array($row['transaction_status'], ['Sales', 'Misc']);
        $productCode = $usesRawMaterial ? $row['raw_mat_code'] : $row['product_code'];
        $productName = $usesRawMaterial ? $row['raw_mat_name'] : $row['product_name'];

        $weightTypes = ['Container' => 'Primer Mover', 'Empty Container' => 'Primer Mover + Container', 'Different Container' => 'Primer Mover + Different Bins'];

        return [$row['transaction_id'], $this->statusLabel($row['transaction_status']), $weightTypes[$row['weight_type']] ?? $row['weight_type'], $row['transaction_date'],
            $row['lorry_plate_no1'], $row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], $productCode, $productName,
            $row['product_description'], $row['destination_code'], $row['destination'], $row['transporter_code'], $row['transporter'], $row['purchase_order'],
            $row['delivery_no'], $row['container_no'], $row['seal_no'], $row['container_no2'], $row['seal_no2'], $row['order_weight'], $row['supplier_weight'],
            $row['gross_weight1'], $row['tare_weight1'], $row['nett_weight1'], $row['gross_weight1_date'], $row['tare_weight1_date'], $row['gross_weight2'],
            $row['tare_weight2'], $row['nett_weight2'], $row['gross_weight2_date'], $row['tare_weight2_date'], $row['reduce_weight'], $row['weight_different'],
            $row['final_weight'], $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks']];
    }

    /**
     * Tab and quote escaping for one Excel cell
     */
    private function escapeExcelCell($value): string
    {
        $str = preg_replace("/\t/", "\\t", (string)$value);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) {
            $str = '"' . str_replace('"', '""', $str) . '"';
        }

        return $str;
    }

    private function statusLabel(string $transactionStatus): string
    {
        $labels = ['Sales' => 'Dispatch', 'Purchase' => 'Receiving', 'Misc' => 'Miscellaneous'];

        return $labels[$transactionStatus] ?? 'Internal Transfer';
    }

    /**
     * Kilograms to tonnes with 2 decimals; blank when the value is empty or zero
     */
    private function toMt($kg): string
    {
        return empty($kg) ? '' : number_format($kg / 1000, 2);
    }

    private const PDF_STYLE = '
        @media print { @page { margin-left: 0.5in; margin-right: 0.5in; margin-top: 0.1in; margin-bottom: 0.1in; } }
        table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.70rem; vertical-align: top; border-top: 1px solid #dee2e6; }
        .table-bordered { border: 1px solid #000000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000000; font-family: sans-serif; font-size: 12px; }
        .row { display: flex; flex-wrap: wrap; margin-top: 20px; margin-right: -15px; margin-left: -15px; }
        .col-md-4 { position: relative; width: 33.333333%; }';
}
