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

    // Record columns => save input keys, in the legacy statement order. Used by every Weight and Weight_Container write.
    private const SAVE_COLUMNS = [
        'transaction_id' => 'transactionId', 'transaction_status' => 'transactionStatus', 'weight_type' => 'weightType', 'customer_type' => 'customerType',
        'transaction_date' => 'transactionDate', 'lorry_plate_no1' => 'vehiclePlateNo1', 'lorry_plate_no2' => 'vehiclePlateNo2', 'supplier_weight' => 'supplierWeight',
        'order_weight' => 'orderWeight', 'customer_code' => 'customerCode', 'customer_name' => 'customerName', 'supplier_code' => 'supplierCode',
        'supplier_name' => 'supplierName', 'product_code' => 'productCode', 'product_name' => 'productName', 'raw_mat_code' => 'rawMaterialCode',
        'raw_mat_name' => 'rawMaterialName', 'container_no' => 'containerNo', 'seal_no' => 'sealNo', 'container_no2' => 'containerNo2', 'seal_no2' => 'sealNo2',
        'invoice_no' => 'invoiceNo', 'purchase_order' => 'purchaseOrder', 'delivery_no' => 'deliveryNo', 'transporter_code' => 'transporterCode',
        'transporter' => 'transporter', 'destination_code' => 'destinationCode', 'destination' => 'destination', 'remarks' => 'otherRemarks',
        'gross_weight1' => 'grossIncoming', 'gross_weight1_date' => 'grossIncomingDate', 'gross_weight_by1' => 'grossWeightBy1', 'tare_weight1' => 'tareOutgoing',
        'tare_weight1_date' => 'tareOutgoingDate', 'tare_weight_by1' => 'tareWeightBy1', 'nett_weight1' => 'nettWeight',
        'gross_weight2' => 'grossIncoming2', 'gross_weight2_date' => 'grossIncomingDate2', 'gross_weight_by2' => 'grossWeightBy2', 'tare_weight2' => 'tareOutgoing2',
        'tare_weight2_date' => 'tareOutgoingDate2', 'tare_weight_by2' => 'tareWeightBy2', 'nett_weight2' => 'nettWeight2', 'reduce_weight' => 'reduceWeight',
        'final_weight' => 'finalWeight', 'weight_different' => 'weightDifference', 'weight_different_perc' => 'weightDifferencePerc', 'is_complete' => 'isComplete',
        'is_cancel' => 'isCancel', 'manual_weight' => 'manualWeight', 'indicator_id' => 'indicatorId', 'weighbridge_id' => 'weighbridge', 'created_by' => 'username',
        'modified_by' => 'username', 'indicator_id_2' => 'indicatorId2', 'product_description' => 'productDescription', 'unit_price' => 'unitPrice',
        'sub_total' => 'subTotalPrice', 'sst' => 'sstPrice', 'total_price' => 'totalPrice', 'is_approved' => 'isApproved', 'plant_code' => 'plantCode', 'plant_name' => 'plant'
    ];

    // Extra columns written after nett_weight1 for Primer Mover + Different Bins
    private const DIFFERENT_BIN_COLUMNS = ['lorry_no2_weight' => 'vehicleWeight2', 'empty_container2_weight' => 'emptyContainerWeight2', 'replacement_container' => 'replacementContainer'];

    // Plant column holding the running transaction number of each status
    private const COUNTER_COLUMNS = ['Purchase' => 'purchase', 'Local' => 'locals', 'Misc' => 'misc'];

    /**
     * Save a weighing (first or second weighing, create or edit). Ported from the legacy php/weight.php: the same branches,
     * statements, column values and messages. The whole save now runs in one transaction.
     */
    public function save(array $input): array
    {
        if (!isset($input['transactionId'], $input['transactionStatus'], $input['weightType'], $input['transactionDate'], $input['grossIncoming'],
            $input['grossIncomingDate'], $input['manualWeight'], $input['plantCode'], $input['plant'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $v = $this->saveInput($input);

        if ($v['weightType'] == 'Empty Container' && (int)$v['grossIncoming'] < (int)$v['tareOutgoing']) {
            return ['status' => 'failed', 'message' => 'Incoming Weight cannot greater than outgoing weight'];
        }

        $this->db->begin_transaction();
        try {
            if (empty($input['transactionId'])) {
                [$v['transactionId'], $v['misValue'], $v['diffContainerTransId']] = $this->nextTransactionId((string)$input['transactionStatus'], $v['plantCode'], $v['weightType']);
            }

            if ($v['weightType'] == 'Empty Container') {
                $result = $this->saveEmptyContainer($v);
            } elseif ($v['weightType'] == 'Different Container') {
                $result = !empty($v['weightId']) ? $this->updateDifferentBin($v) : $this->insertDifferentBin($v);
            } else {
                $result = !empty($v['weightId']) ? $this->updateWeight($v) : $this->insertWeight($v);
            }

            $this->db->commit();

            return $result;
        } catch (SaveFailed $e) {
            $this->db->rollback();

            return ['status' => 'failed', 'message' => $e->getMessage()];
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Form values with the legacy defaults: empty values become null (or the field's default), others are trimmed
     */
    private function saveInput(array $input): array
    {
        $defaults = [
            'id' => null, 'plantCode' => null, 'plant' => null, 'weightType' => 'Normal', 'transactionStatus' => null, 'unitPrice' => null,
            'subTotalPrice' => '0.00', 'sstPrice' => '0.00', 'totalPrice' => null, 'customerType' => 'Normal', 'poSupplyWeight' => null, 'supplierWeight' => null,
            'orderWeight' => null, 'grossIncoming' => 0, 'grossWeightBy1' => 0, 'tareOutgoing' => 0, 'tareWeightBy1' => 0, 'nettWeight' => 0, 'manualWeight' => null,
            'weighbridge' => 'Weigh1', 'indicatorId' => null, 'invoiceNo' => null, 'deliveryNo' => null, 'purchaseOrder' => null, 'containerNo' => null,
            'sealNo' => null, 'containerNo2' => null, 'sealNo2' => null, 'customerName' => null, 'productName' => null, 'rawMaterialName' => null,
            'transporter' => null, 'weightDifference' => null, 'weightDifferencePerc' => null, 'destination' => null, 'reduceWeight' => '0',
            'otherRemarks' => null, 'vehiclePlateNo2' => null, 'vehicleWeight2' => null, 'grossIncoming2' => null, 'emptyContainerWeight2' => 0,
            'replacementContainer' => 0, 'grossWeightBy2' => 0, 'tareOutgoing2' => null, 'tareWeightBy2' => 0, 'nettWeight2' => null, 'customerCode' => null,
            'supplierCode' => null, 'supplierName' => null, 'productCode' => null, 'rawMaterialCode' => null, 'destinationCode' => null, 'transporterCode' => null,
            'finalWeight' => '0', 'indicatorId2' => null, 'productDescription' => null, 'vehiclePlateNo1' => null
        ];

        $v = [];
        foreach ($defaults as $key => $default) {
            $v[$key] = empty($input[$key]) ? $default : trim($input[$key]);
        }
        $v['weightId'] = $v['id'];
        $v['username'] = $this->username;
        $v['transactionId'] = empty($input['transactionId']) ? null : trim($input['transactionId']);
        $v['transactionDate'] = empty($input['transactionDate']) ? null : \DateTime::createFromFormat('d-m-Y', $input['transactionDate'])->format('Y-m-d H:i:s');

        // Weighing dates are saved as posted (not trimmed)
        foreach (['grossIncomingDate', 'tareOutgoingDate', 'grossIncomingDate2', 'tareOutgoingDate2'] as $key) {
            $v[$key] = empty($input[$key]) ? null : $input[$key];
        }

        // A typed-in plate (vehicle not in the master list) replaces the selected one
        if (array_key_exists('manualVehicle', $input)) {
            $v['vehiclePlateNo1'] = trim($input['vehicleNoTxt'] ?? '');
        }
        if (array_key_exists('manualVehicle2', $input)) {
            $v['vehiclePlateNo2'] = trim($input['vehicleNoTxt2'] ?? '');
        }

        // Complete once every weighing of the type is captured; a pending (unapproved) save is never complete
        $v['isCancel'] = 'N';
        $v['isApproved'] = 'Y';
        if (($v['weightType'] == 'Normal' || $v['weightType'] == 'Empty Container') && ($v['grossIncoming'] != null && $v['tareOutgoing'] != null)) {
            $v['isComplete'] = 'Y';
        } elseif (($v['weightType'] == 'Container' || $v['weightType'] == 'Different Container')
            && ($v['grossIncoming'] != null && $v['tareOutgoing'] != null && $v['grossIncoming2'] != null && $v['tareOutgoing2'] != null)) {
            $v['isComplete'] = 'Y';
        } else {
            $v['isComplete'] = 'N';
        }
        if (($input['status'] ?? '') == 'pending') {
            $v['isComplete'] = 'N';
            $v['isApproved'] = 'N';
        }

        $v['misValue'] = '';
        $v['diffContainerTransId'] = null;

        return $v;
    }

    /**
     * New transaction ID: {plant}/{prefix}/{yymm}-{4 digit running no}, with C/ before the prefix for Container weighing.
     * Returns [transaction ID, current running no, ID prefix for a Different Container bin record].
     */
    private function nextTransactionId(string $status, ?string $plantCode, string $weightType): array
    {
        $transactionId = $plantCode . '/';
        $diffContainerTransId = null;
        $today = date('ym');

        if ($row = $this->fetchOne("SELECT * FROM status WHERE status=?", 's', [$status])) {
            if ($weightType == 'Container') {
                $transactionId .= 'C/' . $row['prefix'] . '/' . $today . '-';
            } else {
                $transactionId .= $row['prefix'] . '/' . $today . '-';
                $diffContainerTransId = $transactionId;
            }
        }

        $misValue = '';
        $column = self::COUNTER_COLUMNS[$status] ?? 'sales';
        if ($row = $this->fetchOne("SELECT $column as curcount FROM Plant WHERE plant_code=?", 's', [(string)$plantCode])) {
            $misValue = $row['curcount'];
            $transactionId .= str_repeat('0', max(0, 4 - strlen((string)$misValue))) . $misValue;
        }

        return [$transactionId, $misValue, $diffContainerTransId];
    }

    /**
     * Store the plant's running number for the status (the caller has already incremented it)
     */
    private function saveCounter(array $v): void
    {
        $column = self::COUNTER_COLUMNS[$v['transactionStatus']] ?? 'sales';
        $this->saveStatement("UPDATE Plant SET $column=? WHERE plant_code=?", [$v['misValue'], (string)$v['plantCode']]);
    }

    /**
     * Empty container weighing (Weight_Container). An edit whose record is missing is inserted with the posted ID.
     */
    private function saveEmptyContainer(array $v): array
    {
        $values = $this->recordValues($v, self::SAVE_COLUMNS);

        if (!empty($v['weightId'])) {
            if ($this->fetchOne("SELECT id FROM Weight_Container WHERE id = ?", 's', [$v['weightId']])) {
                $this->saveUpdate('Weight_Container', $values, $v['weightId']);

                return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $v['weightId']];
            }

            $this->saveInsert('Weight_Container', ['id' => $v['weightId']] + $values);

            return ['status' => 'success', 'message' => 'Inserted Successfully!!', 'id' => $v['weightId']];
        }

        $id = $this->saveInsert('Weight_Container', $values);
        $v['misValue']++;
        $this->saveCounter($v);

        return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
    }

    /**
     * Normal / Container weighing edit. A Container weighing marks its empty container as used (is_cancel = is_complete).
     */
    private function updateWeight(array $v): array
    {
        $this->saveUpdate('Weight', $this->recordValues($v, self::SAVE_COLUMNS), $v['weightId']);

        if (!empty($v['containerNo']) && $v['weightType'] == 'Container') {
            $this->releaseContainer($v);
        }

        return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $v['weightId']];
    }

    /**
     * New Normal / Container weighing
     */
    private function insertWeight(array $v): array
    {
        $id = $this->saveInsert('Weight', $this->recordValues($v, self::SAVE_COLUMNS));
        $v['misValue']++;
        $this->saveCounter($v);

        if (!empty($v['containerNo']) && $v['weightType'] == 'Container') {
            $this->releaseContainer($v);
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
    }

    /**
     * Primer Mover + Different Bins edit. When complete, the replacement bin is written to Weight_Container: its existing
     * empty container record is updated, or a new record is added under the next transaction number.
     */
    private function updateDifferentBin(array $v): array
    {
        $this->saveUpdate('Weight', $this->recordValues($v, $this->differentBinColumns()), $v['weightId']);

        if (!empty($v['containerNo'])) {
            $this->releaseContainer($v);

            if ($v['isComplete'] == 'Y') {
                $existing = $this->fetchOne("SELECT * FROM Weight_Container WHERE container_no=? AND is_complete='Y' AND is_cancel='N' AND status='0'", 's', [$v['replacementContainer']]);

                if ($existing) {
                    // The legacy update wrote these three columns from shifted values; kept as it was
                    $columns = array_replace($this->replacementBinColumns(), [
                        'lorry_no2_weight' => 'replacementContainer', 'empty_container2_weight' => 'vehicleWeight2', 'replacement_container' => 'emptyContainerWeight2'
                    ]);
                    unset($columns['transaction_id']);
                    $this->saveUpdate('Weight_Container', $this->recordValues($v, $columns), $existing['id']);
                } else {
                    [$v['diffContainerTransId'], $misValue] = $this->binTransactionId($v);
                    if ($misValue !== null) {
                        $v['misValue'] = $misValue;
                    }
                    $this->saveInsert('Weight_Container', $this->recordValues($v, $this->replacementBinColumns()));
                    $v['misValue']++;
                    $this->saveCounter($v);
                }
            }
        }

        return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $v['weightId']];
    }

    /**
     * New Primer Mover + Different Bins weighing. When complete, the replacement bin is added to Weight_Container under the next transaction number.
     */
    private function insertDifferentBin(array $v): array
    {
        if (empty($v['grossIncomingDate'])) {
            $v['grossIncomingDate'] = $v['grossIncomingDate2'];
        }

        $id = $this->saveInsert('Weight', $this->recordValues($v, $this->differentBinColumns()));
        $v['misValue']++;
        $this->saveCounter($v);

        if ($v['isComplete'] == 'Y') {
            $v['diffContainerTransId'] = $v['diffContainerTransId'] . str_repeat('0', max(0, 4 - strlen((string)$v['misValue']))) . $v['misValue'];
            $this->saveInsert('Weight_Container', $this->recordValues($v, $this->replacementBinColumns()));
            $v['misValue']++;
            $this->saveCounter($v);
        }

        if (!empty($v['containerNo'])) {
            $this->releaseContainer($v);
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
    }

    /**
     * Transaction ID for a replacement bin record, read fresh from the status prefix and plant counter.
     * Returns [ID, running no or null when the plant has no counter].
     */
    private function binTransactionId(array $v): array
    {
        $transactionId = $v['plantCode'] . '/';
        if ($row = $this->fetchOne("SELECT * FROM status WHERE status=?", 's', [$v['transactionStatus']])) {
            $transactionId .= $row['prefix'] . '/' . date('ym') . '-';
        }

        $column = self::COUNTER_COLUMNS[$v['transactionStatus']] ?? 'sales';
        if ($row = $this->fetchOne("SELECT $column as curcount FROM Plant WHERE plant_code=?", 's', [(string)$v['plantCode']])) {
            return [$transactionId . str_repeat('0', max(0, 4 - strlen((string)$row['curcount']))) . $row['curcount'], $row['curcount']];
        }

        return [$transactionId, null];
    }

    /**
     * Mark the weighing's empty container as used once the weighing is complete (is_cancel follows is_complete)
     */
    private function releaseContainer(array $v): void
    {
        $this->saveStatement("UPDATE Weight_Container SET is_cancel=? WHERE container_no=? AND status='0'", [$v['isComplete'], $v['containerNo']]);
    }

    /**
     * Weight columns for Different Container: the standard ones plus the bin columns after nett_weight1
     */
    private function differentBinColumns(): array
    {
        $columns = [];
        foreach (self::SAVE_COLUMNS as $column => $key) {
            $columns[$column] = $key;
            if ($column === 'nett_weight1') {
                $columns += self::DIFFERENT_BIN_COLUMNS;
            }
        }

        return $columns;
    }

    /**
     * Weight_Container columns for the replacement bin: second vehicle, the new bin and its weighing as the first weighing
     */
    private function replacementBinColumns(): array
    {
        return array_replace($this->differentBinColumns(), [
            'transaction_id' => 'diffContainerTransId', 'lorry_plate_no1' => 'vehiclePlateNo2', 'container_no' => 'replacementContainer',
            'gross_weight1' => 'grossIncoming2', 'gross_weight1_date' => 'grossIncomingDate2', 'gross_weight_by1' => 'grossWeightBy2',
            'tare_weight1' => 'vehicleWeight2', 'tare_weight1_date' => 'grossIncomingDate2', 'tare_weight_by1' => 'grossWeightBy2', 'nett_weight1' => 'emptyContainerWeight2'
        ]);
    }

    /**
     * column => value for a write, from column => save input key
     */
    private function recordValues(array $v, array $columns): array
    {
        $values = [];
        foreach ($columns as $column => $key) {
            $values[$column] = $v[$key];
        }

        return $values;
    }

    private function saveInsert(string $table, array $values): int
    {
        $this->saveStatement("INSERT INTO $table (" . implode(', ', array_keys($values)) . ") VALUES (" . $this->placeholders(count($values)) . ")", array_values($values));

        return (int)$this->db->insert_id;
    }

    private function saveUpdate(string $table, array $values, $id): void
    {
        $sets = implode(', ', array_map(fn ($column) => "$column=?", array_keys($values)));
        $this->saveStatement("UPDATE $table SET $sets WHERE id=?", array_merge(array_values($values), [$id]));
    }

    /**
     * Run one save statement with every value bound as a string, as the legacy save did. Throws SaveFailed with the statement error.
     */
    private function saveStatement(string $sql, array $params): void
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new SaveFailed('Something went wrong');
        }
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new SaveFailed($error);
        }
        $stmt->close();
    }

    /**
     * Weighing screen list (DataTables). batch = N (pending) lists Weight and Weight_Container together, anything else Weight only.
     * $plants is null when the user may see every plant. Ported as-is from the legacy filterWeight.php.
     */
    public function getList(array $input, ?array $plants, array $p): array
    {
        [$clauses, $types, $params] = $this->listClauses($input, $p['search']);
        $search = $clauses === [] ? '' : ' AND ' . implode(' AND ', $clauses);

        // An empty plant list matches nothing, as the legacy IN ('') did
        $plantList = $plants === null ? [] : ($plants === [] ? [''] : array_values($plants));
        $plantSql = $plants === null ? '' : ' AND plant_code IN (' . $this->placeholders(count($plantList)) . ')';
        $plantTypes = str_repeat('s', count($plantList));

        $order = $this->orderBy($p);
        $limit = ' LIMIT ' . max(0, $p['start']) . ',' . max(0, $p['length']);

        if (trim((string)($input['batch'] ?? '')) === 'N') {
            // Non-admin total counts Weight twice instead of Weight_Container, as the legacy list did
            $secondTable = $plants === null ? 'Weight_Container' : 'Weight';
            $totalRecords = $this->countUnion(
                "SELECT * FROM Weight WHERE status = '0'$plantSql UNION ALL SELECT * FROM $secondTable WHERE status = '0'$plantSql",
                $plantTypes . $plantTypes, array_merge($plantList, $plantList)
            );

            $unionTypes = $plantTypes . $types . $plantTypes . $types;
            $unionParams = array_merge($plantList, $params, $plantList, $params);
            $union = "(SELECT * FROM Weight WHERE status = '0'$plantSql$search) UNION ALL (SELECT * FROM Weight_Container WHERE status = '0'$plantSql$search)";

            $totalFiltered = $this->countUnion($union, $unionTypes, $unionParams);
            $rows = $this->fetchAll($union . $order . $limit, $unionTypes, $unionParams);
        } else {
            $totalRecords = $this->countRows('Weight', "status = '0'$plantSql", $plantTypes, $plantList);
            $totalFiltered = $this->countRows('Weight', "status = '0'$plantSql$search", $plantTypes . $types, array_merge($plantList, $params));
            $rows = $this->fetchAll("SELECT * FROM Weight WHERE status = '0'$plantSql$search" . $order . $limit, $plantTypes . $types, array_merge($plantList, $params));
        }

        $response = $this->listResponse($p['draw'], $totalRecords, $totalFiltered, $rows, true);
        $response['summary'] = $this->listSummary($input, $plants, $p['search']);

        return $response;
    }

    /**
     * Summary card counts for the table filters. The status and type filters are left out so every card keeps its numbers.
     */
    private function listSummary(array $input, ?array $plants, string $search): array
    {
        [$clauses, $types, $params] = $this->listClauses(array_diff_key($input, ['batch' => true, 'status' => true]), $search);

        if ($plants !== null) {
            $plantList = $plants === [] ? [''] : array_values($plants);
            $clauses[] = 'plant_code IN (' . $this->placeholders(count($plantList)) . ')';
            $types .= str_repeat('s', count($plantList));
            $params = array_merge($params, $plantList);
        }

        $where = implode(' AND ', array_merge(["status = '0'"], $clauses));
        $rows = $this->fetchAll("SELECT transaction_status, SUM(is_complete = 'N') AS Pending, SUM(is_complete = 'Y' AND is_cancel = 'N') AS Complete, SUM(is_cancel = 'Y') AS Cancelled FROM Weight WHERE $where GROUP BY transaction_status", $types, $params);
        // Pending also lists Weight_Container, as batch = N does in getList
        $containers = $this->fetchAll("SELECT transaction_status, COUNT(*) AS total FROM Weight_Container WHERE $where AND is_complete = 'N' GROUP BY transaction_status", $types, $params);

        $summary = [];
        foreach (['Sales', 'Purchase', 'Local', 'Misc'] as $type) {
            $summary[$type] = ['Pending' => 0, 'Complete' => 0, 'Cancelled' => 0];
        }
        $typeOf = fn (string $status): string => in_array($status, ['Sales', 'Purchase', 'Misc'], true) ? $status : 'Local';
        foreach ($rows as $row) {
            foreach (['Pending', 'Complete', 'Cancelled'] as $progress) {
                $summary[$typeOf($row['transaction_status'])][$progress] += (int)$row[$progress];
            }
        }
        foreach ($containers as $row) {
            $summary[$typeOf($row['transaction_status'])]['Pending'] += (int)$row['total'];
        }

        return $summary;
    }

    /**
     * Completed empty container list (DataTables). Ported as-is from the legacy filterEmptyContainer.php:
     * the search box replaces the other conditions, including is_complete / is_cancel.
     */
    public function getEmptyContainerList(array $input, array $p): array
    {
        $clauses = ["is_complete = 'Y'", "is_cancel = 'N'"];
        $types = '';
        $params = [];

        foreach (['fromDate' => ['>=', '00:00:00'], 'toDate' => ['<=', '23:59:59']] as $key => [$operator, $time]) {
            $date = $this->listDate($input[$key] ?? null);
            if ($date !== null) {
                $clauses[] = "transaction_date $operator ?";
                $types .= 's';
                $params[] = $date . ' ' . $time;
            }
        }

        if ($p['search'] !== '') {
            $like = '%' . $p['search'] . '%';
            $clauses = ["(transaction_id LIKE ? OR lorry_plate_no1 LIKE ? OR container_no LIKE ?)"];
            $types = 'sss';
            $params = [$like, $like, $like];
        }

        $where = "status = '0' AND " . implode(' AND ', $clauses);
        $totalRecords = $this->countRows('Weight_Container', "status = '0'", '', []);
        $totalFiltered = $this->countRows('Weight_Container', $where, $types, $params);
        $rows = $this->fetchAll("SELECT * FROM Weight_Container WHERE $where" . $this->orderBy($p) . ' LIMIT ' . max(0, $p['start']) . ',' . max(0, $p['length']), $types, $params);

        return $this->listResponse($p['draw'], $totalRecords, $totalFiltered, $rows, false);
    }

    /**
     * One weighing record. type Log + format EXPANDABLE reads Weight_Log; type Container reads Weight_Container; anything else Weight.
     * EXPANDABLE is the row detail view, MODAL (default) the edit form. Ported as-is from the legacy getWeight.php.
     */
    public function getWeight(string $id, string $format, string $type): array
    {
        if ($format === 'EXPANDABLE' && $type === 'Log') {
            $row = $this->fetchOne("SELECT * FROM Weight_Log WHERE id = ?", 's', [$id]);

            // Loose comparison on purpose: the legacy blanked 0 as well as null
            return ['status' => 'success', 'message' => $row === null ? [] : array_map(fn ($value) => $value == null ? '' : $value, $row)];
        }

        $isContainer = $type === 'Container';
        $rows = $this->fetchAll('SELECT * FROM ' . ($isContainer ? 'Weight_Container' : 'Weight') . ' WHERE id = ?', 's', [$id]);

        $message = [];
        foreach ($rows as $row) {
            if ($format === 'EXPANDABLE') {
                $message = $isContainer ? $this->containerDetail($row) : $this->weightDetail($row);
            } else {
                $message = $this->editFields($row, $isContainer);
            }
        }

        return ['status' => 'success', 'message' => $message];
    }

    /**
     * Cancel weighing records. Weight rows are marked complete + cancelled; empty container rows are soft deleted (status 1).
     * isMulti = Y takes comma separated IDs. Ported as-is from the legacy deleteWeight.php, including its messages.
     */
    public function cancel(array $input): array
    {
        if (!isset($input['cancelReason'], $input['isEmptyContainer'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $cancelReason = $this->sanitizeString($input['cancelReason']);
        $isEmptyContainer = $this->sanitizeString($input['isEmptyContainer']);
        $id = (isset($input['id']) && $input['id'] !== '') ? $input['id'] : null;
        $containerId = (isset($input['containerId']) && $input['containerId'] !== '') ? $input['containerId'] : null;

        if (($input['isMulti'] ?? '') !== 'Y') {
            $result = $isEmptyContainer === 'Y'
                ? $this->write("UPDATE Weight_Container SET status = ?, cancelled_reason = ? WHERE id = ?", 'sss', ['1', $cancelReason, $id])
                : $this->write("UPDATE Weight SET is_complete = ?, is_cancel = ?, cancelled_reason = ? WHERE id = ?", 'ssss', ['Y', 'Y', $cancelReason, $id]);

            return $result === true ? ['status' => 'success', 'message' => 'Deleted'] : ['status' => 'failed', 'message' => $result];
        }

        if ($isEmptyContainer === 'Y') {
            $result = $this->writeIds("UPDATE Weight_Container SET status = ?, cancelled_reason = ? WHERE id IN (%s)", ['1', $cancelReason], $id);

            return $result === true ? ['status' => 'success', 'message' => 'Deleted'] : ['status' => 'failed', 'message' => $result];
        }

        $messages = [];
        $failed = false;

        if ($id !== null) {
            $result = $this->writeIds("UPDATE Weight SET is_complete = ?, is_cancel = ?, cancelled_reason = ? WHERE id IN (%s)", ['Y', 'Y', $cancelReason], $id);
            $failed = $failed || $result !== true;
            $messages[] = $result === true ? 'Deleted' : $result;
        }

        if ($containerId !== null) {
            $result = $this->writeIds("UPDATE Weight_Container SET status = ?, cancelled_reason = ? WHERE id IN (%s)", ['1', $cancelReason], $containerId);
            $failed = $failed || $result !== true;
            $messages[] = $result === true ? 'Deleted' : $result;
        }

        return $failed ? ['status' => 'failed', 'message' => $messages] : ['status' => 'success', 'message' => 'Deleted'];
    }

    /**
     * Completed, not cancelled empty containers of one transaction status (id + container no)
     */
    public function getContainers(string $transactionStatus): array
    {
        $rows = $this->fetchAll("SELECT id, container_no FROM Weight_Container WHERE transaction_status = ? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'", 's', [$transactionStatus]);

        return ['status' => 'success', 'message' => $rows];
    }

    /**
     * First weighing of a completed empty container, used to prefill the second weighing. The last match wins, as the legacy did.
     */
    public function getEmptyContainer(string $containerNo): array
    {
        $rows = $this->fetchAll("SELECT * FROM Weight_Container WHERE container_no = ? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'", 's', [$containerNo]);

        $fields = ['transaction_status', 'customer_code', 'customer_name', 'supplier_code', 'supplier_name', 'invoice_no', 'delivery_no', 'purchase_order',
            'container_no', 'seal_no', 'container_no2', 'seal_no2', 'product_code', 'product_name', 'raw_mat_code', 'raw_mat_name', 'plant_code', 'plant_name',
            'transporter_code', 'transporter', 'destination_code', 'destination', 'gross_weight1', 'gross_weight1_date', 'gross_weight_by1', 'tare_weight1',
            'tare_weight1_date', 'tare_weight_by1', 'lorry_plate_no1', 'nett_weight1'];

        $message = [];
        foreach ($rows as $row) {
            foreach ($fields as $field) {
                $message[$field] = $row[$field];
            }
            $message['vehicleNoTxt'] = $this->vehicleNoTxt($row['lorry_plate_no1']);
        }

        return ['status' => 'success', 'message' => $message];
    }

    /**
     * Weighing list filters. The search box replaces every other filter, as the legacy list did.
     */
    private function listClauses(array $input, string $search): array
    {
        if ($search !== '') {
            $like = '%' . $search . '%';

            return [["(transaction_id LIKE ? OR lorry_plate_no1 LIKE ?)"], 'ss', [$like, $like]];
        }

        $clauses = [];
        $types = '';
        $params = [];
        $add = function (string $sql, array $values) use (&$clauses, &$types, &$params) {
            $clauses[] = $sql;
            $types .= str_repeat('s', count($values));
            array_push($params, ...$values);
        };

        foreach (['fromDate' => ['>=', '00:00:00'], 'toDate' => ['<=', '23:59:59']] as $key => [$operator, $time]) {
            $date = $this->listDate($input[$key] ?? null);
            if ($date !== null) {
                $add("transaction_date $operator ?", [$date . ' ' . $time]);
            }
        }

        $value = fn (string $key): string => (string)($input[$key] ?? '');
        $set = fn (string $key): bool => !in_array($value($key), ['', '-'], true);

        $equals = ['status' => 'transaction_status', 'customer' => 'customer_code', 'supplier' => 'supplier_code', 'invoice' => 'weight_type',
            'product' => 'product_code', 'rawMaterial' => 'raw_mat_code', 'plant' => 'plant_code'];
        foreach ($equals as $key => $column) {
            if ($set($key)) {
                $add("$column = ?", [$value($key)]);
            }
        }

        if ($set('vehicle')) {
            $add("lorry_plate_no1 LIKE ?", ['%' . $value('vehicle') . '%']);
        }

        if ($set('transactionId')) {
            $add("transaction_id LIKE ?", ['%' . $value('transactionId') . '%']);
        }

        // Status cards and status filter: N pending, Y complete, C cancelled
        if ($value('batch') === 'N') {
            $clauses[] = "is_complete = 'N'";
        } elseif ($value('batch') === 'Y') {
            $clauses[] = "is_complete = 'Y' AND is_cancel = 'N'";
        } elseif ($value('batch') === 'C') {
            $clauses[] = "is_cancel = 'Y'";
        }

        if ($set('containerNo')) {
            $like = '%' . $value('containerNo') . '%';
            $add("(container_no LIKE ? OR container_no2 LIKE ?)", [$like, $like]);
        }

        if ($set('sealNo')) {
            $like = '%' . $value('sealNo') . '%';
            $add("(seal_no LIKE ? OR seal_no2 LIKE ?)", [$like, $like]);
        }

        if ($set('invDelPo')) {
            $like = '%' . $value('invDelPo') . '%';
            $add("(purchase_order LIKE ? OR invoice_no LIKE ? OR delivery_no LIKE ?)", [$like, $like, $like]);
        }

        return [$clauses, $types, $params];
    }

    /**
     * d-m-Y list filter date as Y-m-d, or null when empty / invalid
     */
    private function listDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $date = \DateTime::createFromFormat('d-m-Y', (string)$value);

        return $date ? $date->format('Y-m-d') : null;
    }

    /**
     * ORDER BY from the DataTables column. Only plain column names are accepted; anything else sorts by id.
     */
    private function orderBy(array $p): string
    {
        $column = preg_match('/^\w+$/', $p['orderColumn']) ? $p['orderColumn'] : 'id';
        $dir = strtolower($p['orderDir']) === 'desc' ? 'DESC' : 'ASC';

        return " ORDER BY $column $dir";
    }

    private function countUnion(string $union, string $types, array $params): int
    {
        return (int)($this->fetchOne("SELECT COUNT(*) AS allcount FROM ($union) AS combined", $types, $params)['allcount'] ?? 0);
    }

    /**
     * DataTables response for one page of rows
     */
    private function listResponse(int $draw, int $totalRecords, int $totalFiltered, array $rows, bool $weightTypeLabels): array
    {
        $data = [];

        foreach ($rows as $row) {
            $data[] = $this->listRow($row, $weightTypeLabels);
        }

        return [
            'draw' => $draw,
            'iTotalRecords' => (string)$totalRecords,
            'iTotalDisplayRecords' => (string)$totalFiltered,
            'aaData' => $data
        ];
    }

    private function listRow(array $row, bool $weightTypeLabels): array
    {
        $weightTypes = ['Container' => 'Primer Mover', 'Empty Container' => 'Primer Mover + Container', 'Different Container' => 'Primer Mover + Different Bins'];
        $usesSupplier = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

        return [
            // The legacy list read rows with a plain query, so the id came back as a string
            'id' => (string)$row['id'],
            'transaction_id' => $row['transaction_id'],
            'transaction_status' => $this->statusLabel((string)$row['transaction_status']),
            'weight_type' => $weightTypeLabels ? ($weightTypes[$row['weight_type']] ?? $row['weight_type']) : $row['weight_type'],
            'transaction_date' => $row['transaction_date'],
            'lorry_plate_no1' => $row['lorry_plate_no1'],
            'lorry_plate_no2' => $row['lorry_plate_no2'],
            'supplier_weight' => $row['supplier_weight'],
            'customer_code' => $row['customer_code'],
            'customer_name' => $row['customer_name'],
            'plant_code' => $row['plant_code'],
            'plant_name' => $row['plant_name'],
            'supplier_code' => $row['supplier_code'],
            'supplier_name' => $row['supplier_name'],
            'customer' => $usesSupplier ? $row['supplier_name'] : $row['customer_name'],
            'product_code' => $usesSupplier ? $row['raw_mat_code'] : $row['product_code'],
            'product_name' => $usesSupplier ? $row['raw_mat_name'] : $row['product_name'],
            'container_no' => $row['container_no'],
            'seal_no' => $row['seal_no'],
            'invoice_no' => $row['invoice_no'],
            'purchase_order' => $row['purchase_order'],
            'delivery_no' => $row['delivery_no'],
            'transporter_code' => $row['transporter_code'],
            'transporter' => $row['transporter'],
            'destination_code' => $row['destination_code'],
            'destination' => $row['destination'],
            'remarks' => $row['remarks'],
            'gross_weight1' => $row['gross_weight1'],
            'gross_weight1_date' => $row['gross_weight1_date'],
            'tare_weight1' => $row['tare_weight1'],
            'tare_weight1_date' => $row['tare_weight1_date'],
            'nett_weight1' => $row['nett_weight1'],
            'gross_weight2' => $row['gross_weight2'],
            'gross_weight2_date' => $row['gross_weight2_date'],
            'tare_weight2' => $row['tare_weight2'],
            'tare_weight2_date' => $row['tare_weight2_date'],
            'nett_weight2' => $row['nett_weight2'],
            'final_weight' => $row['final_weight'],
            'weight_different' => $row['weight_different'],
            'is_complete' => $row['is_complete'],
            'is_cancel' => $row['is_cancel'],
            'is_approved' => $row['is_approved'],
            'manual_weight' => $row['manual_weight'],
            'indicator_id' => $row['indicator_id'],
            'weighbridge_id' => $row['weighbridge_id'],
            'created_date' => $row['created_date'],
            'created_by' => $row['created_by'],
            'modified_date' => $row['modified_date'],
            'modified_by' => $row['modified_by'],
            'indicator_id_2' => $row['indicator_id_2'],
            'product_description' => $row['product_description']
        ];
    }

    /**
     * Row detail of a Weight record: the customer (or supplier for Purchase / Local) address and the weighing summary
     */
    private function weightDetail(array $row): array
    {
        $message = ['id' => $row['id'], 'name' => '', 'address_line_1' => '', 'address_line_2' => '', 'address_line_3' => '', 'phone_no' => '', 'fax_no' => ''];
        $usesSupplier = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

        $message = array_merge($message, $this->partyAddress($row, $usesSupplier));
        // The legacy detail showed the code here, not the name
        $message['product_rawmat_name'] = ($usesSupplier ? $row['raw_mat_code'] : $row['product_code']) ?? '';

        $dateTime = fn ($value): string => !empty($value) ? date("d/m/Y - h:i:sa", strtotime($value)) : '';

        foreach (['transporter', 'destination', 'plant_name', 'transaction_id', 'transaction_status', 'weight_type', 'invoice_no', 'delivery_no', 'container_no',
            'seal_no', 'container_no2', 'seal_no2', 'purchase_order'] as $field) {
            $message[$field] = $row[$field] ?? '';
        }
        $message['created_date'] = $row['created_date'] ? date("d/m/Y - h:i:sa", strtotime($row['created_date'])) : '';
        $message['lorry_plate_no1'] = $row['lorry_plate_no1'] ?? '';
        $message['gross_weight1'] = $row['gross_weight1'] ?? '';
        $message['gross_weight1_date'] = $dateTime($row['gross_weight1_date']);
        $message['gross_weight_by1'] = $row['gross_weight_by1'] ?? '';
        $message['tare_weight1'] = $row['tare_weight1'] ?? '';
        $message['tare_weight1_date'] = $dateTime($row['tare_weight1_date']);
        $message['tare_weight_by1'] = $row['tare_weight_by1'] ?? '';
        $message['nett_weight1'] = $row['nett_weight1'] ?? '';
        $message['lorry_plate_no2'] = $row['lorry_plate_no2'] ?? '';
        $message['gross_weight2'] = $row['gross_weight2'] ?? '';
        $message['gross_weight2_date'] = $dateTime($row['gross_weight2_date']);
        $message['gross_weight_by2'] = $row['gross_weight_by2'] ?? '';
        $message['tare_weight2'] = $row['tare_weight2'] ?? '';
        $message['tare_weight2_date'] = $dateTime($row['tare_weight2_date']);
        $message['tare_weight_by2'] = $row['tare_weight_by2'] ?? '';
        $message['nett_weight2'] = $row['nett_weight2'] ?? '';
        $message['reduce_weight'] = $row['reduce_weight'] ?? '';
        $message['final_weight'] = $row['final_weight'] ?? '';

        return $message;
    }

    /**
     * Row detail of a Weight_Container record. Address keys are only present when the customer / supplier is found, as the legacy did.
     */
    private function containerDetail(array $row): array
    {
        $usesSupplier = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

        $message = ['id' => $row['id']] + $this->partyAddress($row, $usesSupplier);
        $message['product_rawmat_name'] = ($usesSupplier ? $row['raw_mat_name'] : $row['product_name']) ?? '';

        foreach (['transporter', 'destination', 'plant_name', 'lorry_plate_no1', 'transaction_id', 'transaction_status', 'invoice_no', 'delivery_no', 'purchase_order'] as $field) {
            $message[$field] = $row[$field] ?? '';
        }
        // Empty dates show as 01/01/1970, as the legacy did
        $message['gross_weight1_date'] = date("d/m/Y - h:i:sa", strtotime((string)$row['gross_weight1_date']));
        $message['tare_weight1_date'] = date("d/m/Y - h:i:sa", strtotime((string)$row['tare_weight1_date']));
        $message['created_date'] = date("d/m/Y - h:i:sa", strtotime((string)$row['created_date']));
        foreach (['gross_weight1', 'tare_weight1', 'nett_weight1', 'reduce_weight', 'final_weight'] as $field) {
            $message[$field] = $row[$field] ?? '';
        }

        return $message;
    }

    /**
     * Name and address of the record's active supplier or customer; empty when not found
     */
    private function partyAddress(array $row, bool $usesSupplier): array
    {
        $party = $usesSupplier
            ? $this->fetchOne("SELECT * FROM Supplier WHERE supplier_code = ? AND status = '0'", 's', [$row['supplier_code']])
            : $this->fetchOne("SELECT * FROM Customer WHERE customer_code = ? AND status = '0'", 's', [$row['customer_code']]);

        if ($party === null) {
            return [];
        }

        return [
            'name' => $party['name'],
            'address_line_1' => $party['address_line_1'] ?? '',
            'address_line_2' => $party['address_line_2'] ?? '',
            'address_line_3' => $party['address_line_3'] ?? '',
            'phone_no' => $party['phone_no'] ?? '',
            'fax_no' => $party['fax_no'] ?? ''
        ];
    }

    /**
     * Edit form fields of a Weight or Weight_Container record
     */
    private function editFields(array $row, bool $isContainer): array
    {
        $fields = ['id', 'transaction_id', 'transaction_status', 'weight_type', 'customer_type', 'transaction_date', 'lorry_plate_no1', 'lorry_plate_no2',
            'supplier_weight', 'order_weight', 'customer_code', 'customer_name', 'plant_code', 'plant_name', 'supplier_code', 'supplier_name', 'product_code',
            'product_name', 'raw_mat_code', 'raw_mat_name', 'container_no', 'seal_no', 'container_no2', 'seal_no2', 'invoice_no', 'purchase_order', 'delivery_no',
            'transporter_code', 'transporter', 'destination_code', 'destination', 'remarks', 'gross_weight1', 'gross_weight1_date', 'gross_weight_by1',
            'tare_weight1', 'tare_weight1_date', 'tare_weight_by1', 'nett_weight1', 'lorry_no2_weight', 'empty_container2_weight', 'replacement_container',
            'gross_weight2', 'gross_weight2_date', 'gross_weight_by2', 'tare_weight2', 'tare_weight2_date', 'tare_weight_by2', 'nett_weight2', 'reduce_weight',
            'final_weight', 'weight_different', 'weight_different_perc', 'is_complete', 'is_cancel', 'manual_weight', 'indicator_id', 'weighbridge_id',
            'created_date', 'created_by', 'modified_date', 'modified_by', 'indicator_id_2', 'product_description', 'unit_price', 'sub_total', 'sst', 'total_price'];

        $message = [];
        foreach ($fields as $field) {
            $message[$field] = $row[$field];
        }

        // The container form blanked a missing product, as the legacy did
        if ($isContainer) {
            $message['product_code'] = $row['product_code'] ?? '';
            $message['product_name'] = $row['product_name'] ?? '';
        }

        $message['vehicleNoTxt'] = $this->vehicleNoTxt($row['lorry_plate_no1']);
        $message['vehicleNoTxt2'] = $this->vehicleNoTxt($row['lorry_plate_no2']);

        return $message;
    }

    /**
     * Plate number when it is not a registered vehicle (typed in manually), otherwise null
     */
    private function vehicleNoTxt($plateNo): ?string
    {
        return $this->fetchOne("SELECT id FROM Vehicle WHERE veh_number = ?", 's', [$plateNo]) === null ? $plateNo : null;
    }

    /**
     * Run one write. Returns true, or the error message the legacy endpoints returned.
     */
    private function write(string $sql, string $types, array $params)
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return 'Somethings wrong';
        }
        $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        $error = $stmt->error;
        $stmt->close();

        return $ok ? true : $error;
    }

    /**
     * Run a write whose WHERE takes a comma separated ID list (%s in $sql). No valid ID fails like the legacy empty IN () did.
     */
    private function writeIds(string $sql, array $params, $ids)
    {
        $idList = $this->cleanIds(explode(',', (string)$ids));
        if ($idList === []) {
            return 'Somethings wrong';
        }

        return $this->write(sprintf($sql, $this->placeholders(count($idList))), str_repeat('s', count($params)) . str_repeat('i', count($idList)), array_merge($params, $idList));
    }

    /**
     * The legacy FILTER_SANITIZE_STRING: strip tags and encode quotes
     */
    private function sanitizeString($value): string
    {
        return str_replace(['"', "'"], ['&#34;', '&#39;'], strip_tags((string)$value));
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
