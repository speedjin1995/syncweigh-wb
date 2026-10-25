<?php
require_once __DIR__ . '/BaseService.php';

class PlannedScheduleService extends BaseService {

    // Excel STATUS SYARIKAT text => Vehicle.status
    private static $statusMap = ['AKTIF' => '0', 'TIDAK AKTIF' => '1'];

    /**
     * Limit ranges for the planned schedule list (for DataTables).
     * No date filter: ranges ending today or later. With a date filter: ranges overlapping that span, past dates included.
     */
    public function getAll($post) {
        $draw = $post['draw'] ?? 1;
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);
        $like = '%' . trim($post['search']['value'] ?? '') . '%';
        $vehicleNo = trim($post['vehicleNo'] ?? '');
        $fromDate = $this->parseFilterDate($post['fromDate'] ?? '');
        $toDate = $this->parseFilterDate($post['toDate'] ?? '');

        $where = "l.status = '0'";
        $types = '';
        $params = [];

        if ($fromDate === null && $toDate === null) {
            $where .= " AND l.end_date >= ?";
            $types .= 's';
            $params[] = date('Y-m-d');
        } else {
            if ($fromDate !== null) {
                $where .= " AND l.end_date >= ?";
                $types .= 's';
                $params[] = $fromDate;
            }
            if ($toDate !== null) {
                $where .= " AND l.start_date <= ?";
                $types .= 's';
                $params[] = $toDate;
            }
        }

        if ($vehicleNo !== '') {
            $where .= " AND v.veh_number = ?";
            $types .= 's';
            $params[] = $vehicleNo;
        }

        $where .= " AND (v.veh_number LIKE ? OR v.supplier_name LIKE ? OR c.name LIKE ? OR v.customer_name LIKE ? OR v.raw_mat_name LIKE ? OR v.product_name LIKE ?)";
        $types .= 'ssssss';
        array_push($params, $like, $like, $like, $like, $like, $like);

        $from = " FROM Vehicle_Entry_Limit l JOIN Vehicle v ON v.id = l.vehicle_id LEFT JOIN Contractor c ON c.id = v.contractor_id WHERE {$where}";

        $total = $this->db->query("SELECT COUNT(*) AS total FROM Vehicle_Entry_Limit l WHERE l.status = '0'")->fetch_assoc()['total'];

        $filteredRow = $this->fetchOne("SELECT COUNT(*) AS total" . $from, $types, $params);

