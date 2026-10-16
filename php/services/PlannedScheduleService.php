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

        $where .= " AND (v.veh_number LIKE ? OR c.name LIKE ? OR v.raw_mat_name LIKE ?)";
        $types .= 'sss';
        array_push($params, $like, $like, $like);

        $from = " FROM Vehicle_Entry_Limit l JOIN Vehicle v ON v.id = l.vehicle_id LEFT JOIN Contractor c ON c.id = v.contractor_id WHERE {$where}";

        $total = $this->db->query("SELECT COUNT(*) AS total FROM Vehicle_Entry_Limit l WHERE l.status = '0'")->fetch_assoc()['total'];

        $filteredRow = $this->fetchOne("SELECT COUNT(*) AS total" . $from, $types, $params);

        $stmt = $this->db->prepare("SELECT l.id, l.start_date, l.end_date, l.max_entry, v.veh_number, c.name AS contractor_name, v.raw_mat_name, v.vehicle_type, v.supplier_code, v.contractor_id, v.raw_mat_code" . $from . " ORDER BY l.start_date, v.veh_number LIMIT ?, ?");
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
                'contractor_id'   => $row['contractor_id'] ?? '',
                'raw_mat_code'    => $row['raw_mat_code'] ?? ''
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
            $supplierCode = trim($row['CONTRACTORCODE'] ?? '');
            $supplierName = trim($row['NAMASYARIKAT'] ?? '');
            $contractorName = trim($row['CUSTOMER'] ?? '');
            $wasteName = trim($row['WASTE'] ?? '');
            $vehicleType = trim($row['VEHICLETYPE'] ?? '');
            $trips = trim($row['JUMLAHTRIPKEPSPHARI'] ?? '');
            $statusText = strtoupper(trim($row['STATUSSYARIKAT'] ?? ''));
            $startDate = $this->parseDate(trim($row['STARTDATE'] ?? ''));
            $endDate = $this->parseDate(trim($row['ENDDATE'] ?? ''));

            // Skip rows where every cell is empty
            if ($plate === '' && $supplierCode === '' && $supplierName === '' && $contractorName === '' && $wasteName === '' && $vehicleType === '' && $trips === '' && $statusText === '') {
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

                if ($supplierCode !== null) {
                    $this->ensureSupplier($supplierCode, $supplierName);
                }
                $contractorId = $this->findOrCreateContractor($contractorName);
                $rawMatCode = $this->findOrCreateByName('Raw_Mat', 'raw_mat_code', $wasteName);

                $vehicleId = $this->saveVehicle(
                    $plate, $supplierCode, $supplierName, $contractorId,
                    $rawMatCode, $wasteName ?: null, $vehicleType ?: null, self::$statusMap[$statusText]
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
     * Find an active Contractor / Raw_Mat by name, creating it (code = name) when missing.
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
    private function saveVehicle($plate, $supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType, $status) {
        $row = $this->fetchOne("SELECT id FROM Vehicle WHERE veh_number = ? ORDER BY id DESC LIMIT 1", 's', [$plate]);

        if ($row) {
            $vehicleId = $row['id'];
            $this->run(
                "UPDATE Vehicle SET supplier_code = ?, supplier_name = ?, contractor_id = ?, raw_mat_code = ?, raw_mat_name = ?, vehicle_type = ?, status = ?, modified_by = ? WHERE id = ?",
                'sssssssss', [$supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType, $status, $this->username, $vehicleId]
            );
            return $vehicleId;
        }

        return $this->run(
            "INSERT INTO Vehicle (veh_number, vehicle_weight, supplier_code, supplier_name, contractor_id, raw_mat_code, raw_mat_name, vehicle_type, status, created_by, modified_by) VALUES (?, '0', ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'ssssssssss', [$plate, $supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType, $status, $this->username, $this->username]
        );
    }

    /**
     * Add (id null) or edit one planned schedule row from the form. Throws Exception with the message to show.
     * Supplier, contractor and waste come from dropdowns, so they must match an active master row.
     */
    public function saveRow($data, $id = null) {
        $plate = strtoupper(trim($data['vehNumber'] ?? ''));
        $supplierCode = trim($data['supplierCode'] ?? '');
        $contractorId = trim($data['contractorId'] ?? '');
        $rawMatCode = trim($data['rawMatCode'] ?? '');
        $vehicleType = trim($data['vehicleType'] ?? '');
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

        $vehicleId = $this->findOrCreateVehicle($plate, $supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType ?: null);

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
    private function findOrCreateVehicle($plate, $supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType) {
        $row = $this->fetchOne("SELECT id FROM Vehicle WHERE veh_number = ? ORDER BY id DESC LIMIT 1", 's', [$plate]);

        if ($row) {
            $this->run(
                "UPDATE Vehicle SET supplier_code = ?, supplier_name = ?, contractor_id = ?, raw_mat_code = ?, raw_mat_name = ?, vehicle_type = ?, status = '0', modified_by = ? WHERE id = ?",
                'ssssssss', [$supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType, $this->username, $row['id']]
            );
            return $row['id'];
        }

        return $this->run(
            "INSERT INTO Vehicle (veh_number, vehicle_weight, supplier_code, supplier_name, contractor_id, raw_mat_code, raw_mat_name, vehicle_type, status, created_by, modified_by) VALUES (?, '0', ?, ?, ?, ?, ?, ?, '0', ?, ?)",
            'sssssssss', [$plate, $supplierCode, $supplierName, $contractorId, $rawMatCode, $rawMatName, $vehicleType, $this->username, $this->username]
        );
    }

    /**
     * One Vehicle_Entry_Limit row per exact range. Returns 1 when a row was written, else 0.
     */
    private function saveRangeLimit($vehicleId, $plate, $startDate, $endDate, $trips) {
        $start = $startDate->format('Y-m-d');
        $end = $endDate->format('Y-m-d');
        $existing = $this->fetchOne("SELECT id, max_entry FROM Vehicle_Entry_Limit WHERE vehicle_id = ? AND start_date = ? AND end_date = ? AND status = '0' ORDER BY id DESC LIMIT 1", 'sss', [$vehicleId, $start, $end]);

        if (!$existing) {
            $this->run("INSERT INTO Vehicle_Entry_Limit (vehicle_id, veh_number, start_date, end_date, max_entry, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?)", 'sssssss', [$vehicleId, $plate, $start, $end, $trips, $this->username, $this->username]);
            return 1;
        }

        if ((int)$existing['max_entry'] !== (int)$trips) {
            $this->run("UPDATE Vehicle_Entry_Limit SET max_entry = ?, modified_by = ? WHERE id = ?", 'sss', [$trips, $this->username, $existing['id']]);
            return 1;
        }

        return 0;
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
