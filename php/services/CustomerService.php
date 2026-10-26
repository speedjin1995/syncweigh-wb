<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/DepositService.php';

class CustomerService extends BaseService {

    protected $table = 'Customer';

    /**
     * Get all customers (for DataTables)
     */
    public function getAll($post) {
        $draw = $post['draw'] ?? 1;
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);
        $searchValue = isset($post['search']['value']) ? mysqli_real_escape_string($this->db, $post['search']['value']) : '';

        $sortableColumns = ['id', 'customer_code', 'name', 'company_reg_no', 'new_reg_no', 'deposit_balance', 'deposit_status', 'min_deposit_balance'];
        $columnIndex = $post['order'][0]['column'] ?? 0;
        $columnName = $post['columns'][$columnIndex]['data'] ?? 'id';
        if (!in_array($columnName, $sortableColumns)) {
            $columnName = 'id';
        }
        $columnSortOrder = (isset($post['order'][0]['dir']) && $post['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';

        // Total records
        $totalResult = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status IN ('0','1')");
        $totalRecords = $totalResult->fetch_assoc()['total'];

        // Search (DataTables search box)
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND (customer_code LIKE '%{$searchValue}%' OR name LIKE '%{$searchValue}%' OR company_reg_no LIKE '%{$searchValue}%' OR new_reg_no LIKE '%{$searchValue}%' OR contact_name LIKE '%{$searchValue}%')";
        }

        // Filtered records
        $filteredResult = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status IN ('0','1') {$searchQuery}");
        $totalFiltered = $filteredResult->fetch_assoc()['total'];

        // Data
        $dataResult = $this->db->query("SELECT * FROM {$this->table} WHERE status IN ('0','1') {$searchQuery} ORDER BY status ASC, {$columnName} {$columnSortOrder} LIMIT {$start}, {$length}");

        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = [
                'id'               => $row['id'],
                'customer_code'    => $row['customer_code'],
                'company_reg_no'   => $row['company_reg_no'],
                'new_reg_no'       => $row['new_reg_no'],
                'name'             => $row['name'],
                'address_line_1'   => $row['address_line_1'],
                'address_line_2'   => $row['address_line_2'],
                'address_line_3'   => $row['address_line_3'],
                'phone_no'         => $row['phone_no'],
                'fax_no'           => $row['fax_no'],
                'contact_name'     => $row['contact_name'],
                'ic_no'            => $row['ic_no'],
                'tin_no'           => $row['tin_no'],
                'deposit_balance'  => $row['deposit_balance'],
                'deposit_status'   => $row['deposit_status'],
                'min_deposit_balance' => $row['min_deposit_balance'],
                'status'           => $row['status']
            ];
        }

