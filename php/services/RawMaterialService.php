<?php
require_once __DIR__ . '/BaseService.php';

class RawMaterialService extends BaseService {

    protected $table = 'Raw_Mat';

    /**
     * Get all raw materials (for DataTables)
     */
    public function getAll($post) {
        $draw = $post['draw'] ?? 1;
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);
        $searchValue = isset($post['search']['value']) ? mysqli_real_escape_string($this->db, $post['search']['value']) : '';

        $sortableColumns = ['id', 'raw_mat_code', 'name', 'description'];
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
            $searchQuery = " AND (raw_mat_code LIKE '%{$searchValue}%' OR name LIKE '%{$searchValue}%' OR description LIKE '%{$searchValue}%')";
        }

        // Filtered records
        $filteredResult = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status IN ('0','1') {$searchQuery}");
        $totalFiltered = $filteredResult->fetch_assoc()['total'];

        // Data
        $dataResult = $this->db->query("SELECT *, (SELECT c.currency_code FROM Currency c WHERE c.id = {$this->table}.standard_currency_id) AS standard_currency_code FROM {$this->table} WHERE status IN ('0','1') {$searchQuery} ORDER BY status ASC, {$columnName} {$columnSortOrder} LIMIT {$start}, {$length}");

        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = [
                'id'           => $row['id'],
                'raw_mat_code' => $row['raw_mat_code'],
                'name'         => $row['name'],
                'description'  => $row['description'],
                'standard_price'        => $row['standard_price'],
                'standard_currency_code' => $row['standard_currency_code'],
                'status'       => $row['status']
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
     * Create new raw material
     */
    public function create($post) {
        $rawMatCode = $this->nullIfEmpty($post['rawMatCode'] ?? null);
        $rawMatName = $this->nullIfEmpty($post['rawMatName'] ?? null);
        $description = $this->nullIfEmpty($post['description'] ?? null);
        $varianceType = $this->nullIfEmpty($post['varianceType'] ?? null);
        $high = $this->nullIfEmpty($post['high'] ?? null);
        $low = $this->nullIfEmpty($post['low'] ?? null);

        if ($rawMatCode === null) throw new Exception('Raw Material code is required');
        if ($rawMatName === null) throw new Exception('Raw Material name is required');
        if ($this->isDuplicate('raw_mat_code', $rawMatCode)) {
            throw new Exception('Raw Material code already exists');
        }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (raw_mat_code, name, description, variance, high, low, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ssssssss', $rawMatCode, $rawMatName, $description, $varianceType, $high, $low, $this->username, $this->username);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $newId = $stmt->insert_id;
        $stmt->close();

        return ['id' => $newId];
    }

    /**
     * Update existing raw material. A changed code or name is also updated in the records that reference it.
     */
    public function update($post) {
        $id = trim($post['id'] ?? '');
        if ($id === '') throw new Exception('Record not found');

        $rawMatCode = $this->nullIfEmpty($post['rawMatCode'] ?? null);
        $rawMatName = $this->nullIfEmpty($post['rawMatName'] ?? null);
        $description = $this->nullIfEmpty($post['description'] ?? null);
        $varianceType = $this->nullIfEmpty($post['varianceType'] ?? null);
        $high = $this->nullIfEmpty($post['high'] ?? null);
        $low = $this->nullIfEmpty($post['low'] ?? null);

        if ($rawMatCode === null) throw new Exception('Raw Material code is required');
        if ($rawMatName === null) throw new Exception('Raw Material name is required');
        if ($this->isDuplicate('raw_mat_code', $rawMatCode, $id)) {
            throw new Exception('Raw Material code already exists');
        }

        // Old code / name, to update the weighing records that reference them
        $select = $this->db->prepare("SELECT raw_mat_code, name FROM {$this->table} WHERE id=?");
        if (!$select) throw new Exception($this->db->error);
        $select->bind_param('s', $id);
        if (!$select->execute()) throw new Exception($select->error);
        $old = $select->get_result()->fetch_assoc();
        $select->close();
        if (!$old) throw new Exception('Record not found');

        $stmt = $this->db->prepare("UPDATE {$this->table} SET raw_mat_code=?, name=?, description=?, variance=?, high=?, low=?, created_by=?, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('sssssssss', $rawMatCode, $rawMatName, $description, $varianceType, $high, $low, $this->username, $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        if ($old['raw_mat_code'] !== $rawMatCode) {
            $this->updateMasterDataCodeValue($old['raw_mat_code'], $rawMatCode, 'Raw Material');
        }
        if ($old['name'] !== $rawMatName) {
            $this->updateMasterDataNameValue($old['name'], $rawMatName, 'Raw Material');
        }

        return ['id' => $id];
    }

    /**
     * Delete (soft delete) raw material
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=1, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ss', $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    }

    /**
     * Get single raw material by ID
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
     * Delete (soft delete) several raw materials
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
     * Save Excel rows (Code, Name, Description). Rows without a code are skipped,
     * duplicate codes are returned as messages.
     */
    public function importRows($rows) {
        $errors = [];

        foreach ($rows as $row) {
            $rawMatCode = $this->nullIfEmpty($row['Code'] ?? null);
            $rawMatName = trim($row['Name'] ?? '');
            $description = $this->nullIfEmpty($row['Description'] ?? null);

            if ($rawMatCode === null) continue;

            if ($this->isDuplicate('raw_mat_code', $rawMatCode)) {
                $errors[] = "Raw Material: {$rawMatCode} already exist in master data.";
                continue;
            }

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (raw_mat_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) throw new Exception($this->db->error);
            $stmt->bind_param('sssss', $rawMatCode, $rawMatName, $description, $this->username, $this->username);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();
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

    /**
     * Standard price and supplier prices of a raw material (for the pricing modal)
     */
    public function getPricing($id) {
        $rawMat = $this->get($id);
        if (!$rawMat) return null;

        $stmt = $this->db->prepare("SELECT pcp.supplier_id, pcp.pricing_type, pcp.currency_id, pcp.price, c.supplier_code, c.name AS supplier_name FROM Raw_Mat_Supplier_Price pcp JOIN Supplier c ON c.id = pcp.supplier_id WHERE pcp.raw_mat_id = ? AND pcp.status = '0' ORDER BY c.name");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return [
            'raw_mat_code'         => $rawMat['raw_mat_code'],
            'name'                 => $rawMat['name'],
            'standard_currency_id' => $rawMat['standard_currency_id'],
            'standard_price'       => $rawMat['standard_price'],
            'rows'                 => $rows
        ];
    }

    /**
     * Save the standard price and the supplier prices of a raw material.
     * A supplier already priced for this raw material is updated (and reactivated if needed),
     * a supplier left out of the list is soft deleted. Runs inside the controller's transaction.
     */
    public function savePricing($post) {
        $rawMatId = trim($post['rawMatId'] ?? '');
        if ($rawMatId === '' || !$this->get($rawMatId)) throw new Exception('Record not found');

        $standardCurrencyId = $this->validCurrencyId($post['standardCurrencyId'] ?? null);
        $standardPrice = $this->validPrice($post['standardPrice'] ?? null, 'Standard price');

        $items = json_decode($post['prices'] ?? '[]', true);
        if (!is_array($items)) throw new Exception('Invalid pricing data');

        $rows = [];
        foreach ($items as $item) {
            $supplierId = trim((string) ($item['supplierId'] ?? ''));
            if ($supplierId === '') throw new Exception('Supplier is required');
            if (isset($rows[$supplierId])) throw new Exception('A supplier can only be listed once');
            if (!$this->isActiveRecord('Supplier', $supplierId)) throw new Exception('Supplier not found');

            $pricingType = strtoupper(trim((string) ($item['pricingType'] ?? '')));
            if (!in_array($pricingType, ['STANDARD', 'FLOAT'], true)) throw new Exception('Invalid pricing type');

            // Standard rows always take the raw material's standard price and currency
            $rows[$supplierId] = $pricingType === 'STANDARD'
                ? ['pricing_type' => 'STANDARD', 'currency_id' => $standardCurrencyId, 'price' => $standardPrice]
                : ['pricing_type' => 'FLOAT', 'currency_id' => $this->validCurrencyId($item['currencyId'] ?? null), 'price' => $this->validPrice($item['price'] ?? null, 'Price')];
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET standard_currency_id=?, standard_price=?, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('isss', $standardCurrencyId, $standardPrice, $this->username, $rawMatId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        // Existing price rows for this raw material, by supplier (any status)
        $existing = [];
        $stmt = $this->db->prepare("SELECT id, supplier_id, status FROM Raw_Mat_Supplier_Price WHERE raw_mat_id = ?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $rawMatId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (!isset($existing[$row['supplier_id']])) {
                $existing[$row['supplier_id']] = $row;
            }
        }
        $stmt->close();

        foreach ($rows as $supplierId => $row) {
            if (isset($existing[$supplierId])) {
                $stmt = $this->db->prepare("UPDATE Raw_Mat_Supplier_Price SET pricing_type=?, currency_id=?, price=?, status='0', modified_by=? WHERE id=?");
                if (!$stmt) throw new Exception($this->db->error);
                $priceId = $existing[$supplierId]['id'];
                $stmt->bind_param('sisss', $row['pricing_type'], $row['currency_id'], $row['price'], $this->username, $priceId);
            } else {
                $stmt = $this->db->prepare("INSERT INTO Raw_Mat_Supplier_Price (raw_mat_id, supplier_id, pricing_type, currency_id, price, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) throw new Exception($this->db->error);
                $stmt->bind_param('sssisss', $rawMatId, $supplierId, $row['pricing_type'], $row['currency_id'], $row['price'], $this->username, $this->username);
            }
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();
        }

        foreach ($existing as $supplierId => $info) {
            if (!isset($rows[$supplierId]) && (string) $info['status'] === '0') {
                $stmt = $this->db->prepare("UPDATE Raw_Mat_Supplier_Price SET status='1', modified_by=? WHERE id=?");
                if (!$stmt) throw new Exception($this->db->error);
                $stmt->bind_param('ss', $this->username, $info['id']);
                if (!$stmt->execute()) throw new Exception($stmt->error);
                $stmt->close();
            }
        }

        return ['id' => $rawMatId];
    }

    /**
     * Currency id that must point to an active currency
     */
    private function validCurrencyId($value) {
        $id = trim((string) $value);
        if ($id === '' || !ctype_digit($id)) throw new Exception('Currency is required');
        if (!$this->isActiveRecord('Currency', $id)) throw new Exception('Currency not found');
        return (int) $id;
    }

    /**
     * Price as a non-negative decimal string
     */
    private function validPrice($value, $label) {
        if ($value === null || trim((string) $value) === '' || !is_numeric($value) || $value < 0) {
            throw new Exception("{$label} must be a number of 0 or more");
        }
        return number_format((float) $value, 2, '.', '');
    }

    /**
     * True when the record exists and is active
     */
    private function isActiveRecord($table, $id) {
        $stmt = $this->db->prepare("SELECT id FROM {$table} WHERE id = ? AND status = '0'");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
?>
