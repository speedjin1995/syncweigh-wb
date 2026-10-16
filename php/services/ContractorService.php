<?php
require_once __DIR__ . '/BaseService.php';

class ContractorService extends BaseService {

    protected $table = 'Contractor';

    /**
     * Get all contractors (for DataTables)
     */
    public function getAll($post) {
        $draw = $post['draw'] ?? 1;
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);
        $searchValue = isset($post['search']['value']) ? mysqli_real_escape_string($this->db, $post['search']['value']) : '';

        $sortableColumns = ['id', 'contractor_code', 'name'];
        $columnIndex = $post['order'][0]['column'] ?? 0;
        $columnName = $post['columns'][$columnIndex]['data'] ?? 'id';
        if (!in_array($columnName, $sortableColumns)) {
            $columnName = 'id';
        }
        $columnSortOrder = (isset($post['order'][0]['dir']) && $post['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';

        // Total records
        $totalResult = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = '0'");
        $totalRecords = $totalResult->fetch_assoc()['total'];

        // Search (DataTables search box)
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND (contractor_code LIKE '%{$searchValue}%' OR name LIKE '%{$searchValue}%')";
        }

        // Filtered records
        $filteredResult = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = '0' {$searchQuery}");
        $totalFiltered = $filteredResult->fetch_assoc()['total'];

        // Data
        $dataResult = $this->db->query("SELECT * FROM {$this->table} WHERE status = '0' {$searchQuery} ORDER BY {$columnName} {$columnSortOrder} LIMIT {$start}, {$length}");

        $data = [];
        while ($row = $dataResult->fetch_assoc()) {
            $data[] = [
                'id'              => $row['id'],
                'contractor_code' => $row['contractor_code'],
                'name'            => $row['name']
            ];
        }

        return [
            'draw'                  => intval($draw),
            'iTotalRecords'         => $totalRecords,
            'iTotalDisplayRecords'  => $totalFiltered,
            'aaData'                => $data
        ];
    }

    /**
     * Create new contractor
     */
    public function create($post) {
        $contractorCode = trim($post['contractorCode']);
        $contractorName = trim($post['contractorName']);

        if ($this->isDuplicate('contractor_code', $contractorCode)) {
            throw new Exception('Contractor code already exists');
        }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (contractor_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ssss', $contractorCode, $contractorName, $this->username, $this->username);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $newId = $stmt->insert_id;
        $stmt->close();

        return ['id' => $newId];
    }

    /**
     * Update existing contractor
     */
    public function update($post) {
        $id             = $post['id'];
        $contractorCode = trim($post['contractorCode']);
        $contractorName = trim($post['contractorName']);

        if ($this->isDuplicate('contractor_code', $contractorCode, $id)) {
            throw new Exception('Contractor code already exists');
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET contractor_code=?, name=?, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ssss', $contractorCode, $contractorName, $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        return ['id' => $id];
    }

    /**
     * Delete (soft delete) contractor
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=1, modified_by=? WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('ss', $this->username, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    }

    /**
     * Get single contractor by ID
     */
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        if (!$stmt) throw new Exception($this->db->error);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    public function getList() {
        $stmt = $this->db->prepare("SELECT id, contractor_code, name FROM {$this->table} WHERE status = '0' ORDER BY name");
        if (!$stmt) throw new Exception($this->db->error);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $result = $stmt->get_result();
        $list = [];
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }

    /**
     * Delete (soft delete) several contractors
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
     * Save Excel rows (Code, Name). Invalid or duplicate rows are skipped and returned as messages.
     */
    public function importRows($rows) {
        $errors = [];
        $seen = [];

        foreach ($rows as $row) {
            $code = trim($row['Code'] ?? '');
            $name = trim($row['Name'] ?? '');

            if ($code === '' && $name === '') continue;

            if ($code === '' || $name === '') {
                $errors[] = "Contractor: {$code} {$name} - code and name are required.";
                continue;
            }

            if (isset($seen[$code]) || $this->isDuplicate('contractor_code', $code)) {
                $errors[] = "Contractor: {$code} already exist in master data.";
                continue;
            }
            $seen[$code] = true;

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (contractor_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)");
            if (!$stmt) throw new Exception($this->db->error);
            $stmt->bind_param('ssss', $code, $name, $this->username, $this->username);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();
        }

        return $errors;
    }

    /**
     * Check for duplicate value
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
