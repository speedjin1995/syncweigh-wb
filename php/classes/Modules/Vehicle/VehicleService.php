<?php
namespace App\Modules\Vehicle;

use App\Core\BaseService;

class VehicleService extends BaseService
{
    private const SORT_COLUMNS = ['id', 'veh_number', 'vehicle_weight', 'transporter_name', 'customer_name', 'supplier_name'];

    private const FORM_COLUMNS = 'id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name';

    /**
     * Paginated active vehicles for DataTables
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $totalRecords = $this->countRows('Vehicle', '1=1', '', []);

        $where = "status = '0'";
        $params = [];
        $types = '';
        if ($search !== '') {
            $where .= " AND (veh_number LIKE ? OR vehicle_weight LIKE ? OR transporter_code LIKE ? OR customer_name LIKE ? OR supplier_name LIKE ?)";
            $params = array_fill(0, 5, '%' . $search . '%');
            $types = 'sssss';
        }
        $totalFiltered = $this->countRows('Vehicle', $where, $types, $params);

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT id, veh_number, vehicle_weight, transporter_name, customer_name, supplier_name, status FROM Vehicle WHERE $where ORDER BY status ASC, $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row['id'],
                'veh_number' => $row['veh_number'],
                'vehicle_weight' => $row['vehicle_weight'],
                'transporter_name' => $row['transporter_name'],
                'customer_name' => $row['customer_name'],
                'supplier_name' => $row['supplier_name'],
                'status' => $row['status'] == '0' ? 'Active' : 'Inactive'
            ];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Weighing screen check: an error when the vehicle has a pending weighing, otherwise its active record.
     * Ported as-is from the legacy getVehicle.php (type = lookup).
     */
    public function lookup(string $plateNo): array
    {
        $pending = $this->fetchOne("SELECT COUNT(*) AS COUNT FROM Weight WHERE lorry_plate_no1 = ? AND is_complete='N'", 's', [$plateNo]);
        if (($pending['COUNT'] ?? 0) > 0) {
            return ['status' => 'error', 'message' => 'There is a pending record for this vehicle'];
        }

        return ['status' => 'success', 'message' => $this->findActive('veh_number', $plateNo)];
    }

    /**
     * Active vehicle by number, for filling the weighing screen (type = pullCustomer)
     */
    public function pullCustomer(string $plateNo): array
    {
        return ['status' => 'success', 'message' => $this->findActive('veh_number', $plateNo)];
    }

    /**
     * Active vehicle by ID (the vehicle page)
     */
    public function get(string $id): array
    {
        return ['status' => 'success', 'message' => $this->findActive('id', $id)];
    }

    /**
     * Details of the last active vehicle matching the column, or an empty list when none. $column is never user input.
     */
    private function findActive(string $column, string $value): array
    {
        $message = [];
        foreach ($this->fetchAll("SELECT * FROM Vehicle WHERE $column=? AND status='0'", 's', [$value]) as $row) {
            $message = [
                'id' => $row['id'],
                'veh_number' => $row['veh_number'],
                'vehicle_weight' => $row['vehicle_weight'],
                'transporter_name' => $row['transporter_name'],
                'transporter_code' => $row['transporter_code'],
                'customer_code' => $row['customer_code'],
                'customer_name' => $row['customer_name'],
                'supplier_code' => $row['supplier_code'],
                'supplier_name' => $row['supplier_name']
            ];
        }

        return $message;
    }

    public function create(array $data): array
    {
        if ($this->isDuplicateVehicle($data['veh_number'], 0)) {
            return ['status' => 'failed', 'message' => 'Vehicle number already exists'];
        }

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->insertRow('Vehicle', $data)) {
            return ['status' => 'failed', 'message' => 'Failed to add record'];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }

    /**
     * Update existing vehicle. created_by is overwritten with the editor, as the legacy endpoint did.
     */
    public function update(int $id, array $data): array
    {
        if ($this->isDuplicateVehicle($data['veh_number'], $id)) {
            return ['status' => 'failed', 'message' => 'Vehicle number already exists'];
        }

        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->updateRow('Vehicle', $data, 'id = ?', 'i', [$id])) {
            return ['status' => 'failed', 'message' => 'Failed to update record'];
        }

        return ['status' => 'success', 'message' => 'Updated Successfully!!'];
    }

    private function isDuplicateVehicle(string $vehNumber, int $excludeId): bool
    {
        return $this->countRows('Vehicle', "veh_number = ? AND status = '0' AND id != ?", 'si', [$vehNumber, $excludeId]) > 0;
    }

    /**
     * Soft delete one or more vehicles
     */
    public function delete(array $ids): array
    {
        $ids = $this->cleanIds($ids);

        if (empty($ids)) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $sql = "UPDATE Vehicle SET status = '1' WHERE id IN (" . $this->placeholders(count($ids)) . ")";
        if (!$this->executeWrite($sql, str_repeat('i', count($ids)), $ids)) {
            return ['status' => 'failed', 'message' => 'Failed to delete record'];
        }

        return ['status' => 'success', 'message' => 'Deleted'];
    }

    /**
     * Bulk insert vehicles from Excel. Customer and supplier codes must already exist; vehicle numbers must be new.
     */
    public function upload(array $rows): array
    {
        $errors = [];

        foreach ($rows as $row) {
            $text = fn(string $key): string => trim((string)($row[$key] ?? ''));

            $vehNumber = $text('VehicleNo');
            if ($vehNumber === '') {
                continue;
            }

            $customerCode = $text('CustomerCode');
            if ($customerCode !== '' && !$this->fetchOne("SELECT id FROM Customer WHERE customer_code = ? AND status = '0'", 's', [$customerCode])) {
                $errors[] = 'Customer: ' . $customerCode . " doesn't exist in master data.";
                continue;
            }

            $supplierCode = $text('SupplierCode');
            if ($supplierCode !== '' && !$this->fetchOne("SELECT id FROM Supplier WHERE supplier_code = ? AND status = '0'", 's', [$supplierCode])) {
                $errors[] = 'Supplier: ' . $supplierCode . " doesn't exist in master data.";
                continue;
            }

            if ($this->isDuplicateVehicle($vehNumber, 0)) {
                $errors[] = 'Vehicle: ' . $vehNumber . ' already exist in master data.';
                continue;
            }

            $this->insertRow('Vehicle', [
                'veh_number' => $vehNumber,
                'vehicle_weight' => $text('VehicleWeightKG'),
                'customer_code' => $customerCode,
                'customer_name' => $text('CustomerName'),
                'supplier_code' => $supplierCode,
                'supplier_name' => $text('SupplierName'),
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
