<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/WeightService.php';
require_once __DIR__ . '/../requires/lookup.php';

class WeightController extends BaseController {

    private $weightService;

    public function __construct($db) {
        parent::__construct($db);
        $this->weightService = new WeightService($db, $this->username);
    }

    /**
     * Pending record for a vehicle and transaction status (second weighing)
     */
    public function getPendingByVehicle() {
        $vehicleNo = $this->getRequiredPost('vehicleNo');
        $transactionStatus = $this->getRequiredPost('transactionStatus');

        if (!isTransactionStatusEnabled($transactionStatus, $this->db)) {
            $this->failed('Transaction status is not enabled');
        }

        try {
            $id = $this->weightService->getPendingIdByVehicle($vehicleNo, $transactionStatus);
            $this->success('', ['id' => $id]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }
}
