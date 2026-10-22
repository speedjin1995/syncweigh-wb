<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../requires/lookup.php';
require_once __DIR__ . '/VehicleEntryService.php';
require_once __DIR__ . '/DepositService.php';

class WeightService extends BaseService {

    protected $table = 'Weight';

    // Columns the weighing list can be sorted by (DataTables sends the column name)
    private $weightSortColumns = ['id', 'location_id', 'transaction_id', 'transaction_status', 'weight_type', 'transaction_date', 'lorry_plate_no1', 'lorry_plate_no2', 'supplier_weight', 'customer_code', 'customer_name', 'plant_code', 'plant_name', 'supplier_code', 'supplier_name', 'product_code', 'product_name', 'container_no', 'seal_no', 'container_no2', 'seal_no2', 'invoice_no', 'purchase_order', 'delivery_no', 'transporter_code', 'transporter', 'destination_code', 'destination', 'remarks', 'gross_weight1', 'gross_weight1_date', 'tare_weight1', 'tare_weight1_date', 'nett_weight1', 'gross_weight2', 'gross_weight2_date', 'tare_weight2', 'tare_weight2_date', 'nett_weight2', 'final_weight', 'weight_different', 'is_complete', 'is_cancel', 'is_approved', 'manual_weight', 'indicator_id', 'weighbridge_id', 'created_date', 'created_by', 'modified_date', 'modified_by', 'indicator_id_2', 'product_description'];

    private $emptyContainerSortColumns = ['id', 'transaction_id', 'transaction_status', 'weight_type', 'transaction_date', 'lorry_plate_no1', 'lorry_plate_no2', 'supplier_weight', 'customer_code', 'customer_name', 'plant_code', 'plant_name', 'supplier_code', 'supplier_name', 'product_code', 'product_name', 'container_no', 'seal_no', 'invoice_no', 'purchase_order', 'delivery_no', 'transporter_code', 'transporter', 'destination_code', 'destination', 'remarks', 'gross_weight1', 'gross_weight1_date', 'tare_weight1', 'tare_weight1_date', 'nett_weight1', 'gross_weight2', 'gross_weight2_date', 'tare_weight2', 'tare_weight2_date', 'nett_weight2', 'final_weight', 'weight_different', 'is_complete', 'is_cancel', 'is_approved', 'manual_weight', 'indicator_id', 'weighbridge_id', 'created_date', 'created_by', 'modified_date', 'modified_by', 'indicator_id_2', 'product_description'];

    /**
     * Id of the newest pending record for the vehicle and transaction status, or null
     */
    public function getPendingIdByVehicle($vehicleNo, $transactionStatus) {
        $sql = "SELECT id FROM {$this->table} WHERE status = '0' AND is_complete = 'N' AND is_cancel = 'N' AND lorry_plate_no1 = ? AND transaction_status = ? ORDER BY id DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Unable to check pending records');
        }

