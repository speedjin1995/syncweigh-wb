<?php

/**
 * Base Service Class
 * All module services should extend this class
 */
class BaseService {
    protected $db;
    protected $username;

    // Columns holding each master data record's code / name in Weight and Weight_Container.
    // vehicle = Vehicle also stores it; the value is its name column in Vehicle.
    private static $masterDataColumns = [
        'Customer' => ['code' => 'customer_code', 'name' => 'customer_name', 'vehicle' => 'customer_name'],
        'Supplier' => ['code' => 'supplier_code', 'name' => 'supplier_name', 'vehicle' => 'supplier_name'],
        'Transporter' => ['code' => 'transporter_code', 'name' => 'transporter', 'vehicle' => 'transporter_name'],
        'Destination' => ['code' => 'destination_code', 'name' => 'destination'],
        'Product' => ['code' => 'product_code', 'name' => 'product_name'],
        'Raw Material' => ['code' => 'raw_mat_code', 'name' => 'raw_mat_name'],
        'Plant' => ['code' => 'plant_code', 'name' => 'plant_name'],
    ];

    public function __construct($db, $username) {
        $this->db = $db;
        $this->username = $username;
    }

    /**
     * True for values that must not be saved as master data:
     * null, empty / whitespace only, or "-" (the dropdown placeholder).
     */
    protected function isBlankValue($value) {
        if ($value === null) {
            return true;
        }
        $value = trim((string) $value);
        return $value === '' || $value === '-';
    }

    /**
     * True when the company id is missing or not a valid id.
     */
    protected function isInvalidCompanyId($companyId) {
        return $this->isBlankValue($companyId) || (int) $companyId <= 0;
    }

    /**
     * When a master data code changes, update it in the records that reference it.
     */
    public function updateMasterDataCodeValue($oldValue, $newValue, $module) {
        $this->updateMasterDataReferences('code', $oldValue, $newValue, $module);
    }

    /**
     * When a master data name changes, update it in the records that reference it.
     */
    public function updateMasterDataNameValue($oldValue, $newValue, $module) {
        $this->updateMasterDataReferences('name', $oldValue, $newValue, $module);
    }

    private function updateMasterDataReferences($field, $oldValue, $newValue, $module) {
        // A blank old value would match unrelated records
        if (!isset(self::$masterDataColumns[$module]) || $this->isBlankValue($oldValue)) {
            return;
        }

        $config = self::$masterDataColumns[$module];

        // Table => column to update
        $tables = ['Weight' => $config[$field], 'Weight_Container' => $config[$field]];
        if (!empty($config['vehicle'])) {
            $tables['Vehicle'] = $field === 'code' ? $config['code'] : $config['vehicle'];
        }

        foreach ($tables as $table => $column) {
            $stmt = $this->db->prepare("UPDATE {$table} SET {$column} = ? WHERE {$column} = ?");
            if (!$stmt) throw new Exception($this->db->error);
            $stmt->bind_param('ss', $newValue, $oldValue);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();
        }
    }
}
?>
