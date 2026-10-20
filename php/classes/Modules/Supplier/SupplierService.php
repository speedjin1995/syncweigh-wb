<?php
namespace App\Modules\Supplier;

use App\Core\BaseService;

class SupplierService extends BaseService
{
    private const SORT_COLUMNS = ['id', 'supplier_code', 'company_reg_no', 'new_reg_no', 'name', 'address_line_1', 'address_line_2', 'address_line_3', 'phone_no', 'fax_no', 'contact_name', 'ic_no', 'tin_no'];

    private const FORM_COLUMNS = 'id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no';

    /**
     * Paginated active suppliers for DataTables
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $totalRecords = $this->countRows('Supplier', '1=1', '', []);

        $where = "status = '0'";
        $params = [];
        $types = '';
        if ($search !== '') {
            $where .= " AND (name LIKE ? OR company_reg_no LIKE ? OR supplier_code LIKE ?)";
            $params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
            $types = 'sss';
        }
        $totalFiltered = $this->countRows('Supplier', $where, $types, $params);

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT " . self::FORM_COLUMNS . ", status FROM Supplier WHERE $where ORDER BY status ASC, $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row['id'],
                'supplier_code' => $row['supplier_code'],
                'name' => $row['name'],
                'company_reg_no' => $row['company_reg_no'],
                'new_reg_no' => $row['new_reg_no'],
                'address_line_1' => $row['address_line_1'],
                'address_line_2' => $row['address_line_2'],
                'address_line_3' => $row['address_line_3'],
                'phone_no' => $row['phone_no'],
                'fax_no' => $row['fax_no'],
                'contact_name' => $row['contact_name'],
                'ic_no' => $row['ic_no'],
                'tin_no' => $row['tin_no'],
                'status' => $row['status']
            ];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Single supplier by ID
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT " . self::FORM_COLUMNS . " FROM Supplier WHERE id = ?", 'i', [$id]);
    }

    public function create(array $data): array
    {
        if ($duplicate = $this->duplicateError($data)) {
            return $duplicate;
        }

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->insertRow('Supplier', $data)) {
            return ['status' => 'failed', 'message' => 'Failed to add record'];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }

    /**
     * Update existing supplier. created_by is overwritten with the editor, as the legacy endpoint did.
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
            if (!$this->updateRow('Supplier', $data, 'id = ?', 'i', [$id])) {
                throw new \Exception('Failed to update supplier');
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

    /**
     * Carry a changed supplier code or name over to the weighing records that reference it
     */
    private function renameReferences(?array $old, array $data): void
    {
        if (!$old) {
            return;
        }

        if (!empty($old['supplier_code']) && !empty($data['supplier_code']) && $old['supplier_code'] !== $data['supplier_code']) {
            $this->updateMasterDataCodeValue($old['supplier_code'], $data['supplier_code'], 'Supplier');
        }

        if (!empty($old['name']) && !empty($data['name']) && $old['name'] !== $data['name']) {
            $this->updateMasterDataNameValue($old['name'], $data['name'], 'Supplier');
        }
    }

    /**
     * Failure result when the code or name is already used by another active supplier in this company database
     */
    private function duplicateError(array $data, int $excludeId = 0): ?array
    {
        if (!empty($data['supplier_code']) && $this->isDuplicate('supplier_code', $data['supplier_code'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Supplier code already exists'];
        }

        if (!empty($data['name']) && $this->isDuplicate('name', $data['name'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Supplier name already exists'];
        }

        return null;
    }

    private function isDuplicate(string $column, string $value, int $excludeId): bool
    {
        return $this->countRows('Supplier', "$column = ? AND status = '0' AND id != ?", 'si', [$value, $excludeId]) > 0;
    }

    /**
     * Soft delete one or more suppliers
     */
    public function delete(array $ids): array
    {
        $ids = $this->cleanIds($ids);

        if (empty($ids)) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $sql = "UPDATE Supplier SET status = '1' WHERE id IN (" . $this->placeholders(count($ids)) . ")";
        if (!$this->executeWrite($sql, str_repeat('i', count($ids)), $ids)) {
            return ['status' => 'failed', 'message' => 'Failed to delete record'];
        }

        return ['status' => 'success', 'message' => 'Deleted'];
    }

    /**
     * Bulk insert suppliers from Excel upload, skipping codes that already exist
     */
    public function upload(array $rows): array
    {
        $errors = [];

        foreach ($rows as $row) {
            $text = fn(string $key): string => trim((string)($row[$key] ?? ''));

            $code = $text('Code');
            if ($code === '') {
                continue;
            }

            if ($this->fetchOne("SELECT id FROM Supplier WHERE supplier_code = ? AND status = '0'", 's', [$code])) {
                $errors[] = 'Supplier: ' . $text('Name') . ' already exist in master data.';
                continue;
            }

            $this->insertRow('Supplier', [
                'supplier_code' => $code,
                'company_reg_no' => $text('RegNo'),
                'new_reg_no' => $text('NewRegNo'),
                'name' => $text('Name'),
                'address_line_1' => $text('Addr1'),
                'address_line_2' => $text('Addr2'),
                'address_line_3' => $text('Addr3'),
                'phone_no' => $text('Tel'),
                'fax_no' => $text('Fax'),
                'contact_name' => $text('ContactName'),
                'ic_no' => $text('ICNo'),
                'tin_no' => $text('TinNo'),
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
