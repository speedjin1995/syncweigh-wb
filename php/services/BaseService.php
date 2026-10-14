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
     * True when the feature is active and enabled for the company.
     */
    public function isFeatureEnabled($featureCode, $companyId = '1') {
        $enabled = false;

        $stmt = $this->db->prepare("SELECT cf.enabled FROM Company_Features cf JOIN Features f ON f.feature_code = cf.feature_code AND f.status = '0' WHERE cf.company_id = ? AND cf.feature_code = ?");
        if ($stmt) {
            $stmt->bind_param('ss', $companyId, $featureCode);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $enabled = $row['enabled'] == '1';
                }
            }
            $stmt->close();
        }

        return $enabled;
    }

    /**
     * Active features with their on/off state for the company.
     */
    public function getCompanyFeatures($companyId = '1') {
        $features = [];

        $stmt = $this->db->prepare("SELECT f.feature_code, f.feature_name, f.description, (SELECT cf.enabled FROM Company_Features cf WHERE cf.company_id = ? AND cf.feature_code = f.feature_code ORDER BY cf.id LIMIT 1) AS enabled FROM Features f WHERE f.status = '0' ORDER BY f.id");
        if ($stmt) {
            $stmt->bind_param('s', $companyId);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $row['enabled'] = $row['enabled'] == '1';
                    $features[] = $row;
                }
            }
            $stmt->close();
        }

        return $features;
    }

    /**
     * Turn a feature on or off for the company. Creates the company row if it does not exist yet.
     */
    public function saveCompanyFeature($featureCode, $enabled, $companyId = '1') {
        $stmt = $this->db->prepare("SELECT id FROM Features WHERE feature_code = ? AND status = '0'");
        if (!$stmt) throw new Exception('Something went wrong!');
        $stmt->bind_param('s', $featureCode);
        if (!$stmt->execute()) throw new Exception('Something went wrong!');
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        if (!$exists) throw new Exception('Feature not found');

        $enabled = $enabled ? 1 : 0;
        $today = date("Y-m-d H:i:s");

        $stmt = $this->db->prepare("UPDATE Company_Features SET enabled = ?, modified_by = ?, modified_date = ? WHERE company_id = ? AND feature_code = ?");
        if (!$stmt) throw new Exception('Something went wrong!');
        $stmt->bind_param('issss', $enabled, $this->username, $today, $companyId, $featureCode);
        if (!$stmt->execute()) throw new Exception('Something went wrong!');
        $stmt->close();

        $stmt = $this->db->prepare("SELECT id FROM Company_Features WHERE company_id = ? AND feature_code = ?");
        if (!$stmt) throw new Exception('Something went wrong!');
        $stmt->bind_param('ss', $companyId, $featureCode);
        if (!$stmt->execute()) throw new Exception('Something went wrong!');
        $stmt->store_result();
        $hasRow = $stmt->num_rows > 0;
        $stmt->close();

        if (!$hasRow) {
            $stmt = $this->db->prepare("INSERT INTO Company_Features (company_id, feature_code, enabled, created_by, created_date) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) throw new Exception('Something went wrong!');
            $stmt->bind_param('ssiss', $companyId, $featureCode, $enabled, $this->username, $today);
            if (!$stmt->execute()) throw new Exception('Something went wrong!');
            $stmt->close();
        }
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
