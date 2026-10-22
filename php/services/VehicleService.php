<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../requires/lookup.php';
require_once __DIR__ . '/VehicleEntryService.php';

class VehicleService extends BaseService {

    protected $table = 'Vehicle';

    // Columns the vehicle list can be sorted by (DataTables sends the column name)
    private $sortColumns = ['id', 'veh_number', 'vehicle_weight', 'vehicle_type', 'customer_name', 'supplier_name', 'status'];

    /**
     * Insert a vehicle, or update it when an id is posted
     */
    public function save($post) {
        $vehicleId = empty($post['id']) ? null : trim($post['id']);
        $vehicleNo = empty($post['vehicleNo']) ? null : trim($post['vehicleNo']);
        $vehicleWeight = empty($post['vehicleWeight']) ? 0 : trim($post['vehicleWeight']);
        $transporter = empty($post['transporter']) ? null : trim($post['transporter']);
        $transporterCode = empty($post['transporterCode']) ? null : trim($post['transporterCode']);
        $customer = empty($post['customer']) ? null : trim($post['customer']);
        $customerCode = empty($post['customerCode']) ? null : trim($post['customerCode']);
        $supplier = empty($post['supplier']) ? null : trim($post['supplier']);
        $supplierCode = empty($post['supplierCode']) ? null : trim($post['supplierCode']);
        $vehicleType = empty($post['vehicleType']) ? null : trim($post['vehicleType']);
        $contractorId = empty($post['contractorId']) ? null : intval($post['contractorId']);
        $binNo = empty($post['binNo']) ? null : trim($post['binNo']);
        $binType = empty($post['binType']) ? null : trim($post['binType']);

        if (!empty($vehicleId)) {
            $sql = "UPDATE Vehicle SET veh_number=?, vehicle_weight=?, transporter_code=?, transporter_name=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?, vehicle_type=?, contractor_id=?, bin_no=?, bin_type=?, created_by=?, modified_by=? WHERE id=?";
            $params = [$vehicleNo, $vehicleWeight, $transporterCode, $transporter, $customerCode, $customer, $supplierCode, $supplier, $vehicleType, $contractorId, $binNo, $binType, $this->username, $this->username, $vehicleId];
        } else {
            $sql = "INSERT INTO Vehicle (veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, vehicle_type, contractor_id, bin_no, bin_type, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$vehicleNo, $vehicleWeight, $transporterCode, $transporter, $customerCode, $customer, $supplierCode, $supplier, $vehicleType, $contractorId, $binNo, $binType, $this->username, $this->username];
        }

        $this->execute($sql, $params);
    }

    /**
     * One active vehicle by id (default), by plate number for the pending lookup, or for the weighing screen pull (pullCustomer)
     */
    public function get($key, $type) {
        if ($type == 'lookup') {
            return $this->getLookup($key);
        }

        if ($type == 'pullCustomer') {
            $stmt = $this->prepareOrFail("SELECT v.*, c.name AS contractor_name FROM Vehicle v LEFT JOIN Contractor c ON c.id = v.contractor_id WHERE v.veh_number=? AND v.status='0'");
            $stmt->bind_param('s', $key);
            $row = $this->fetchOne($stmt);

            if (!$row) {
                return [];
            }

            $message = $this->vehicleRow($row);
            $message['contractor_name'] = $row['contractor_name'] ?? '';
            $message['raw_mat_code'] = $row['raw_mat_code'] ?? null;
            $message['raw_mat_name'] = $row['raw_mat_name'] ?? null;
            return $message;
        }

        $stmt = $this->prepareOrFail("SELECT * FROM Vehicle WHERE id=? AND status='0'");
        $stmt->bind_param('s', $key);
        $row = $this->fetchOne($stmt);

        return $row ? $this->vehicleRow($row) : [];
    }

