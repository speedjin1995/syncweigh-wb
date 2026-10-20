<?php
namespace App\Modules\Plant;

use App\Core\BaseService;

class PlantService extends BaseService
{
    private const SORT_COLUMNS = ['id', 'plant_code', 'name', 'address_line_1', 'address_line_2', 'address_line_3', 'phone_no', 'fax_no'];

    private const FORM_COLUMNS = 'id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no';

    /**
     * Paginated active plant records for DataTables
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $totalRecords = $this->countRows('Plant', '1=1', '', []);

        $where = "status = '0'";
        $params = [];
        $types = '';
        if ($search !== '') {
            $where .= " AND (name LIKE ? OR plant_code LIKE ?)";
            $params = ['%' . $search . '%', '%' . $search . '%'];
            $types = 'ss';
        }
        $totalFiltered = $this->countRows('Plant', $where, $types, $params);

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT " . self::FORM_COLUMNS . ", status FROM Plant WHERE $where ORDER BY status ASC, $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $row) {
            $data[] = ['id' => $row['id'], 'plant_code' => $row['plant_code'], 'name' => $row['name'], 'address_line_1' => $row['address_line_1'], 'address_line_2' => $row['address_line_2'], 'address_line_3' => $row['address_line_3'], 'phone_no' => $row['phone_no'], 'fax_no' => $row['fax_no'], 'status' => $row['status']];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Single record by ID
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT " . self::FORM_COLUMNS . " FROM Plant WHERE id = ?", 'i', [$id]);
    }

    public function create(array $data): array
    {
        if ($duplicate = $this->duplicateError($data)) {
            return $duplicate;
        }

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->insertRow('Plant', $data)) {
            return ['status' => 'failed', 'message' => 'Failed to add record'];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }

    /**
     * Update existing record. created_by is overwritten with the editor, as the legacy endpoint did.
     */
    public function update(int $id, array $data): array
    {
        if ($duplicate = $this->duplicateError($data, $id)) {
            return $duplicate;
        }

        $old = $this->getById($id);

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        $this->db->begin_transaction();
        try {
            if (!$this->updateRow('Plant', $data, 'id = ?', 'i', [$id])) {
                throw new \Exception('Failed to update plant');
            }
            $this->renameReferences($old, $data);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log(static::class . '::update - ' . $e->getMessage());
            return ['status' => 'failed', 'message' => 'Failed to update record'];
        }

        return ['status' => 'success', 'message' => 'Updated Successfully!!'];
    }

    private function renameReferences(?array $old, array $data): void
    {
        if (!$old) {
            return;
        }

        if (!empty($old['plant_code']) && !empty($data['plant_code']) && $old['plant_code'] !== $data['plant_code']) {
            $this->updateMasterDataCodeValue($old['plant_code'], $data['plant_code'], 'Plant');
        }

        if (!empty($old['name']) && !empty($data['name']) && $old['name'] !== $data['name']) {
            $this->updateMasterDataNameValue($old['name'], $data['name'], 'Plant');
        }
    }

    /**
     * Failure result when the code or name is already used by another active record in this company database
     */
    private function duplicateError(array $data, int $excludeId = 0): ?array
    {
        if (!empty($data['plant_code']) && $this->isDuplicate('plant_code', $data['plant_code'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Plant code already exists'];
        }
        if (!empty($data['name']) && $this->isDuplicate('name', $data['name'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Plant name already exists'];
        }

        return null;
    }

    private function isDuplicate(string $column, string $value, int $excludeId): bool
    {
        return $this->countRows('Plant', "$column = ? AND status = '0' AND id != ?", 'si', [$value, $excludeId]) > 0;
    }

    /**
     * Soft delete one or more records
     */
    public function delete(array $ids): array
    {
        $ids = $this->cleanIds($ids);

        if (empty($ids)) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $sql = "UPDATE Plant SET status = '1' WHERE id IN (" . $this->placeholders(count($ids)) . ")";
        if (!$this->executeWrite($sql, str_repeat('i', count($ids)), $ids)) {
            return ['status' => 'failed', 'message' => 'Failed to delete record'];
        }

        return ['status' => 'success', 'message' => 'Deleted'];
    }

    /**
     * Bulk insert from Excel upload, skipping codes that already exist
     */
    public function upload(array $rows): array
    {
        $errors = [];

        foreach ($rows as $row) {
            $text = fn(string $key): string => trim((string)($row[$key] ?? ''));

            $code = $text('PlantCode');
            if ($code === '') {
                continue;
            }

            if ($this->fetchOne("SELECT id FROM Plant WHERE plant_code = ? AND status = '0'", 's', [$code])) {
                $errors[] = 'Plant: ' . $text('PlantName') . ' already exist in master data.';
                continue;
            }

            $this->insertRow('Plant', [
                'plant_code' => $code,
                'name' => $text('PlantName'),
                'address_line_1' => $text('AddressLine1'),
                'address_line_2' => $text('AddressLine2'),
                'address_line_3' => $text('AddressLine3'),
                'phone_no' => $text('PhoneNo'),
                'fax_no' => $text('FaxNo'),
                'created_by' => $this->username,
                'modified_by' => $this->username
            ]);
        }

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => $errors];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }
}
