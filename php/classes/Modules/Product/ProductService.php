<?php
namespace App\Modules\Product;

use App\Core\BaseService;

class ProductService extends BaseService
{
    private const SORT_COLUMNS = ['id', 'product_code', 'name', 'description'];

    private const FORM_COLUMNS = 'id, product_code, name, description, variance, high, low';

    /**
     * Paginated active product records for DataTables
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $totalRecords = $this->countRows('Product', '1=1', '', []);

        $where = "status = '0'";
        $params = [];
        $types = '';
        if ($search !== '') {
            $where .= " AND (name LIKE ? OR description LIKE ? OR product_code LIKE ?)";
            $params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
            $types = 'sss';
        }
        $totalFiltered = $this->countRows('Product', $where, $types, $params);

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT " . self::FORM_COLUMNS . ", status FROM Product WHERE $where ORDER BY status ASC, $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $row) {
            $data[] = ['id' => $row['id'], 'product_code' => $row['product_code'], 'name' => $row['name'], 'description' => $row['description'], 'variance' => $row['variance'], 'high' => $row['high'], 'low' => $row['low'], 'status' => $row['status']];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Single record by ID
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT " . self::FORM_COLUMNS . " FROM Product WHERE id = ?", 'i', [$id]);
    }

    public function create(array $data): array
    {
        if ($duplicate = $this->duplicateError($data)) {
            return $duplicate;
        }

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->insertRow('Product', $data)) {
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
            if (!$this->updateRow('Product', $data, 'id = ?', 'i', [$id])) {
                throw new \Exception('Failed to update product');
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

        if (!empty($old['product_code']) && !empty($data['product_code']) && $old['product_code'] !== $data['product_code']) {
            $this->updateMasterDataCodeValue($old['product_code'], $data['product_code'], 'Product');
        }

        if (!empty($old['name']) && !empty($data['name']) && $old['name'] !== $data['name']) {
            $this->updateMasterDataNameValue($old['name'], $data['name'], 'Product');
        }
    }

    /**
     * Failure result when the code or name is already used by another active record in this company database
     */
    private function duplicateError(array $data, int $excludeId = 0): ?array
    {
        if (!empty($data['product_code']) && $this->isDuplicate('product_code', $data['product_code'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Product code already exists'];
        }
        if (!empty($data['name']) && $this->isDuplicate('name', $data['name'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Product name already exists'];
        }

        return null;
    }

    private function isDuplicate(string $column, string $value, int $excludeId): bool
    {
        return $this->countRows('Product', "$column = ? AND status = '0' AND id != ?", 'si', [$value, $excludeId]) > 0;
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

        $sql = "UPDATE Product SET status = '1' WHERE id IN (" . $this->placeholders(count($ids)) . ")";
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

            $code = $text('ProductCode');
            if ($code === '') {
                continue;
            }

            if ($this->fetchOne("SELECT id FROM Product WHERE product_code = ? AND status = '0'", 's', [$code])) {
                $errors[] = 'Product: ' . $text('ProductName') . ' already exist in master data.';
                continue;
            }

            $this->insertRow('Product', [
                'product_code' => $code,
                'name' => $text('ProductName'),
                'description' => $text('ProductDescription'),
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
