<?php
require_once __DIR__ . '/BaseService.php';

class WeightService extends BaseService {

    protected $table = 'Weight';

    /**
     * Id of the newest pending record for the vehicle and transaction status, or null
     */
    public function getPendingIdByVehicle($vehicleNo, $transactionStatus, $locationId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE status = '0' AND is_complete = 'N' AND is_cancel = 'N' AND lorry_plate_no1 = ? AND transaction_status = ?";
        if ($locationId !== null) {
            $sql .= " AND location_id = ?";
        }
        $sql .= " ORDER BY id DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Unable to check pending records');
        }

        if ($locationId !== null) {
            $stmt->bind_param('ssi', $vehicleNo, $transactionStatus, $locationId);
        } else {
            $stmt->bind_param('ss', $vehicleNo, $transactionStatus);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Unable to check pending records');
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? (int) $row['id'] : null;
    }
}