        return [
            'draw'                 => intval($draw),
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData'               => $data
        ];
    }

    /**
     * Create new customer. An opening deposit is logged as a top-up.
     */
    public function create($post) {
        $code = $this->nullIfEmpty($post['customerCode'] ?? null);
        $regNo = $this->nullIfEmpty($post['companyRegNo'] ?? null);
        $newRegNo = $this->nullIfEmpty($post['newRegNo'] ?? null);
        $name = $this->nullIfEmpty($post['companyName'] ?? null);
        $address1 = $this->nullIfEmpty($post['addressLine1'] ?? null);
        $address2 = $this->nullIfEmpty($post['addressLine2'] ?? null);
        $address3 = $this->nullIfEmpty($post['addressLine3'] ?? null);
        $phone = $this->nullIfEmpty($post['phoneNo'] ?? null);
        $fax = $this->nullIfEmpty($post['faxNo'] ?? null);
        $contactName = $this->nullIfEmpty($post['contactName'] ?? null);
        $icNo = $this->nullIfEmpty($post['icNo'] ?? null);
        $tinNo = $this->nullIfEmpty($post['tinNo'] ?? null);
        $deposit = $this->depositValue($post['depositBalance'] ?? null);
        $depositStatus = ($post['depositStatus'] ?? '') === 'Y' ? 'Y' : 'N';
        $minDeposit = $this->depositValue($post['minDepositBalance'] ?? null, 'Min deposit balance');

        if ($code === null) throw new Exception('Customer code is required');
        if ($this->isDuplicate('customer_code', $code)) {
            throw new Exception('Customer code already exists');
        }

        // Deposit balance and minimum apply only when the deposit status is Y

        if ($depositStatus === 'N') { $deposit = 0.0; $minDeposit = 0.0; }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, deposit_balance, deposit_status, min_deposit_balance, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ssssssssssssdsdss', $code, $regNo, $newRegNo, $name, $address1, $address2, $address3, $phone, $fax, $contactName, $icNo, $tinNo, $deposit, $depositStatus, $minDeposit, $this->username, $this->username);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $newId = $stmt->insert_id;
        $stmt->close();

        if ($deposit > 0) {
            (new DepositService($this->db, $this->username))->log(['type' => 'CUSTOMER', 'id' => $newId, 'code' => $code], null, 'TOPUP', $deposit, 0, $deposit, 'Opening deposit');
        }

        return ['id' => $newId];
    }

    /**
     * Update existing customer. A changed code or name is also updated in the records that reference it.
     * A changed deposit balance is logged as a top-up (increase) or an adjustment (decrease).
     */
    public function update($post) {
        $id = trim($post['id'] ?? '');
        if ($id === '') throw new Exception('Record not found');

        $code = $this->nullIfEmpty($post['customerCode'] ?? null);
        $regNo = $this->nullIfEmpty($post['companyRegNo'] ?? null);
        $newRegNo = $this->nullIfEmpty($post['newRegNo'] ?? null);
        $name = $this->nullIfEmpty($post['companyName'] ?? null);
        $address1 = $this->nullIfEmpty($post['addressLine1'] ?? null);
        $address2 = $this->nullIfEmpty($post['addressLine2'] ?? null);
        $address3 = $this->nullIfEmpty($post['addressLine3'] ?? null);
        $phone = $this->nullIfEmpty($post['phoneNo'] ?? null);
        $fax = $this->nullIfEmpty($post['faxNo'] ?? null);
        $contactName = $this->nullIfEmpty($post['contactName'] ?? null);
        $icNo = $this->nullIfEmpty($post['icNo'] ?? null);
        $tinNo = $this->nullIfEmpty($post['tinNo'] ?? null);
        $deposit = $this->depositValue($post['depositBalance'] ?? null);
        $depositStatus = ($post['depositStatus'] ?? '') === 'Y' ? 'Y' : 'N';
        $minDeposit = $this->depositValue($post['minDepositBalance'] ?? null, 'Min deposit balance');

        if ($code === null) throw new Exception('Customer code is required');
        if ($this->isDuplicate('customer_code', $code, $id)) {
            throw new Exception('Customer code already exists');
        }

        // Old code, name and balance: to update the weighing records and to log a balance change
        $select = $this->db->prepare("SELECT customer_code, name, deposit_balance, min_deposit_balance FROM {$this->table} WHERE id=? FOR UPDATE");
        if (!$select) throw new Exception($this->db->error);
        $select->bind_param('s', $id);
        if (!$select->execute()) throw new Exception($select->error);
        $old = $select->get_result()->fetch_assoc();
        $select->close();
        if (!$old) throw new Exception('Record not found');

        // Switching to N keeps the stored balance and minimum (the fields are hidden)
        if ($depositStatus === 'N') { $deposit = (float) $old['deposit_balance']; $minDeposit = (float) $old['min_deposit_balance']; }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET customer_code=?, company_reg_no=?, new_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, contact_name=?, ic_no=?, tin_no=?, deposit_balance=?, deposit_status=?, min_deposit_balance=?, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ssssssssssssdsdss', $code, $regNo, $newRegNo, $name, $address1, $address2, $address3, $phone, $fax, $contactName, $icNo, $tinNo, $deposit, $depositStatus, $minDeposit, $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        $oldDeposit = (float) $old['deposit_balance'];
        if (round($deposit - $oldDeposit, 2) != 0) {
            $txnType = $deposit > $oldDeposit ? 'TOPUP' : 'ADJUST';
            (new DepositService($this->db, $this->username))->log(['type' => 'CUSTOMER', 'id' => $id, 'code' => $code], null, $txnType, round($deposit - $oldDeposit, 2), $oldDeposit, $deposit, 'Manual update');
        }

        if ($old['customer_code'] !== $code) {
            $this->updateMasterDataCodeValue($old['customer_code'], $code, 'Customer');
        }
        if ($old['name'] !== $name) {
            $this->updateMasterDataNameValue($old['name'], $name, 'Customer');
        }

        return ['id' => $id];
    }

    /**
     * Delete (soft delete) customer
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=1, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ss', $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    }

    /**
     * Get single customer by ID
     */
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Delete (soft delete) several customers
     */
    public function deleteMany($ids) {
        $ids = array_values(array_filter(array_map('intval', (array) $ids)));
        if (empty($ids)) throw new Exception('No records selected');

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=1, modified_by=? WHERE id IN ({$placeholders})");
        if (!$stmt) throw new Exception($this->db->error);
        $params = array_merge([$this->username], $ids);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    }

    /**
     * Save Excel rows (Code, Name, RegNo, NewRegNo, Addr1-3, Tel, Fax, ContactName, ICNo, TinNo, DepositStatus, Deposit, MinDeposit).
     * Rows without a code are skipped. Duplicate codes are updated instead of skipped.
     */
    public function importRows($rows) {
        $errors = [];

        foreach ($rows as $row) {
            $code = $this->nullIfEmpty($row['Code'] ?? null);
            if ($code === null) continue;

            $name = $this->nullIfEmpty($row['Name'] ?? null);
            $regNo = $this->nullIfEmpty($row['RegNo'] ?? null);
            $newRegNo = $this->nullIfEmpty($row['NewRegNo'] ?? null);
            $address1 = $this->nullIfEmpty($row['Addr1'] ?? null);
            $address2 = $this->nullIfEmpty($row['Addr2'] ?? null);
            $address3 = $this->nullIfEmpty($row['Addr3'] ?? null);
            $phone = $this->nullIfEmpty($row['Tel'] ?? null);
            $fax = $this->nullIfEmpty($row['Fax'] ?? null);
            $contactName = $this->nullIfEmpty($row['ContactName'] ?? null);
            $icNo = $this->nullIfEmpty($row['ICNo'] ?? null);
            $tinNo = $this->nullIfEmpty($row['TinNo'] ?? null);
            $deposit = $this->depositValue($row['Deposit'] ?? null);
            $depositStatus = strtoupper(trim($row['DepositStatus'] ?? '')) === 'Y' ? 'Y' : 'N';
            $minDeposit = $this->depositValue($row['MinDeposit'] ?? null, 'Min deposit balance');

            if ($this->isDuplicate('customer_code', $code)) {
                // Fetch existing record to log deposit change
                $sel = $this->db->prepare("SELECT id, deposit_balance, min_deposit_balance FROM {$this->table} WHERE customer_code=? AND status=0");
                $sel->bind_param('s', $code);
                $sel->execute();
                $existing = $sel->get_result()->fetch_assoc();
                $sel->close();

                // Blank Excel name keeps the stored name (COALESCE below)
                // N keeps the stored balance and minimum
                if ($depositStatus === 'N') { $deposit = (float) $existing['deposit_balance']; $minDeposit = (float) $existing['min_deposit_balance']; }

                $stmt = $this->db->prepare("UPDATE {$this->table} SET company_reg_no=?, new_reg_no=?, name=COALESCE(?, name), address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, contact_name=?, ic_no=?, tin_no=?, deposit_balance=?, deposit_status=?, min_deposit_balance=?, modified_by=? WHERE id=?");
                if (!$stmt) throw new Exception($this->db->error);
                $stmt->bind_param('sssssssssssdsdss', $regNo, $newRegNo, $name, $address1, $address2, $address3, $phone, $fax, $contactName, $icNo, $tinNo, $deposit, $depositStatus, $minDeposit, $this->username, $existing['id']);
                if (!$stmt->execute()) throw new Exception($stmt->error);
                $stmt->close();

                $oldDeposit = (float) $existing['deposit_balance'];
                if (round($deposit - $oldDeposit, 2) != 0) {
                    $txnType = $deposit > $oldDeposit ? 'TOPUP' : 'ADJUST';
                    (new DepositService($this->db, $this->username))->log(['type' => 'CUSTOMER', 'id' => $existing['id'], 'code' => $code], null, $txnType, round($deposit - $oldDeposit, 2), $oldDeposit, $deposit, 'Excel import update');
                }
            } else {
                // Deposit balance and minimum apply only when the deposit status is Y
                if ($depositStatus === 'N') { $deposit = 0.0; $minDeposit = 0.0; }
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, deposit_balance, deposit_status, min_deposit_balance, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) throw new Exception($this->db->error);
                $stmt->bind_param('ssssssssssssdsdss', $code, $regNo, $newRegNo, $name, $address1, $address2, $address3, $phone, $fax, $contactName, $icNo, $tinNo, $deposit, $depositStatus, $minDeposit, $this->username, $this->username);
                if (!$stmt->execute()) throw new Exception($stmt->error);
                $newId = $stmt->insert_id;
                $stmt->close();

                if ($deposit > 0) {
                    (new DepositService($this->db, $this->username))->log(['type' => 'CUSTOMER', 'id' => $newId, 'code' => $code], null, 'TOPUP', $deposit, 0, $deposit, 'Opening deposit');
                }
            }
        }

        return $errors;
    }

    /**
     * Empty values are stored as NULL, as the legacy endpoints did
     */
    private function nullIfEmpty($value) {
        return empty($value) ? null : trim($value);
    }

    /**
     * Opening / topped-up deposit: 0 or more, two decimals
     */
    private function depositValue($value, $label = 'Deposit balance') {
        if ($value === null || $value === '') return 0.0;
        if (!is_numeric($value) || (float) $value < 0) {
            throw new Exception($label . ' must be 0 or more');
        }
        return round((float) $value, 2);
    }

    /**
     * Check for duplicate active code
     */
    private function isDuplicate($column, $value, $excludeId = null) {
        $query = "SELECT id FROM {$this->table} WHERE {$column} = ? AND status = 0";
        if ($excludeId) {
            $query .= " AND id != ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('si', $value, $excludeId);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $value);
        }
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
?>