        $stmt->bind_param('ss', $vehicleNo, $transactionStatus);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Unable to check pending records');
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? (int) $row['id'] : null;
    }

    /**
     * Weighing screen list (DataTables) with the status counts for the summary cards.
     * $plants is null for ADMIN / SADMIN (all plants), otherwise the user's plant codes.
     */
    public function getList($post, $plants) {
        $dt = $this->dataTableParams($post, $this->weightSortColumns);
        $f = $this->filterValues($post);

        $searchQuery = " ";

        if (!empty($post['fromDate'])) {
            $fromDateTime = DateTime::createFromFormat('d-m-Y', $post['fromDate'])->format('Y-m-d 00:00:00');
            $searchQuery = " and transaction_date >= '" . $this->esc($fromDateTime) . "'";
        }

        if (!empty($post['toDate'])) {
            $toDateTime = DateTime::createFromFormat('d-m-Y', $post['toDate'])->format('Y-m-d 23:59:59');
            $searchQuery .= " and transaction_date <= '" . $this->esc($toDateTime) . "'";
        }

        $searchQuery .= transactionStatusExclusion($this->db, 'transaction_status');
        $searchQuery .= permissionExclusion('Weighing', 'transaction_status', 'weight_type');

        if ($f('status') !== null) $searchQuery .= " and transaction_status = '" . $this->esc($f('status')) . "'";
        if ($f('customer') !== null) $searchQuery .= " and customer_code = '" . $this->esc($f('customer')) . "'";
        if ($f('supplier') !== null) $searchQuery .= " and supplier_code = '" . $this->esc($f('supplier')) . "'";
        if ($f('vehicle') !== null) $searchQuery .= " and lorry_plate_no1 like '%" . $this->esc($f('vehicle')) . "%'";
        if ($f('invoice') !== null) $searchQuery .= " and weight_type = '" . $this->esc($f('invoice')) . "'";

        if ($f('batch') !== null) {
            if ($f('batch') == 'Pending') {
                $searchQuery .= " and is_complete = 'N' AND is_cancel = 'N'";
            } elseif ($f('batch') == 'Complete') {
                $searchQuery .= " and is_complete = 'Y' AND is_cancel = 'N'";
            } elseif ($f('batch') == 'Cancel') {
                $searchQuery .= " and is_cancel = 'Y'";
            }
        } else {
            $searchQuery .= " and is_complete = 'N' AND is_cancel = 'N'";
        }

        if ($f('product') !== null) $searchQuery .= " and product_code = '" . $this->esc($f('product')) . "'";
        if ($f('rawMaterial') !== null) $searchQuery .= " and raw_mat_code = '" . $this->esc($f('rawMaterial')) . "'";
        if ($f('plant') !== null) $searchQuery .= " and plant_code = '" . $this->esc($f('plant')) . "'";
        if (isset($post['location']) && $f('location') !== null) $searchQuery .= " and location_id = '" . intval($post['location']) . "'";
        if ($f('transactionId') !== null) $searchQuery .= " and transaction_id like '%" . $this->esc($f('transactionId')) . "%'";

        if ($f('containerNo') !== null) {
            $c = $this->esc($f('containerNo'));
            $searchQuery .= " and (container_no like '%{$c}%' OR container_no2 like '%{$c}%')";
        }

        if ($f('sealNo') !== null) {
            $s = $this->esc($f('sealNo'));
            $searchQuery .= " and (seal_no like '%{$s}%' OR seal_no2 like '%{$s}%')";
        }

        if ($f('invDelPo') !== null) {
            $v = $this->esc($f('invDelPo'));
            $searchQuery .= " and (purchase_order like '%{$v}%' OR invoice_no like '%{$v}%' OR delivery_no like '%{$v}%')";
        }

        // Table search replaces the filters above (existing behaviour, kept as is)
        if ($dt['search'] != '') {
            $s = $this->esc($dt['search']);
            $searchQuery = " and (transaction_id like '%{$s}%' or lorry_plate_no1 like '%{$s}%')";
        }

        $plantSql = $plants === null ? '' : " and plant_code IN ('" . $this->plantList($plants) . "')";

        if (($post['batch'] ?? null) == 'N') {
            // Pending branch: counts both Weight and Weight_Container (existing behaviour, kept as is)
            if ($plants === null) {
                $allQuery = "select COUNT(*) as allcount FROM (SELECT * FROM Weight WHERE status = '0' UNION ALL SELECT * FROM Weight_Container WHERE status = '0') AS combined";
            } else {
                $allQuery = "select COUNT(*) as allcount FROM (SELECT * FROM Weight WHERE status = '0'{$plantSql} UNION ALL SELECT * FROM Weight WHERE status = '0'{$plantSql}) AS combined";
            }
            $totalRecords = $this->countQuery($allQuery);

            $filteredQuery = "select count(*) as allcount from (SELECT * FROM Weight where status = '0'{$plantSql}{$searchQuery} UNION ALL SELECT * FROM Weight_Container where status = '0'{$plantSql}{$searchQuery}) AS combined";
            $totalFiltered = $this->countQuery($filteredQuery);

            $empQuery = "(select * from Weight where status = '0'{$plantSql}{$searchQuery}) UNION ALL (select * from Weight_Container where status = '0'{$plantSql}{$searchQuery}) order by {$dt['column']} {$dt['dir']} limit {$dt['start']},{$dt['length']}";
        } else {
            $totalRecords = $this->countQuery("select count(*) as allcount from Weight where status = '0'{$plantSql}");
            $totalFiltered = $this->countQuery("select count(*) as allcount from Weight where status = '0'{$plantSql}{$searchQuery}");

            $empQuery = "select * from Weight where status = '0'{$plantSql}{$searchQuery}order by {$dt['column']} {$dt['dir']} limit {$dt['start']},{$dt['length']}";
        }

        $rows = $this->db->query($empQuery);
        $data = [];
        $salesCount = 0;
        $purchaseCount = 0;
        $localCount = 0;
        $miscCount = 0;

        while ($row = $rows->fetch_assoc()) {
            $transactionStatus = '';
            if ($row['transaction_status'] == 'Sales') {
                $salesCount++;
                $transactionStatus = 'Dispatch';
            } elseif ($row['transaction_status'] == 'Purchase') {
                $purchaseCount++;
                $transactionStatus = 'Receiving';
            } elseif ($row['transaction_status'] == 'Misc') {
                $miscCount++;
                $transactionStatus = 'Miscellaneous';
            } else {
                $localCount++;
                $transactionStatus = 'Internal Transfer';
            }

            if ($row['weight_type'] == 'Container') {
                $weightType = 'Primer Mover';
            } elseif ($row['weight_type'] == 'Empty Container') {
                $weightType = 'Primer Mover + Container';
            } elseif ($row['weight_type'] == 'Different Container') {
                $weightType = 'Primer Mover + Different Bins';
            } else {
                $weightType = $row['weight_type'];
            }

            // Time since IN for vehicles still inside; '-' for completed/cancelled rows
            $duration = '-';
            if ($row['is_complete'] == 'N' && $row['is_cancel'] == 'N' && !empty($row['gross_weight1_date'])) {
                $elapsed = (new DateTime($row['gross_weight1_date']))->diff(new DateTime());
                $duration = ($elapsed->days > 0 ? $elapsed->days . 'd ' : '') . ($elapsed->h > 0 ? $elapsed->h . 'h ' : '') . $elapsed->i . 'm';
            }

            $isPurchaseOrLocal = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

            $data[] = [
                'id' => $row['id'],
                'location' => searchLocationNameById($row['location_id'], $this->db),
                'transaction_id' => $row['transaction_id'],
                'transaction_status' => $transactionStatus,
                'weight_type' => $weightType,
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
                'customer' => $isPurchaseOrLocal ? $row['supplier_name'] : $row['customer_name'],
                'product_code' => $isPurchaseOrLocal ? $row['raw_mat_code'] : $row['product_code'],
                'product_name' => $isPurchaseOrLocal ? $row['raw_mat_name'] : $row['product_name'],
                'container_no' => $row['container_no'],
                'seal_no' => $row['seal_no'],
                'container_no2' => $row['container_no2'],
                'seal_no2' => $row['seal_no2'],
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
                'duration' => $duration,
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

        return [
            'draw' => $dt['draw'],
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data,
            'salesTotal' => $salesCount,
            'purchaseTotal' => $purchaseCount,
            'localTotal' => $localCount,
            'miscTotal' => $miscCount
        ];
    }

    /**
     * Completed empty container list (DataTables) with the status counts
     */
    public function getEmptyContainerList($post) {
        $dt = $this->dataTableParams($post, $this->emptyContainerSortColumns);
        $f = $this->filterValues($post);

        $searchQuery = " and is_complete = 'Y' and is_cancel = 'N'";

        if (!empty($post['fromDate'])) {
            $fromDateTime = DateTime::createFromFormat('d-m-Y', $post['fromDate'])->format('Y-m-d 00:00:00');
            $searchQuery .= " and transaction_date >= '" . $this->esc($fromDateTime) . "'";
        }

        if (!empty($post['toDate'])) {
            $toDateTime = DateTime::createFromFormat('d-m-Y', $post['toDate'])->format('Y-m-d 23:59:59');
            $searchQuery .= " and transaction_date <= '" . $this->esc($toDateTime) . "'";
        }

        if (isset($post['location']) && $f('location') !== null) {
            $searchQuery .= " and location_id = '" . intval($post['location']) . "'";
        }

        if ($dt['search'] != '') {
            $s = $this->esc($dt['search']);
            $searchQuery = " and (transaction_id like '%{$s}%' or lorry_plate_no1 like '%{$s}%' or container_no like '%{$s}%')";
        }

        $totalRecords = $this->countQuery("select count(*) as allcount from Weight_Container where status = '0'");
        $totalFiltered = $this->countQuery("select count(*) as allcount from Weight_Container where status = '0'" . $searchQuery);

        $rows = $this->db->query("select * from Weight_Container where status = '0'{$searchQuery}order by {$dt['column']} {$dt['dir']} limit {$dt['start']},{$dt['length']}");
        $data = [];
        $salesCount = 0;
        $purchaseCount = 0;
        $localCount = 0;
        $miscCount = 0;

        while ($row = $rows->fetch_assoc()) {
            $transactionStatus = '';
            if ($row['transaction_status'] == 'Sales') {
                $salesCount++;
                $transactionStatus = 'Dispatch';
            } elseif ($row['transaction_status'] == 'Purchase') {
                $purchaseCount++;
                $transactionStatus = 'Receiving';
            } elseif ($row['transaction_status'] == 'Misc') {
                $miscCount++;
                $transactionStatus = 'Miscellaneous';
            } else {
                $localCount++;
                $transactionStatus = 'Internal Transfer';
            }

            $isPurchaseOrLocal = $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';

            $data[] = [
                'id' => $row['id'],
                'transaction_id' => $row['transaction_id'],
                'transaction_status' => $transactionStatus,
                'weight_type' => $row['weight_type'],
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
                'customer' => $isPurchaseOrLocal ? $row['supplier_name'] : $row['customer_name'],
                'product_code' => $isPurchaseOrLocal ? $row['raw_mat_code'] : $row['product_code'],
                'product_name' => $isPurchaseOrLocal ? $row['raw_mat_name'] : $row['product_name'],
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

        return [
            'draw' => $dt['draw'],
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data,
            'salesTotal' => $salesCount,
            'purchaseTotal' => $purchaseCount,
            'localTotal' => $localCount,
            'miscTotal' => $miscCount
        ];
    }

    /**
     * Completed empty containers for a transaction status (id, container_no)
     */
    public function getContainers($transactionStatus) {
        $stmt = $this->prepareOrFail("SELECT * FROM Weight_Container WHERE transaction_status=? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'");
        $stmt->bind_param('s', $transactionStatus);
        $rows = $this->fetchAll($stmt);

        $message = [];
        foreach ($rows as $row) {
            $message[] = ['id' => $row['id'], 'container_no' => $row['container_no']];
        }

        return $message;
    }

    /**
     * First weighing of a completed empty container by container no. Empty array when not found.
     */
    public function getEmptyContainer($containerNo) {
        $stmt = $this->prepareOrFail("SELECT * FROM Weight_Container WHERE container_no=? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'");
        $stmt->bind_param('s', $containerNo);
        $row = $this->fetchOne($stmt);

        if (!$row) {
            return [];
        }

        $message = [];
        foreach (['transaction_status', 'customer_code', 'customer_name', 'supplier_code', 'supplier_name', 'invoice_no', 'delivery_no', 'purchase_order', 'container_no', 'seal_no', 'container_no2', 'seal_no2', 'product_code', 'product_name', 'raw_mat_code', 'raw_mat_name', 'plant_code', 'plant_name', 'transporter_code', 'transporter', 'destination_code', 'destination', 'gross_weight1', 'gross_weight1_date', 'gross_weight_by1', 'tare_weight1', 'tare_weight1_date', 'tare_weight_by1', 'lorry_plate_no1', 'nett_weight1'] as $column) {
            $message[$column] = $row[$column];
        }

        // Plate not in the vehicle master: show the plate itself as the vehicle text
        $vehicle = $this->prepareOrFail("SELECT * FROM Vehicle WHERE veh_number=?");
        $vehicle->bind_param('s', $row['lorry_plate_no1']);
        $found = $this->fetchOne($vehicle);
        $message['vehicleNoTxt'] = $found ? null : $row['lorry_plate_no1'];

        return $message;
    }

    /**
     * One weighing record for the edit form (MODAL, default) or the row detail (EXPANDABLE).
     * type Container reads Weight_Container, type Log with EXPANDABLE reads Weight_Log, anything else Weight.
     */
    public function getWeight($id, $format, $type) {
        if ($format == 'EXPANDABLE' && $type == 'Log') {
            return $this->getWeightLog($id);
        }

        if ($type == 'Container') {
            return $this->getContainerWeight($id, $format);
        }

        return $this->getWeightRecord($id, $format);
    }

    private function getWeightLog($id) {
        $row = $this->selectOne("SELECT * FROM Weight_Log WHERE id=?", 's', [$id]);

        if (!$row) {
            return [];
        }

        $message = [];
        // Loose null check, as the legacy endpoint does (so a stored '0' status shows as empty)
        foreach ($row as $key => $value) {
            $message[$key] = $value == null ? '' : $value;
        }
        return $message;
    }

    private function getContainerWeight($id, $format) {
        $row = $this->selectOne("SELECT wc.*, c.name AS contractor_name FROM Weight_Container wc LEFT JOIN Contractor c ON c.id = wc.contractor_id WHERE wc.id=?", 's', [$id]);

        if (!$row) {
            return [];
        }

        if ($format == 'EXPANDABLE') {
            $message = ['id' => $row['id']] + $this->partyDetail($row);
            $message['product_rawmat_name'] = $this->isPurchaseOrLocal($row) ? ($row['raw_mat_name'] ?? '') : ($row['product_name'] ?? '');
            $message['remarks'] = $row['remarks'] ?? '';
            $message['transporter'] = $row['transporter'] ?? '';
            $message['destination'] = $row['destination'] ?? '';
            $message['plant_name'] = $row['plant_name'] ?? '';
            $message['lorry_plate_no1'] = $row['lorry_plate_no1'] ?? '';
            $message['transaction_id'] = $row['transaction_id'] ?? '';
            $message['transaction_status'] = $row['transaction_status'] ?? '';
            $message['invoice_no'] = $row['invoice_no'] ?? '';
            $message['delivery_no'] = $row['delivery_no'] ?? '';
            $message['purchase_order'] = $row['purchase_order'] ?? '';
            $message['gross_weight1_date'] = $this->dateTimeText($row['gross_weight1_date']);
            $message['tare_weight1_date'] = $this->dateTimeText($row['tare_weight1_date']);
            $message['created_date'] = $this->dateTimeText($row['created_date']);
            $message['gross_weight1'] = $row['gross_weight1'] ?? '';
            $message['tare_weight1'] = $row['tare_weight1'] ?? '';
            $message['nett_weight1'] = $row['nett_weight1'] ?? '';
            $message['reduce_weight'] = $row['reduce_weight'] ?? '';
            $message['final_weight'] = $row['final_weight'] ?? '';
            $message['vehicle_entry_logs'] = (new VehicleEntryService($this->db, $this->username))->logs('Weight_Container', $id);
            return $message;
        }

        $message = $this->modalFields($row, true);
        $message['vehicleNoTxt'] = $this->plateCheck($row['lorry_plate_no1']);
        $message['vehicleNoTxt2'] = $this->plateCheck($row['lorry_plate_no2']);
        return $message;
    }

    private function getWeightRecord($id, $format) {
        $row = $this->selectOne("SELECT w.*, c.name AS contractor_name FROM Weight w LEFT JOIN Contractor c ON c.id = w.contractor_id WHERE w.id=?", 's', [$id]);

        if (!$row) {
            return [];
        }

        if ($format == 'EXPANDABLE') {
            $message = ['id' => $row['id'], 'name' => '', 'address_line_1' => '', 'address_line_2' => '', 'address_line_3' => '', 'phone_no' => '', 'fax_no' => ''];

            if ($this->isPurchaseOrLocal($row)) {
                $party = searchSupplierByCode($row['supplier_code'], $this->db);
                $message['product_rawmat_name'] = $row['raw_mat_code'] . ' - ' . $row['raw_mat_name'];
            } else {
                $party = searchCustomerByCode($row['customer_code'], $this->db);
                $message['product_rawmat_name'] = $row['product_code'] . ' - ' . $row['product_name'];
            }
            $message['name'] = $party['name'] ?? null;
            $message['address_line_1'] = $party['address_line_1'] ?? '';
            $message['address_line_2'] = $party['address_line_2'] ?? '';
            $message['address_line_3'] = $party['address_line_3'] ?? '';
            $message['phone_no'] = $party['phone_no'] ?? '';
            $message['fax_no'] = $party['fax_no'] ?? '';

            $contractor = searchContractorById($row['contractor_id'], $this->db);
            $message['contractorName'] = $contractor['name'] ?? '';
            $message['remarks'] = $row['remarks'] ?? '';
            $message['transporter'] = $row['transporter'] ?? '';
            $message['destination'] = $row['destination'] ?? '';
            $message['plant_name'] = $row['plant_name'] ?? '';
            $message['transaction_id'] = $row['transaction_id'] ?? '';
            $message['transaction_status'] = $row['transaction_status'] ?? '';
            $message['weight_type'] = $row['weight_type'] ?? '';
            $message['invoice_no'] = $row['invoice_no'] ?? '';
            $message['delivery_no'] = $row['delivery_no'] ?? '';
            $message['container_no'] = $row['container_no'] ?? '';
            $message['seal_no'] = $row['seal_no'] ?? '';
            $message['container_no2'] = $row['container_no2'] ?? '';
            $message['seal_no2'] = $row['seal_no2'] ?? '';
            $message['purchase_order'] = $row['purchase_order'] ?? '';
            $message['transaction_date'] = !empty($row['transaction_date']) ? date('d/m/Y', strtotime($row['transaction_date'])) : '';
            $message['created_date'] = $row['created_date'] ? date('d/m/Y - h:i:sa', strtotime($row['created_date'])) : '';
            $message['lorry_plate_no1'] = $row['lorry_plate_no1'] ?? '';
            $message['gross_weight1'] = $row['gross_weight1'] ?? '';
            $message['gross_weight1_date'] = !empty($row['gross_weight1_date']) ? $this->dateTimeText($row['gross_weight1_date']) : '';
            $message['gross_weight_by1'] = $row['gross_weight_by1'] ?? '';
            $message['location_id'] = $row['location_id'] ?? null;
            $message['location_id2'] = $row['location_id2'] ?? null;
            $message['tare_weight1'] = $row['tare_weight1'] ?? '';
            $message['tare_weight1_date'] = !empty($row['tare_weight1_date']) ? $this->dateTimeText($row['tare_weight1_date']) : '';
            $message['tare_weight_by1'] = $row['tare_weight_by1'] ?? '';
            $message['nett_weight1'] = $row['nett_weight1'] ?? '';
            $message['lorry_plate_no2'] = $row['lorry_plate_no2'] ?? '';
            $message['gross_weight2'] = $row['gross_weight2'] ?? '';
            $message['gross_weight2_date'] = !empty($row['gross_weight2_date']) ? $this->dateTimeText($row['gross_weight2_date']) : '';
            $message['gross_weight_by2'] = $row['gross_weight_by2'] ?? '';
            $message['tare_weight2'] = $row['tare_weight2'] ?? '';
            $message['tare_weight2_date'] = !empty($row['tare_weight2_date']) ? $this->dateTimeText($row['tare_weight2_date']) : '';
            $message['tare_weight_by2'] = $row['tare_weight_by2'] ?? '';
            $message['nett_weight2'] = $row['nett_weight2'] ?? '';
            $message['reduce_weight'] = $row['reduce_weight'] ?? '';
            $message['final_weight'] = $row['final_weight'] ?? '';
            $message['unit_price'] = $row['unit_price'] ?? '';
            $message['total_price'] = $row['total_price'] ?? '';
            $message['vehicle_entry_logs'] = (new VehicleEntryService($this->db, $this->username))->logs('Weight', $id);
            return $message;
        }

        $message = $this->modalFields($row, false);
        $message['vehicleNoTxt'] = $this->plateCheck($row['lorry_plate_no1']);
        $message['vehicleNoTxt2'] = $this->plateCheck($row['lorry_plate_no2']);
        return $message;
    }

    /**
     * Edit form fields, shared by Weight and Weight_Container (values as stored, missing columns as null)
     */
    private function modalFields($row, $isContainer) {
        $fields = ['id', 'transaction_id', 'transaction_status', 'weight_type', 'customer_type', 'transaction_date', 'lorry_plate_no1', 'lorry_plate_no2', 'supplier_weight', 'order_weight', 'customer_code', 'customer_name', 'plant_code', 'plant_name', 'supplier_code', 'supplier_name', 'product_code', 'product_name', 'raw_mat_code', 'raw_mat_name', 'contractor_id', 'contractor_name', 'container_no', 'seal_no', 'container_no2', 'seal_no2', 'invoice_no', 'purchase_order', 'delivery_no', 'transporter_code', 'transporter', 'destination_code', 'destination', 'remarks', 'gross_weight1', 'gross_weight1_date', 'gross_weight_by1', 'location_id', 'location_id2', 'tare_weight1', 'tare_weight1_date', 'tare_weight_by1', 'nett_weight1', 'lorry_no2_weight', 'empty_container2_weight', 'replacement_container', 'gross_weight2', 'gross_weight2_date', 'gross_weight_by2', 'tare_weight2', 'tare_weight2_date', 'tare_weight_by2', 'nett_weight2', 'reduce_weight', 'final_weight', 'weight_different', 'weight_different_perc', 'is_complete', 'is_cancel', 'manual_weight', 'indicator_id', 'weighbridge_id', 'created_date', 'created_by', 'modified_date', 'modified_by', 'indicator_id_2', 'product_description', 'unit_price', 'sub_total', 'sst', 'sst_rate', 'total_price'];

        $message = [];
        foreach ($fields as $field) {
            $message[$field] = $row[$field] ?? null;
        }
        // Container form shows empty text for a missing product, the weight form keeps the stored value
        if ($isContainer) {
            $message['product_code'] = $row['product_code'] ?? '';
            $message['product_name'] = $row['product_name'] ?? '';
        }
        $message['contractor_name'] = $row['contractor_name'] ?? '';
        $message['contractor_id'] = $row['contractor_id'] ?? null;
        return $message;
    }

    /**
     * Name and address of the party (supplier for Purchase / Local, otherwise customer), as the detail view shows it
     */
    private function partyDetail($row) {
        $isPurchaseOrLocal = $this->isPurchaseOrLocal($row);
        $party = $isPurchaseOrLocal
            ? $this->selectOne("SELECT * FROM Supplier WHERE supplier_code=? AND status = '0'", 's', [$row['supplier_code']])
            : $this->selectOne("SELECT * FROM Customer WHERE customer_code=? AND status = '0'", 's', [$row['customer_code']]);

        if (!$party) {
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

    private function isPurchaseOrLocal($row) {
        return $row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local';
    }

    // null when the plate is in the vehicle master, otherwise the plate itself
    private function plateCheck($plate) {
        $vehicle = $this->selectOne("SELECT * FROM Vehicle WHERE veh_number=?", 's', [$plate]);
        return $vehicle ? null : $plate;
    }

    private function dateTimeText($value) {
        return date('d/m/Y - h:i:sa', strtotime((string) $value));
    }

    private function selectOne($sql, $types, $params) {
        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param($types, ...$params);
        return $this->fetchOne($stmt);
    }

    /**
     * Save a weighing record: create, or update when id is posted. Normal and Container weight types.
     * Empty Container and Different Container are still saved by php/weight.php. Returns the response array.
     */
    public function save($post, $userId) {
        $status = $post['transactionStatus'];
        $weightType = $post['weightType'];

        if (!isTransactionStatusEnabled($status, $this->db)) {
            return ['status' => 'failed', 'message' => 'Transaction status is not enabled for this company.'];
        }

        // Weighing permissions: create a new record, or edit / weight out an existing one
        $savePermissions = empty($post['id']) ? ['create'] : ['edit', 'weight_out'];
        if (!statusPermitted('Weighing', $status, $savePermissions) || !weightTypePermitted($weightType)) {
            return ['status' => 'failed', 'message' => 'You do not have permission to save this weighing record'];
        }

        // Gate per weighing: the client stamps the session location when each weight is captured.
        // Keep an id only when it is an active Location.
        $activeGates = array_column($this->db->query("SELECT id FROM Location WHERE status = '0'")->fetch_all(MYSQLI_ASSOC), 'id');
        $locationId = in_array((int) ($post['grossLocationId'] ?? 0), $activeGates) ? (int) $post['grossLocationId'] : null;
        $locationId2 = in_array((int) ($post['tareLocationId'] ?? 0), $activeGates) ? (int) $post['tareLocationId'] : null;

        $weightId = empty($post['id']) ? null : trim($post['id']);
        $plantCode = empty($post['plantCode']) ? null : trim($post['plantCode']);
        $plant = empty($post['plant']) ? null : trim($post['plant']);
        $weightType = empty($post['weightType']) ? 'Normal' : trim($post['weightType']);

        // Transaction number: generated from the plant counter when none is posted
        $misValue = '';
        $diffPrefix = '';
        if (empty($post['transactionId'])) {
            $generated = $this->generateTransactionId($status, $weightType, $plantCode);
            if ($generated === null) {
                return ['status' => 'failed', 'message' => 'Something went wrong when pulling status'];
            }
            $transactionId = $generated['transactionId'];
            $misValue = $generated['misValue'];
            $diffPrefix = $generated['prefix'];
        } else {
            $transactionId = trim($post['transactionId']);
        }

        $transactionStatus = empty($post['transactionStatus']) ? null : trim($post['transactionStatus']);

        // Sales/Purchase price is calculated here from the pricing setup, not taken from the browser
        $sstRateValue = 0.0;
        $deposit = new DepositService($this->db, $this->username);
        if (in_array($status, ['Sales', 'Purchase'], true) && in_array($weightType, ['Normal', 'Container', 'Different Container'], true)) {
            $sstRateValue = isset($post['sstRate']) && $post['sstRate'] !== '' ? (float) $post['sstRate'] : 0.0;
            if ($sstRateValue < 0 || $sstRateValue > 100) {
                return ['status' => 'failed', 'message' => 'SST rate must be between 0 and 100'];
            }

            $partyCode = trim((string) ($status === 'Sales' ? ($post['customerCode'] ?? '') : ($post['supplierCode'] ?? '')));
            $itemCode = trim((string) ($status === 'Sales' ? ($post['productCode'] ?? '') : ($post['rawMaterialCode'] ?? '')));
            $priceParty = $deposit->getPartyByCode($deposit->partyTypeForStatus($status), $partyCode);
            $postedPrice = trim((string) ($post['unitPrice'] ?? ''));

            if ($postedPrice !== '' && statusPermitted('Weighing', $status, 'manual_price')) {
                if (!is_numeric($postedPrice) || (float) $postedPrice < 0) {
                    return ['status' => 'failed', 'message' => 'Unit price must be 0 or more'];
                }
                $unitPriceValue = (float) $postedPrice;
            } else {
                $unitPriceValue = ($priceParty && $itemCode !== '') ? $deposit->price($deposit->partyTypeForStatus($status), $priceParty['id'], $itemCode) : 0;
            }
            $amounts = $deposit->amounts((float) ($post['finalWeight'] ?? 0), $unitPriceValue, $sstRateValue);

            $post['unitPrice'] = $unitPriceValue;
            $post['subTotalPrice'] = $amounts['sub_total'];
            $post['sstPrice'] = $amounts['sst'];
            $post['totalPrice'] = $amounts['total_price'];
            $post['sstRate'] = $sstRateValue;
        }

        $values = $this->weightValues($post, $transactionId, $transactionStatus, $weightType, $locationId, $locationId2, $plantCode, $plant);
        if ($weightType == 'Different Container') {
            $values['lorry_no2_weight'] = empty($post['vehicleWeight2']) ? null : trim($post['vehicleWeight2']);
            $values['empty_container2_weight'] = empty($post['emptyContainerWeight2']) ? 0 : trim($post['emptyContainerWeight2']);
            $values['replacement_container'] = empty($post['replacementContainer']) ? 0 : trim($post['replacementContainer']);
        }

        // Vehicle daily entry limit: new records only, checked before anything is saved
        $vehicleEntry = new VehicleEntryService($this->db, $this->username);
        $vehicleEntryStatus = null;
        $vehicleEntryOverride = false;

        if (empty($weightId)) {
            $vehicleEntryStatus = $vehicleEntry->status($values['lorry_plate_no1'], date('Y-m-d'));

            if (!empty($vehicleEntryStatus) && $vehicleEntryStatus['exceeded']) {
                $overridePassword = $post['overridePassword'] ?? '';

                if (!$vehicleEntry->verifyPassword($userId, $overridePassword)) {
                    if ($vehicleEntryStatus['max'] === 0) {
                        $entryMessage = 'Vehicle ' . $values['lorry_plate_no1'] . ' is blocked today (daily limit 0). ';
                    } else {
                        $entryMessage = 'Insufficient entry for ' . $values['lorry_plate_no1'] . ' (' . $vehicleEntryStatus['count'] . '/' . $vehicleEntryStatus['max'] . ' used today). ';
                    }
                    $entryMessage .= ($overridePassword === '') ? 'Password confirmation is required.' : 'Invalid password.';
                    return ['status' => 'failed', 'message' => $entryMessage];
                }

                $vehicleEntryOverride = true;
            }
        }

        if ($weightType == 'Empty Container' && (int) $values['gross_weight1'] < (int) $values['tare_weight1']) {
            return ['status' => 'failed', 'message' => 'Incoming Weight cannot greater than outgoing weight'];
        }

        // Deposit balance: Sales/Purchase saves run in one transaction with the deposit charge
        $depositCtx = null;
        $isComplete = $values['is_complete'];
        if (in_array($status, ['Sales', 'Purchase'], true) && in_array($weightType, ['Normal', 'Container', 'Different Container'], true)) {
            $depositOn = $deposit->controlEnabled();
            $partyCode = trim((string) ($status === 'Sales' ? $post['customerCode'] ?? '' : $post['supplierCode'] ?? ''));
            $depositParty = $deposit->getPartyByCode($deposit->partyTypeForStatus($status), $partyCode);

            if ($depositOn && !$depositParty) {
                return ['status' => 'failed', 'message' => ($status === 'Sales' ? 'Customer' : 'Supplier') . ' not found'];
            }

            $amount = $isComplete == 'Y' ? (float) $values['total_price'] : 0.0;
            $oldAmount = empty($weightId) ? 0.0 : $deposit->chargedAmount($weightId);
            $delta = round($amount - $oldAmount, 2);

            if ($depositOn && ((empty($weightId) && $depositParty['balance'] < 1) || $delta > $depositParty['balance'])) {
                return ['status' => 'failed', 'message' => 'Insufficient Deposit Please Contact Office Admin'];
            }

            $this->db->begin_transaction();
            $depositCtx = [
                'sstRate' => $sstRateValue,
                'depositAmount' => $depositOn ? $amount : null,
                'party' => $depositOn ? $depositParty : null,
                'delta' => $delta,
                'remark' => 'Weighing ' . $transactionId,
                'username' => $this->username
            ];
        }

        try {
            if ($weightType == 'Empty Container') {
                return $this->saveEmptyContainer($values, $weightId, $status, $plantCode, $misValue, $vehicleEntry, $vehicleEntryStatus, $vehicleEntryOverride);
            }
            if ($weightType == 'Different Container') {
                return $this->saveDifferentContainer($values, $weightId, $status, $plantCode, $misValue, $diffPrefix, $deposit, $depositCtx, $vehicleEntry, $vehicleEntryStatus, $vehicleEntryOverride);
            }

            if (!empty($weightId)) {
                $this->updateWeight($values, $weightId);
                $deposit->settle($depositCtx, $weightId);
                $this->setContainerCancelled($values, $weightType, $isComplete);
                return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $weightId];
            }

            $id = $this->insertWeight($values);
            $misValue = ($misValue === '' ? 0 : (int) $misValue) + 1;
            $deposit->settle($depositCtx, $id);
            $vehicleEntry->logEntryIn($vehicleEntryStatus, 'Weight', $id, $transactionId, $vehicleEntryOverride, $this->username);
            $this->updatePlantCounter($status, $plantCode, $misValue);
            $this->setContainerCancelled($values, $weightType, $isComplete);
            return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
        } catch (DepositInsufficient $e) {
            return ['status' => 'failed', 'message' => $e->getMessage()];
        } catch (Exception $e) {
            if ($depositCtx !== null) {
                $this->db->rollback();
            }
            error_log('WeightService::save - ' . $e->getMessage());
            return ['status' => 'failed', 'message' => 'Something went wrong'];
        }
    }

    /**
     * Empty Container: saved to Weight_Container. An update whose id is not there yet inserts it with that id.
     */
    private function saveEmptyContainer($values, $weightId, $status, $plantCode, $misValue, $vehicleEntry, $vehicleEntryStatus, $vehicleEntryOverride) {
        if (!empty($weightId)) {
            if ($this->selectOne("SELECT id FROM Weight_Container WHERE id = ?", 's', [$weightId])) {
                $this->updateWeight($values, $weightId, 'Weight_Container');
                return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $weightId];
            }

            $this->insertWeight(['id' => $weightId] + $values, 'Weight_Container');
            return ['status' => 'success', 'message' => 'Inserted Successfully!!', 'id' => $weightId];
        }

        $id = $this->insertWeight($values, 'Weight_Container');
        $vehicleEntry->logEntryIn($vehicleEntryStatus, 'Weight_Container', $id, $values['transaction_id'], $vehicleEntryOverride, $this->username);
        $this->updatePlantCounter($status, $plantCode, ($misValue === '' ? 0 : (int) $misValue) + 1);
        return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
    }

    /**
     * Different Container (prime mover + different bin): the Weight row, then once complete the replacement bin goes to
     * Weight_Container (updated when it is already there, otherwise added with its own transaction number) and the
     * posted container's empty record follows the weighing's completion.
     */
    private function saveDifferentContainer($values, $weightId, $status, $plantCode, $misValue, $diffPrefix, $deposit, $depositCtx, $vehicleEntry, $vehicleEntryStatus, $vehicleEntryOverride) {
        $isComplete = $values['is_complete'];
        $containerNo = $values['container_no'];
        $container = $this->replacementContainerValues($values);

        if (!empty($weightId)) {
            $this->updateWeight($values, $weightId);
            $deposit->settle($depositCtx, $weightId);

            if (!empty($containerNo)) {
                $this->run("UPDATE Weight_Container SET is_cancel=? WHERE container_no=? AND status='0'", 'ss', [$isComplete, $containerNo]);

                if ($isComplete == 'Y') {
                    $existing = $this->selectOne("SELECT id FROM Weight_Container WHERE container_no=? AND is_complete='Y' AND is_cancel='N' AND status='0'", 's', [(string) $values['replacement_container']]);

                    if ($existing) {
                        // The legacy update shifts these three columns; kept so existing data stays consistent
                        $container['lorry_no2_weight'] = $values['replacement_container'];
                        $container['empty_container2_weight'] = $values['lorry_no2_weight'];
                        $container['replacement_container'] = $values['empty_container2_weight'];
                        $this->updateWeight($container, $existing['id'], 'Weight_Container');
                    } else {
                        $generated = $this->generateTransactionId($status, $values['weight_type'], $plantCode);
                        $this->insertWeight(['transaction_id' => $generated['transactionId']] + $container, 'Weight_Container');
                        $this->updatePlantCounter($status, $plantCode, ($generated['misValue'] === '' ? 0 : (int) $generated['misValue']) + 1);
                    }
                }
            }

            return ['status' => 'success', 'message' => 'Updated Successfully!!', 'id' => $weightId];
        }

        if (empty($values['gross_weight1_date'])) {
            $values['gross_weight1_date'] = $values['gross_weight2_date'];
        }

        $id = $this->insertWeight($values);
        $misValue = ($misValue === '' ? 0 : (int) $misValue) + 1;
        $deposit->settle($depositCtx, $id);
        $vehicleEntry->logEntryIn($vehicleEntryStatus, 'Weight', $id, $values['transaction_id'], $vehicleEntryOverride, $this->username);
        $this->updatePlantCounter($status, $plantCode, $misValue);

        if ($isComplete == 'Y') {
            $this->insertWeight(['transaction_id' => $diffPrefix . str_pad((string) $misValue, 4, '0', STR_PAD_LEFT)] + $container, 'Weight_Container');
            $this->updatePlantCounter($status, $plantCode, $misValue + 1);
        }

        if (!empty($containerNo)) {
            $this->run("UPDATE Weight_Container SET is_cancel=? WHERE container_no=? AND status='0'", 'ss', [$isComplete, $containerNo]);
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!', 'id' => $id];
    }

    // Replacement bin row from a Different Container weighing (the legacy column mapping is kept as is)
    private function replacementContainerValues($values) {
        $container = $values;
        unset($container['transaction_id']);
        $container['lorry_plate_no1'] = $values['lorry_plate_no2'];
        $container['container_no'] = $values['replacement_container'];
        $container['gross_weight1'] = $values['gross_weight2'];
        $container['gross_weight1_date'] = $values['gross_weight2_date'];
        $container['gross_weight_by1'] = $values['gross_weight_by2'];
        $container['tare_weight1'] = $values['lorry_no2_weight'];
        $container['tare_weight1_date'] = $values['gross_weight2_date'];
        $container['tare_weight_by1'] = $values['gross_weight_by2'];
        $container['nett_weight1'] = $values['empty_container2_weight'];
        return $container;
    }

    /**
     * Transaction number for a new weighing: prefix, date, then the plant counter padded to 4 digits.
     * Returns null when the status row cannot be read.
     */
    private function generateTransactionId($status, $weightType, $plantCode) {
        $row2 = $this->selectOne("SELECT * FROM status WHERE status=?", 's', [$status]);

        $transactionId = $plantCode . '/';
        $prefix = '';
        if ($row2) {
            if ($weightType == 'Container') {
                $transactionId .= 'C/' . $row2['prefix'] . '/' . date('ym') . '-';
            } else {
                $transactionId .= $row2['prefix'] . '/' . date('ym') . '-';
                $prefix = $transactionId;
            }
        }

        $counter = $this->selectOne("SELECT " . $this->plantColumn($status) . " AS curcount FROM Plant WHERE plant_code=?", 's', [$plantCode]);
        $misValue = '';
        if ($counter) {
            $misValue = $counter['curcount'];
            $transactionId .= str_repeat('0', max(0, 4 - strlen($misValue))) . $misValue;
        }

        // prefix: the number without its counter, used for the Different Container replacement bin
        return ['transactionId' => $transactionId, 'misValue' => $misValue, 'prefix' => $prefix];
    }

    // Plant counter column for the transaction status (sales is the default)
    private function plantColumn($status) {
        if ($status == 'Purchase') return 'purchase';
        if ($status == 'Local') return 'locals';
        if ($status == 'Misc') return 'misc';
        return 'sales';
    }

    private function updatePlantCounter($status, $plantCode, $misValue) {
        $this->run("UPDATE Plant SET " . $this->plantColumn($status) . "=? WHERE plant_code=?", 'ss', [(string) $misValue, $plantCode]);
    }

    /**
     * The weighing record's columns, in the order the Weight table is written (insert and update use the same list)
     */
    private function weightValues($post, $transactionId, $transactionStatus, $weightType, $locationId, $locationId2, $plantCode, $plant) {
        $field = function ($key, $default = null) use ($post) {
            return empty($post[$key]) ? $default : trim($post[$key]);
        };

        $vehiclePlateNo1 = $field('vehiclePlateNo1');
        if (array_key_exists('manualVehicle', $post)) {
            $vehiclePlateNo1 = trim((string) ($post['vehicleNoTxt'] ?? ''));
        }
        $vehiclePlateNo2 = $field('vehiclePlateNo2');
        if (array_key_exists('manualVehicle2', $post)) {
            $vehiclePlateNo2 = trim((string) ($post['vehicleNoTxt2'] ?? ''));
        }

        $grossIncoming = empty($post['grossIncoming']) ? 0 : trim($post['grossIncoming']);
        $tareOutgoing = empty($post['tareOutgoing']) ? 0 : trim($post['tareOutgoing']);
        $grossIncoming2 = $field('grossIncoming2');
        $tareOutgoing2 = $field('tareOutgoing2');

        // Complete when both weights are captured (Container types need the second weighing too)
        if (($weightType == 'Normal' || $weightType == 'Empty Container') && ($grossIncoming != null && $tareOutgoing != null)) {
            $isComplete = 'Y';
        } elseif (($weightType == 'Container' || $weightType == 'Different Container') && ($grossIncoming != null && $tareOutgoing != null && $grossIncoming2 != null && $tareOutgoing2 != null)) {
            $isComplete = 'Y';
        } else {
            $isComplete = 'N';
        }

        $isApproved = 'Y';
        if (isset($post['status']) && $post['status'] != null && $post['status'] != '' && $post['status'] == 'pending') {
            $isComplete = 'N';
            $isApproved = 'N';
        }

        $transactionDate = null;
        if (!empty($post['transactionDate'])) {
            $dateTime = DateTime::createFromFormat('d-m-Y', $post['transactionDate']);
            if (!$dateTime) {
                throw new Exception('Invalid date');
            }
            $transactionDate = $dateTime->format('Y-m-d H:i:s');
        }

        return [
            'transaction_id' => $transactionId,
            'transaction_status' => $transactionStatus,
            'weight_type' => $weightType,
            'customer_type' => $field('customerType', 'Normal'),
            'transaction_date' => $transactionDate,
            'lorry_plate_no1' => $vehiclePlateNo1,
            'lorry_plate_no2' => $vehiclePlateNo2,
            'supplier_weight' => $field('supplierWeight'),
            'order_weight' => $field('orderWeight'),
            'customer_code' => $field('customerCode'),
            'customer_name' => $field('customerName'),
            'supplier_code' => $field('supplierCode'),
            'supplier_name' => $field('supplierName'),
            'product_code' => $field('productCode'),
            'product_name' => $field('productName'),
            'raw_mat_code' => $field('rawMaterialCode'),
            'raw_mat_name' => $field('rawMaterialName'),
            'contractor_id' => empty($post['contractorId']) ? null : intval($post['contractorId']),
            'container_no' => $field('containerNo'),
            'seal_no' => $field('sealNo'),
            'container_no2' => $field('containerNo2'),
            'seal_no2' => $field('sealNo2'),
            'invoice_no' => $field('invoiceNo'),
            'purchase_order' => $field('purchaseOrder'),
            'delivery_no' => $field('deliveryNo'),
            'transporter_code' => $field('transporterCode'),
            'transporter' => $field('transporter'),
            'destination_code' => $field('destinationCode'),
            'destination' => $field('destination'),
            'remarks' => $field('otherRemarks'),
            'gross_weight1' => $grossIncoming,
            'gross_weight1_date' => empty($post['grossIncomingDate']) ? null : $post['grossIncomingDate'],
            'gross_weight_by1' => $field('grossWeightBy1', 0),
            'tare_weight1' => $tareOutgoing,
            'tare_weight1_date' => empty($post['tareOutgoingDate']) ? null : $post['tareOutgoingDate'],
            'tare_weight_by1' => $field('tareWeightBy1', 0),
            'nett_weight1' => $field('nettWeight', 0),
            'gross_weight2' => $grossIncoming2,
            'gross_weight2_date' => empty($post['grossIncomingDate2']) ? null : $post['grossIncomingDate2'],
            'gross_weight_by2' => $field('grossWeightBy2', 0),
            'tare_weight2' => $tareOutgoing2,
            'tare_weight2_date' => empty($post['tareOutgoingDate2']) ? null : $post['tareOutgoingDate2'],
            'tare_weight_by2' => $field('tareWeightBy2', 0),
            'nett_weight2' => $field('nettWeight2'),
            'reduce_weight' => $field('reduceWeight', '0'),
            'final_weight' => $field('finalWeight', '0'),
            'weight_different' => $field('weightDifference'),
            'weight_different_perc' => $field('weightDifferencePerc'),
            'is_complete' => $isComplete,
            'is_cancel' => 'N',
            'manual_weight' => $field('manualWeight'),
            'indicator_id' => $field('indicatorId'),
            'weighbridge_id' => $field('weighbridge', 'Weigh1'),
            'location_id' => $locationId,
            'location_id2' => $locationId2,
            'created_by' => $this->username,
            'modified_by' => $this->username,
            'indicator_id_2' => $field('indicatorId2'),
            'product_description' => $field('productDescription'),
            'unit_price' => $field('unitPrice'),
            'sub_total' => $field('subTotalPrice', '0.00'),
            'sst' => $field('sstPrice', '0.00'),
            'total_price' => $field('totalPrice'),
            'is_approved' => $isApproved,
            'plant_code' => $plantCode,
            'plant_name' => $plant
        ];
    }

    private function insertWeight($values, $table = 'Weight') {
        $columns = array_keys($values);
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_fill(0, count($columns), '?')) . ")";

        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param(str_repeat('s', count($columns)), ...array_values($values));

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Something went wrong');
        }

        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    private function updateWeight($values, $weightId, $table = 'Weight') {
        $sets = implode(', ', array_map(function ($column) {
            return $column . '=?';
        }, array_keys($values)));

        $stmt = $this->prepareOrFail("UPDATE {$table} SET {$sets} WHERE id=?");
        $params = array_values($values);
        $params[] = $weightId;
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Something went wrong');
        }

        $stmt->close();
    }

    // Container with a gate entry: the empty container record follows the weighing's completion
    private function setContainerCancelled($values, $weightType, $isComplete) {
        if (empty($values['container_no']) || $weightType != 'Container') {
            return;
        }

        $this->run("UPDATE Weight_Container SET is_cancel=? WHERE container_no=? AND status='0'", 'ss', [$isComplete, $values['container_no']]);
    }

    /**
     * Cancel weighing records: Weight, or Weight_Container when isEmptyContainer = Y. One id, or several when isMulti = Y.
     * Each record's status needs the Weighing cancelled permission. Returns the response array (status, message).
     */
    public function cancel($input) {
        if (!isset($input['cancelReason'], $input['isEmptyContainer'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $cancelChecks = [];
        if (!empty($input['id'])) {
            $cancelChecks[] = [$input['isEmptyContainer'] == 'Y' ? 'Weight_Container' : 'Weight', $input['id']];
        }
        if (!empty($input['containerId'])) {
            $cancelChecks[] = ['Weight_Container', $input['containerId']];
        }

        try {
            foreach ($cancelChecks as [$table, $idValue]) {
                $ids = $this->intList($idValue);
                if (empty($ids)) continue;

                $result = $this->db->query("SELECT transaction_status FROM {$table} WHERE id IN (" . implode(',', $ids) . ")");
                if ($result === false) {
                    return ['status' => 'failed', 'message' => 'Something went wrong'];
                }

                while ($row = $result->fetch_assoc()) {
                    if (!statusPermitted('Weighing', $row['transaction_status'], 'cancelled')) {
                        return ['status' => 'failed', 'message' => 'You do not have permission to cancel this weighing record'];
                    }
                }
            }

            $reason = trim((string) $input['cancelReason']);
            $isMulti = $input['isMulti'] ?? '';
            $isEmptyContainer = $input['isEmptyContainer'];
            $id = $this->present($input, 'id') ? $input['id'] : null;
            $containerId = $this->present($input, 'containerId') ? $input['containerId'] : null;

            if ($isMulti == 'Y') {
                $statuses = [];
                $messages = [];

                if ($isEmptyContainer == 'Y') {
                    $this->cancelContainers($this->intList($input['id']), $reason);
                    return ['status' => 'success', 'message' => 'Deleted'];
                }

                if ($id !== null) {
                    $ids = $this->intList($id);
                    $this->cancelWeights($ids, $reason);
                    $statuses[] = 'success';
                    $messages[] = 'Deleted';
                }

                if ($containerId !== null) {
                    $this->cancelContainers($this->intList($containerId), $reason);
                    $statuses[] = 'success';
                    $messages[] = 'Deleted';
                }

                if (in_array('failed', $statuses, true)) {
                    return ['status' => 'failed', 'message' => $messages];
                }
                return ['status' => 'success', 'message' => 'Deleted'];
            }

            if ($isEmptyContainer == 'Y') {
                $this->cancelContainers($this->intList($id), $reason);
            } else {
                $this->cancelWeights($this->intList($id), $reason);
            }

            return ['status' => 'success', 'message' => 'Deleted'];
        } catch (Exception $e) {
            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }

    // Marks weighings cancelled, reverses their vehicle entries and refunds their deposit
    private function cancelWeights($ids, $reason) {
        if (empty($ids)) return;

        $list = implode(',', $ids);
        $this->run("UPDATE Weight SET is_complete=?, is_cancel=?, cancelled_reason=? WHERE id IN ({$list})", 'sss', ['Y', 'Y', $reason]);
        (new VehicleEntryService($this->db, $this->username))->reverse('Weight', $ids, $this->username, $reason);
        (new DepositService($this->db, $this->username))->refundWeights($ids);
    }

    // Soft deletes empty container weighings and reverses their vehicle entries
    private function cancelContainers($ids, $reason) {
        if (empty($ids)) return;

        $list = implode(',', $ids);
        $this->run("UPDATE Weight_Container SET status=?, cancelled_reason=? WHERE id IN ({$list})", 'ss', ['1', $reason]);
        (new VehicleEntryService($this->db, $this->username))->reverse('Weight_Container', $ids, $this->username, $reason);
    }

    // Ids from one value or a comma-separated list, as integers
    private function intList($value) {
        $ids = is_array($value) ? $value : explode(',', (string) $value);
        return array_values(array_filter(array_map('intval', $ids)));
    }

    private function present($input, $key) {
        return isset($input[$key]) && $input[$key] != null && $input[$key] != '';
    }

    private function run($sql, $types, $params) {
        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('WeightService - execute failed');
            throw new Exception('Something went wrong');
        }

        $stmt->close();
    }

    /**
     * Sort column and direction, paging and the DataTables search text (escaped, column whitelisted)
     */
    private function dataTableParams($post, $sortColumns) {
        $columnIndex = intval($post['order'][0]['column'] ?? 0);
        $column = $post['columns'][$columnIndex]['data'] ?? 'id';

        return [
            'draw' => intval($post['draw'] ?? 0),
            'start' => intval($post['start'] ?? 0),
            'length' => intval($post['length'] ?? 10),
            'column' => in_array($column, $sortColumns, true) ? $column : 'id',
            'dir' => strtolower($post['order'][0]['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc',
            'search' => (string) ($post['search']['value'] ?? '')
        ];
    }

    /**
     * Returns a closure that reads one filter: the value, or null when it is empty or the '-' placeholder
     */
    private function filterValues($post) {
        return function ($key) use ($post) {
            if (!isset($post[$key]) || $post[$key] == null || $post[$key] == '' || $post[$key] == '-') {
                return null;
            }
            return $post[$key];
        };
    }

    private function esc($value) {
        return mysqli_real_escape_string($this->db, (string) $value);
    }

    private function plantList($plants) {
        return implode("', '", array_map([$this, 'esc'], array_values((array) $plants)));
    }

    private function countQuery($sql) {
        $row = $this->db->query($sql)->fetch_assoc();
        return $row['allcount'];
    }

    private function prepareOrFail($sql) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log('WeightService - ' . $this->db->error);
            throw new Exception('Something went wrong');
        }

        return $stmt;
    }

    private function fetchAll($stmt) {
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Something went wrong');
        }

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    private function fetchOne($stmt) {
        $rows = $this->fetchAll($stmt);
        return $rows ? $rows[0] : null;
    }
}
