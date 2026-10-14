<?php
require_once __DIR__ . '/../services/BaseService.php';

/**
 * Base Controller Class
 * All module controllers should extend this class
 */
class BaseController {
    protected $db;
    protected $username;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct($db) {
        $this->db = $db;
        $this->username = $_SESSION['username'] ?? 'system';
    }
    
    /**
     * Send JSON response — any extra key-value pairs in $extra are merged at top level
     */
    protected function response($status, $message, $extra = []) {
        $response = array_merge(['status' => $status, 'message' => $message], $extra);
        echo json_encode($response);
        exit();
    }
    
    /**
     * Success response
     */
    protected function success($message, $extra = []) {
        $this->response('success', $message, $extra);
    }
    
    /**
     * Failed response
     */
    protected function failed($message, $extra = []) {
        $this->response('failed', $message, $extra);
    }
    
    /**
     * Check for duplicate value
     */
    protected function isDuplicate($column, $value, $excludeId = null) {
        $sql = "SELECT {$this->primaryKey} FROM {$this->table} WHERE {$column} = ? AND status = 0";
        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('si', $value, $excludeId);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('s', $value);
        }
        $stmt->execute();
        $stmt->store_result();
        $isDuplicate = $stmt->num_rows > 0;
        $stmt->close();
        return $isDuplicate;
    }
    
    /**
     * True when the feature is enabled for the company
     */
    public function isFeatureEnabled($featureCode, $companyId = '1') {
        $service = new BaseService($this->db, $this->username);
        return $service->isFeatureEnabled($featureCode, $companyId);
    }

    /**
     * Active features with their on/off state for the company
     */
    public function getCompanyFeatures($companyId = '1') {
        $service = new BaseService($this->db, $this->username);
        return $service->getCompanyFeatures($companyId);
    }

    /**
     * Turn a feature on or off (SADMIN only)
     */
    public function saveCompanyFeature() {
        if (($_SESSION['roles'] ?? '') != 'SADMIN') {
            $this->failed('You do not have permission to change features');
        }

        $featureCode = $this->getRequiredPost('featureCode');
        $enabled = $this->getPost('enabled') == '1';

        try {
            $service = new BaseService($this->db, $this->username);
            $service->saveCompanyFeature($featureCode, $enabled);
            $this->success('Updated Successfully!!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Get optional POST value (handles arrays)
     */
    protected function getPost($key, $default = null) {
        if (!isset($_POST[$key]) || $_POST[$key] === '') {
            return $default;
        }
        return is_array($_POST[$key]) ? $_POST[$key] : trim($_POST[$key]);
    }
    
    /**
     * Get required POST value
     */
    protected function getRequiredPost($key) {
        if (!isset($_POST[$key]) || $_POST[$key] === '') {
            $this->failed("Field '{$key}' is required");
        }
        return trim($_POST[$key]);
    }
}
?>