        $stmt = $this->db->prepare("SELECT l.id, l.start_date, l.end_date, l.max_entry, v.veh_number, c.name AS contractor_name, v.raw_mat_name, v.vehicle_type, v.supplier_code, v.supplier_name, v.customer_code, v.customer_name, v.contractor_id, v.raw_mat_code, v.product_code, v.product_name, v.bin_no, v.bin_type" . $from . " ORDER BY l.start_date, v.veh_number LIMIT ?, ?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param($types . 'ii', ...array_merge($params, [$start, $length]));
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id'              => $row['id'],
                'start_date'      => date('d/m/Y', strtotime($row['start_date'])),
                'end_date'        => date('d/m/Y', strtotime($row['end_date'])),
                'veh_number'      => $row['veh_number'],
                'contractor_name' => $row['contractor_name'] ?? '',
                'raw_mat_name'    => $row['raw_mat_name'] ?? '',
                'vehicle_type'    => $row['vehicle_type'] ?? '',
                'max_entry'       => $row['max_entry'],
                'supplier_code'   => $row['supplier_code'] ?? '',
                'supplier_name'   => $row['supplier_name'] ?? '',
                'customer_code'   => $row['customer_code'] ?? '',
                'customer_name'   => $row['customer_name'] ?? '',
                'contractor_id'   => $row['contractor_id'] ?? '',
                'raw_mat_code'    => $row['raw_mat_code'] ?? '',
                'product_code'    => $row['product_code'] ?? '',
                'product_name'    => $row['product_name'] ?? '',
                'bin_no'          => $row['bin_no'] ?? '',
                'bin_type'        => $row['bin_type'] ?? ''
            ];
        }
        $stmt->close();

        return [
            'draw'                  => intval($draw),
            'iTotalRecords'         => $total,
            'iTotalDisplayRecords'  => $filteredRow['total'],
            'aaData'                => $data
        ];
    }

    /**
     * Filter date from the search box (d-m-Y, as index.php uses) as Y-m-d, or null when blank / invalid
     */
    private function parseFilterDate($text) {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $date = DateTime::createFromFormat('d-m-Y', $text);
        if (!$date || $date->format('d-m-Y') !== $text) {
            return null;
        }
        return $date->format('Y-m-d');
    }

    /**
     * Save Excel rows. Invalid rows are skipped and reported in 'errors'.
     * Returns ['errors' => [...], 'vehicles' => n, 'ranges' => n]
     */
    public function importRows($rows) {
        $errors = [];
        $vehicles = 0;
        $ranges = 0;

        foreach ($rows as $index => $row) {
            $line = $row['ExcelRow'] ?? ($index + 2); // Excel row number from the preview, else position + header
            $plate = trim($row['NOPENDAFTARANKENDERAAN'] ?? '');
            $supplierCode = trim($row['SUPPLIERCODE'] ?? '');
            $supplierName = trim($row['SUPPLIERNAME'] ?? '');
            $contractorName = trim($row['CONTRACTORNAME'] ?? '');
            $customerCode = trim($row['CUSTOMERCODE'] ?? '');
            $customerName = trim($row['CUSTOMERNAME'] ?? '');
            $wasteName = trim($row['WASTE'] ?? '');
            $productName = trim($row['PRODUCT'] ?? '');
            $binNo = trim($row['BINNO'] ?? '');
            $binType = trim($row['BINTYPE'] ?? '');
            $vehicleType = trim($row['VEHICLETYPE'] ?? '');
            $trips = trim($row['JUMLAHTRIPKEPSPHARI'] ?? '');
            $statusText = strtoupper(trim($row['STATUSSYARIKAT'] ?? ''));
            $startDate = $this->parseDate(trim($row['STARTDATE'] ?? ''));
            $endDate = $this->parseDate(trim($row['ENDDATE'] ?? ''));

            // Skip rows where every cell is empty
            if ($plate === '' && $supplierCode === '' && $supplierName === '' && $contractorName === '' && $customerCode === '' && $customerName === '' && $wasteName === '' && $productName === '' && $vehicleType === '' && $binNo === '' && $binType === '' && $trips === '' && $statusText === '') {
                continue;
            }

            if ($plate === '') {
                $errors[] = "Row {$line}: vehicle plate is required.";
            } elseif (!ctype_digit($trips)) {
                $errors[] = "Row {$line}: trips per day must be a whole number, 0 or more.";
            } elseif (!isset(self::$statusMap[$statusText])) {
                $errors[] = "Row {$line}: status must be AKTIF or TIDAK AKTIF.";
            } elseif ($startDate === null || $endDate === null) {
                $errors[] = "Row {$line}: start and end date must be dd/mm/yyyy.";
            } elseif ($startDate > $endDate) {
                $errors[] = "Row {$line}: start date is after end date.";
            } elseif ($startDate->diff($endDate)->days > 365) {
                $errors[] = "Row {$line}: date range is longer than 366 days.";
            } else {
                $supplierCode = $supplierCode !== '' ? $supplierCode : null;
                $supplierName = $supplierName !== '' ? $supplierName : null;

                $customerCode = $customerCode !== '' ? $customerCode : null;
                $customerName = $customerName !== '' ? $customerName : null;

                if ($supplierCode !== null) {
                    $this->ensureSupplier($supplierCode, $supplierName);
                }
                if ($customerCode !== null) {
                    $this->ensureCustomer($customerCode, $customerName);
                }
                $contractorId = $this->findOrCreateContractor($contractorName);
                $rawMatCode = $this->findOrCreateByName('Raw_Mat', 'raw_mat_code', $wasteName);
                $productCode = $this->findOrCreateByName('Product', 'product_code', $productName);
                $this->findOrCreateByName('Bin_No', 'bin_no_code', $binNo);
                $this->findOrCreateByName('Bin_Type', 'bin_type_code', $binType);

                $vehicleId = $this->saveVehicle(
                    $plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId,
                    $rawMatCode, $wasteName ?: null, $productCode, $productName ?: null, $vehicleType ?: null, self::$statusMap[$statusText],
                    $binNo ?: null, $binType ?: null
                );

                $vehicles++;
                $ranges += $this->saveRangeLimit($vehicleId, $plate, $startDate, $endDate, $trips);
            }
        }

        return ['errors' => $errors, 'vehicles' => $vehicles, 'ranges' => $ranges];
    }

    /**
     * Start/end date from dd/mm/yyyy, or null when invalid
     */
    private function parseDate($text) {
        $date = DateTime::createFromFormat('d/m/Y', $text);
        if (!$date || $date->format('d/m/Y') !== $text) {
            return null;
        }
        return $date->setTime(0, 0);
    }

    /**
     * Create the supplier when its code is new. An existing supplier is never changed.
     */
    private function ensureSupplier($code, $name) {
        $row = $this->fetchOne("SELECT id FROM Supplier WHERE supplier_code = ? AND status = '0' LIMIT 1", 's', [$code]);
        if (!$row) {
            $this->run("INSERT INTO Supplier (supplier_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)", 'ssss', [$code, $name, $this->username, $this->username]);
        }
    }

    /**
     * Create the customer when its code is new. An existing customer is never changed.
     */
    private function ensureCustomer($code, $name) {
        $row = $this->fetchOne("SELECT id FROM Customer WHERE customer_code = ? AND status = '0' LIMIT 1", 's', [$code]);
        if (!$row) {
            $this->run("INSERT INTO Customer (customer_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)", 'ssss', [$code, $name, $this->username, $this->username]);
        }
    }

    /**
     * Find an active Raw_Mat / Product / Bin master by name, creating it (code = name) when missing.
     * Returns the code, or null for a blank name.
     */
    private function findOrCreateByName($table, $codeColumn, $name) {
        if ($name === '') {
            return null;
        }

        $row = $this->fetchOne("SELECT {$codeColumn} AS code FROM {$table} WHERE name = ? AND status = '0' LIMIT 1", 's', [$name]);
        if ($row) {
            return $row['code'];
        }

        $code = mb_substr($name, 0, 50);
        $this->run("INSERT INTO {$table} ({$codeColumn}, name, created_by, modified_by) VALUES (?, ?, ?, ?)", 'ssss', [$code, $name, $this->username, $this->username]);
        return $code;
    }

    /**
     * Contractor id by name, creating the contractor (code = name) when missing. Null for a blank name.
     */
    private function findOrCreateContractor($name) {
        if ($name === '') {
            return null;
        }

        $row = $this->fetchOne("SELECT id FROM Contractor WHERE name = ? AND status = '0' LIMIT 1", 's', [$name]);
        if ($row) {
            return (int)$row['id'];
        }

        return (int)$this->run("INSERT INTO Contractor (contractor_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)", 'ssss', [mb_substr($name, 0, 50), $name, $this->username, $this->username]);
    }

    /**
     * Update the vehicle with this plate, or insert it. Returns the vehicle id.
     */
    private function saveVehicle($plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType, $status, $binNo, $binType) {
        $current = $this->fetchOne("SELECT id, supplier_code, supplier_name, customer_code, customer_name, contractor_id, raw_mat_code, raw_mat_name, product_code, product_name, vehicle_type, status, bin_no, bin_type FROM Vehicle WHERE veh_number = ? ORDER BY id DESC LIMIT 1", 's', [$plate]);

        if ($current) {
            // A blank Excel cell (null here) keeps the value already on the vehicle
            $values = [
                'supplier_code' => $supplierCode ?? $current['supplier_code'],
                'supplier_name' => $supplierName ?? $current['supplier_name'],
                'customer_code' => $customerCode ?? $current['customer_code'],
                'customer_name' => $customerName ?? $current['customer_name'],
                'contractor_id' => $contractorId ?? $current['contractor_id'],
                'raw_mat_code'  => $rawMatCode ?? $current['raw_mat_code'],
                'raw_mat_name'  => $rawMatName ?? $current['raw_mat_name'],
                'product_code'  => $productCode ?? $current['product_code'],
                'product_name'  => $productName ?? $current['product_name'],
                'vehicle_type'  => $vehicleType ?? $current['vehicle_type'],
                'status'        => $status,
                'bin_no'        => $binNo ?? $current['bin_no'],
                'bin_type'      => $binType ?? $current['bin_type'],
            ];

            // Skip the UPDATE (and its Vehicle_Log row) when nothing changed
            $changed = false;
            foreach ($values as $column => $value) {
                if ((string)$value !== (string)$current[$column]) {
                    $changed = true;
                    break;
                }
            }
            if (!$changed) {
                return $current['id'];
            }

            $sets = implode(', ', array_map(fn($column) => "$column = ?", array_keys($values)));
            $this->run(
                "UPDATE Vehicle SET {$sets}, modified_by = ? WHERE id = ?",
                str_repeat('s', count($values)) . 'ss', array_merge(array_values($values), [$this->username, $current['id']])
            );
            return $current['id'];
        }

        return $this->run(
            "INSERT INTO Vehicle (veh_number, vehicle_weight, supplier_code, supplier_name, customer_code, customer_name, contractor_id, raw_mat_code, raw_mat_name, product_code, product_name, vehicle_type, status, bin_no, bin_type, created_by, modified_by) VALUES (?, '0', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'ssssssssssssssss', [$plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType, $status, $binNo, $binType, $this->username, $this->username]
        );
    }

    /**
     * Add (id null) or edit one planned schedule row from the form. Throws Exception with the message to show.
     * Supplier, contractor and waste come from dropdowns, so they must match an active master row.
     */
    public function saveRow($data, $id = null) {
        $plate = strtoupper(trim($data['vehNumber'] ?? ''));
        $supplierCode = trim($data['supplierCode'] ?? '');
        $customerCode = trim($data['customerCode'] ?? '');
        $contractorId = trim($data['contractorId'] ?? '');
        $rawMatCode = trim($data['rawMatCode'] ?? '');
        $productCode = trim($data['productCode'] ?? '');
        $vehicleType = trim($data['vehicleType'] ?? '');
        $binNo = trim($data['binNo'] ?? '');
        $binType = trim($data['binType'] ?? '');
        $trips = trim($data['trips'] ?? '');
        $startDate = $this->parseDate(trim($data['startDate'] ?? ''));
        $endDate = $this->parseDate(trim($data['endDate'] ?? ''));

        if ($plate === '') {
            throw new Exception('Vehicle plate is required.');
        }
        if (!ctype_digit($trips)) {
            throw new Exception('Trips per day must be a whole number, 0 or more.');
        }
        if ($startDate === null || $endDate === null) {
            throw new Exception('Start and end date must be dd/mm/yyyy.');
        }
        if ($startDate > $endDate) {
            throw new Exception('Start date is after end date.');
        }
        if ($startDate->diff($endDate)->days > 365) {
            throw new Exception('Date range is longer than 366 days.');
        }

        $supplierName = null;
        if ($supplierCode !== '') {
            $supplier = $this->fetchOne("SELECT name FROM Supplier WHERE supplier_code = ? AND status = '0' LIMIT 1", 's', [$supplierCode]);
            if (!$supplier) {
                throw new Exception('Supplier not found.');
            }
            $supplierName = $supplier['name'];
        }
        $supplierCode = $supplierCode !== '' ? $supplierCode : null;

        $customerName = null;
        if ($customerCode !== '') {
            $customer = $this->fetchOne("SELECT name FROM Customer WHERE customer_code = ? AND status = '0' LIMIT 1", 's', [$customerCode]);
            if (!$customer) {
                throw new Exception('Customer not found.');
            }
            $customerName = $customer['name'];
        }
        $customerCode = $customerCode !== '' ? $customerCode : null;

        if ($contractorId !== '') {
            if (!ctype_digit($contractorId) || !$this->fetchOne("SELECT id FROM Contractor WHERE id = ? AND status = '0' LIMIT 1", 's', [$contractorId])) {
                throw new Exception('Contractor not found.');
            }
        }
        $contractorId = $contractorId !== '' ? $contractorId : null;

        $rawMatName = null;
        if ($rawMatCode !== '') {
            $rawMat = $this->fetchOne("SELECT name FROM Raw_Mat WHERE raw_mat_code = ? AND status = '0' LIMIT 1", 's', [$rawMatCode]);
            if (!$rawMat) {
                throw new Exception('Waste not found.');
            }
            $rawMatName = $rawMat['name'];
        }
        $rawMatCode = $rawMatCode !== '' ? $rawMatCode : null;

        $productName = null;
        if ($productCode !== '') {
            $product = $this->fetchOne("SELECT name FROM Product WHERE product_code = ? AND status = '0' LIMIT 1", 's', [$productCode]);
            if (!$product) {
                throw new Exception('Product not found.');
            }
            $productName = $product['name'];
        }
        $productCode = $productCode !== '' ? $productCode : null;

        $binNoName = null;
        if ($binNo !== '') {
            if (!$this->fetchOne("SELECT name FROM Bin_No WHERE name = ? AND status = '0' LIMIT 1", 's', [$binNo])) {
                throw new Exception('Bin No not found.');
            }
            $binNoName = $binNo;
        }

        $binTypeName = null;
        if ($binType !== '') {
            if (!$this->fetchOne("SELECT name FROM Bin_Type WHERE name = ? AND status = '0' LIMIT 1", 's', [$binType])) {
                throw new Exception('Bin Type not found.');
            }
            $binTypeName = $binType;
        }

        $vehicleId = $this->findOrCreateVehicle($plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType ?: null, $binNoName, $binTypeName);

        if ($id === null) {
            $this->saveRangeLimit($vehicleId, $plate, $startDate, $endDate, $trips);
            return;
        }

        $existing = $this->fetchOne("SELECT id FROM Vehicle_Entry_Limit WHERE id = ? AND status = '0' LIMIT 1", 's', [$id]);
        if (!$existing) {
            throw new Exception('Planned schedule row not found.');
        }
        $this->run(
            "UPDATE Vehicle_Entry_Limit SET vehicle_id = ?, veh_number = ?, start_date = ?, end_date = ?, max_entry = ?, modified_by = ? WHERE id = ?",
            'sssssss', [$vehicleId, $plate, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $trips, $this->username, $id]
        );
    }

    /**
     * Soft delete (status 1) one planned schedule row. The trigger writes the log row.
     */
    public function deactivate($id) {
        $existing = $this->fetchOne("SELECT id FROM Vehicle_Entry_Limit WHERE id = ? AND status = '0' LIMIT 1", 's', [$id]);
        if (!$existing) {
            throw new Exception('Planned schedule row not found.');
        }
        $this->run("UPDATE Vehicle_Entry_Limit SET status = '1', modified_by = ? WHERE id = ?", 'ss', [$this->username, $id]);
    }

    /**
     * Change log of one planned schedule row, oldest first (for the View Log popup)
     */
    public function getLogs($id) {
        $stmt = $this->db->prepare("SELECT action_id, start_date, end_date, max_entry, action_by, event_date FROM Vehicle_Entry_Limit_Log WHERE limit_id = ? ORDER BY id ASC");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = [
                'action'     => (int)$row['action_id'],
                'start_date' => date('d/m/Y', strtotime($row['start_date'])),
                'end_date'   => date('d/m/Y', strtotime($row['end_date'])),
                'max_entry'  => $row['max_entry'],
                'action_by'  => $row['action_by'] ?? '',
                'event_date' => date('d/m/Y H:i:s', strtotime($row['event_date']))
            ];
        }
        $stmt->close();

        return $logs;
    }

    /**
     * Vehicle id by plate. A found vehicle is reactivated (status 0) even if it was deleted; a new one is created as active.
     */
    private function findOrCreateVehicle($plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType, $binNo, $binType) {
        $row = $this->fetchOne("SELECT id FROM Vehicle WHERE veh_number = ? ORDER BY id DESC LIMIT 1", 's', [$plate]);

        if ($row) {
            $this->run(
                "UPDATE Vehicle SET supplier_code = ?, supplier_name = ?, customer_code = ?, customer_name = ?, contractor_id = ?, raw_mat_code = ?, raw_mat_name = ?, product_code = ?, product_name = ?, vehicle_type = ?, bin_no = ?, bin_type = ?, status = '0', modified_by = ? WHERE id = ?",
                'ssssssssssssss', [$supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType, $binNo, $binType, $this->username, $row['id']]
            );
            return $row['id'];
        }

        return $this->run(
            "INSERT INTO Vehicle (veh_number, vehicle_weight, supplier_code, supplier_name, customer_code, customer_name, contractor_id, raw_mat_code, raw_mat_name, product_code, product_name, vehicle_type, bin_no, bin_type, status, created_by, modified_by) VALUES (?, '0', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?, ?)",
            'sssssssssssssss', [$plate, $supplierCode, $supplierName, $customerCode, $customerName, $contractorId, $rawMatCode, $rawMatName, $productCode, $productName, $vehicleType, $binNo, $binType, $this->username, $this->username]
        );
    }

    /**
     * One Vehicle_Entry_Limit row per exact range. Returns 1 when a row was written, else 0.
     */
    private function saveRangeLimit($vehicleId, $plate, $startDate, $endDate, $trips) {
        $start = $startDate->format('Y-m-d');
        $end = $endDate->format('Y-m-d');
        // Widen the range until no active row overlaps it, so chains of overlapping rows merge too
        $newStart = $start;
        $newEnd = $end;
        do {
            $before = [$newStart, $newEnd];
            $overlaps = $this->fetchAll("SELECT id, start_date, end_date, max_entry FROM Vehicle_Entry_Limit WHERE vehicle_id = ? AND status = '0' AND start_date <= ? AND end_date >= ? ORDER BY start_date, id", 'sss', [$vehicleId, $newEnd, $newStart]);
            foreach ($overlaps as $overlap) {
                $newStart = min($newStart, $overlap['start_date']);
                $newEnd = max($newEnd, $overlap['end_date']);
            }
        } while ([$newStart, $newEnd] !== $before);

        if (!$overlaps) {
            $this->run("INSERT INTO Vehicle_Entry_Limit (vehicle_id, veh_number, start_date, end_date, max_entry, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?)", 'sssssss', [$vehicleId, $plate, $start, $end, $trips, $this->username, $this->username]);
            return 1;
        }

        // A row with the same range keeps its trips replaced; any other overlapping row adds its trips
        $keep = $overlaps[0];
        foreach ($overlaps as $overlap) {
            if ($overlap['start_date'] === $start && $overlap['end_date'] === $end) {
                $keep = $overlap;
                break;
            }
        }
        $sameRange = $keep['start_date'] === $start && $keep['end_date'] === $end;

        $total = (int)$trips;
        foreach ($overlaps as $overlap) {
            if (!($sameRange && $overlap['id'] === $keep['id'])) {
                $total += (int)$overlap['max_entry'];
            }
        }

        if (count($overlaps) === 1 && $sameRange && $total === (int)$keep['max_entry']) {
            return 0;
        }

        $this->run("UPDATE Vehicle_Entry_Limit SET start_date = ?, end_date = ?, max_entry = ?, modified_by = ? WHERE id = ?", 'sssss', [$newStart, $newEnd, $total, $this->username, $keep['id']]);
        foreach ($overlaps as $overlap) {
            if ($overlap['id'] !== $keep['id']) {
                $this->run("UPDATE Vehicle_Entry_Limit SET status = '1', modified_by = ? WHERE id = ?", 'ss', [$this->username, $overlap['id']]);
            }
        }
        return 1;
    }

    private function fetchAll($sql, $types, $params) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    private function fetchOne($sql, $types, $params) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function run($sql, $types, $params) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $insertId = $stmt->insert_id;
        $stmt->close();
        return $insertId;
    }
}
?>