    /**
     * Legacy lookup: a plate with a pending weighing returns an error, otherwise the active vehicle
     */
    private function getLookup($plateNo) {
        $stmt = $this->prepareOrFail("SELECT COUNT(*) AS COUNT FROM `Weight` WHERE lorry_plate_no1 = ? AND is_complete='N'");
        $stmt->bind_param('s', $plateNo);
        $pending = $this->fetchOne($stmt);

        if (!empty($pending) && $pending['COUNT'] > 0) {
            return ['error' => true, 'message' => 'There is a pending record for this vehicle'];
        }

        $stmt = $this->prepareOrFail("SELECT * FROM Vehicle WHERE veh_number=? AND status='0'");
        $stmt->bind_param('s', $plateNo);
        $row = $this->fetchOne($stmt);

        return $row ? $this->vehicleRow($row) : [];
    }

    /**
     * DataTables page of vehicles (status 0 and 1, sorted by status first)
     */
    public function list($post) {
        $draw = intval($post['draw'] ?? 0);
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);

        $columnIndex = intval($post['order'][0]['column'] ?? 0);
        $columnName = $post['columns'][$columnIndex]['data'] ?? 'id';
        $columnName = in_array($columnName, $this->sortColumns, true) ? $columnName : 'id';
        $sortOrder = strtolower($post['order'][0]['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $searchValue = mysqli_real_escape_string($this->db, $post['search']['value'] ?? '');
        $searchQuery = '';
        if ($searchValue != '') {
            $searchQuery = " and (veh_number like '%{$searchValue}%'
                or vehicle_weight like '%{$searchValue}%'
                or transporter_code like '%{$searchValue}%'
                or customer_name like '%{$searchValue}%'
                or supplier_name like '%{$searchValue}%')";
        }

        $totalRecords = $this->countQuery("select count(*) as allcount from Vehicle");
        $totalFiltered = $this->countQuery("select count(*) as allcount from Vehicle WHERE status IN (0,1)" . $searchQuery);

        $result = $this->db->query("select * from Vehicle WHERE status IN (0,1)" . $searchQuery . " order by status ASC, {$columnName} {$sortOrder} limit {$start},{$length}");
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['id'],
                'veh_number' => $row['veh_number'],
                'vehicle_weight' => $row['vehicle_weight'],
                'vehicle_type' => $row['vehicle_type'],
                'transporter_name' => $row['transporter_name'],
                'customer_name' => $row['customer_name'],
                'supplier_name' => $row['supplier_name'],
                'status' => $row['status'] == '0' ? 'Active' : 'Inactive'
            ];
        }

        return [
            'draw' => $draw,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data
        ];
    }

    /**
     * Soft delete one or more vehicles (status 1)
     */
    public function delete($ids) {
        $ids = array_values(array_filter(array_map('intval', $ids)));

        if (empty($ids)) {
            throw new Exception('Please fill in all the fields');
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge(['1'], $ids);
        $this->execute("UPDATE Vehicle SET status=? WHERE id IN ({$placeholders})", $params);
    }

    /**
     * Import vehicles from the Excel preview. Returns the row messages (empty when every row saved).
     */
    public function upload($rows) {
        $errors = [];
        $status = '0';
        $uid = $this->username;

        foreach ($rows as $row) {
            $vehicleNo = !empty($row['VehicleNo']) ? trim($row['VehicleNo']) : '';
            $vehicleWeight = !empty($row['VehicleWeightKG']) ? trim($row['VehicleWeightKG']) : '';
            $vehicleType = !empty($row['VehicleType']) ? trim($row['VehicleType']) : null;
            $customerCode = null;
            $customerName = null;
            $supplierCode = null;
            $supplierName = null;

            $customerInput = !empty($row['CustomerCode']) ? trim($row['CustomerCode']) : '';
            if ($customerInput != '') {
                $customerRow = searchCustomerByCode($customerInput, $this->db);

                if (empty($customerRow)) {
                    $errors[] = "Customer: " . $customerInput . " doesn't exist in master data.";
                    continue;
                }

                $customerCode = $customerRow['customer_code'];
                $customerName = $customerRow['name'];
            }

            $supplierInput = !empty($row['SupplierCode']) ? trim($row['SupplierCode']) : '';
            if ($supplierInput != '') {
                $supplierRow = searchSupplierByCode($supplierInput, $this->db);

                if (empty($supplierRow)) {
                    $errors[] = "Supplier: " . $supplierInput . " doesn't exist in master data.";
                    continue;
                }

                $supplierCode = $supplierRow['supplier_code'];
                $supplierName = $supplierRow['name'];
            }

            if ($vehicleNo == '') {
                continue;
            }

            $check = $this->prepareOrFail("SELECT id FROM Vehicle WHERE veh_number = ? AND status = ?");
            $check->bind_param('ss', $vehicleNo, $status);
            $check->execute();
            $vehRow = $check->get_result()->fetch_assoc();
            $check->close();

            if (empty($vehRow)) {
                $this->execute(
                    "INSERT INTO Vehicle (veh_number, vehicle_weight, customer_code, customer_name, supplier_code, supplier_name, vehicle_type, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$vehicleNo, $vehicleWeight, $customerCode, $customerName, $supplierCode, $supplierName, $vehicleType, $status, $uid, $uid]
                );
            } else {
                $this->execute(
                    "UPDATE Vehicle SET vehicle_weight = ?, customer_code = ?, customer_name = ?, supplier_code = ?, supplier_name = ?, vehicle_type = ?, modified_by = ? WHERE id = ?",
                    [$vehicleWeight, $customerCode, $customerName, $supplierCode, $supplierName, $vehicleType, $uid, $vehRow['id']]
                );
            }
        }

        return $errors;
    }

    /**
     * Daily entry status for the weighing screen. $dateInput is d-m-Y (optional, defaults to today).
     */
    public function getEntryStatus($vehicleNo, $dateInput) {
        $date = date('Y-m-d');

        if (!empty($dateInput)) {
            $dateTime = DateTime::createFromFormat('d-m-Y', trim($dateInput));

            if (!$dateTime) {
                throw new Exception('Invalid date');
            }

            $date = $dateTime->format('Y-m-d');
        }

        $entryStatus = (new VehicleEntryService($this->db, $this->username))->status($vehicleNo, $date);

        if (empty($entryStatus)) {
            return ['limited' => false, 'exceeded' => false, 'max' => 0, 'count' => 0];
        }

        return [
            'limited' => $entryStatus['limited'],
            'exceeded' => $entryStatus['exceeded'],
            'max' => $entryStatus['max'],
            'count' => $entryStatus['count']
        ];
    }

    /**
     * Re-confirm the logged-in user's password (insufficient entry override)
     */
    public function verifyPassword($userId, $password) {
        return (new VehicleEntryService($this->db, $this->username))->verifyPassword($userId, $password);
    }

    private function vehicleRow($row) {
        return [
            'id' => $row['id'],
            'veh_number' => $row['veh_number'],
            'vehicle_weight' => $row['vehicle_weight'],
            'transporter_name' => $row['transporter_name'],
            'transporter_code' => $row['transporter_code'],
            'customer_code' => $row['customer_code'],
            'customer_name' => $row['customer_name'],
            'supplier_code' => $row['supplier_code'],
            'supplier_name' => $row['supplier_name'],
            'vehicle_type' => $row['vehicle_type'],
            'contractor_id' => $row['contractor_id'],
            'bin_no' => $row['bin_no'],
            'bin_type' => $row['bin_type']
        ];
    }

    private function countQuery($sql) {
        $row = $this->db->query($sql)->fetch_assoc();
        return $row['allcount'];
    }

    private function prepareOrFail($sql) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log('VehicleService - ' . $this->db->error);
            throw new Exception('Something went wrong');
        }

        return $stmt;
    }

    private function fetchOne($stmt) {
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Something went wrong');
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    private function execute($sql, $params) {
        $stmt = $this->prepareOrFail($sql);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('VehicleService - execute failed');
            throw new Exception('Something went wrong');
        }

        $stmt->close();
    }
}
