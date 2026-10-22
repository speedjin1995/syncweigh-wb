<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/WeightService.php';
require_once __DIR__ . '/../services/WeightPrintService.php';
require_once __DIR__ . '/../requires/lookup.php';

class WeightController extends BaseController {

    private $weightService;
    private $printService;

    public function __construct($db) {
        parent::__construct($db);
        $this->weightService = new WeightService($db, $this->username);
        $this->printService = new WeightPrintService($db, $this->username);
    }

    /**
     * Weighing slip HTML (file = weight). Report and weighing screens check the Print permission on the record's status first.
     */
    public function print() {
        $source = $_POST['source'] ?? '';

        if (in_array($source, ['report', 'weighing'], true)) {
            try {
                if (!$this->printService->canPrint($_POST['userID'] ?? null, $_POST['isEmptyContainer'] ?? 'N', $source)) {
                    $this->failed('You do not have permission to print this record');
                }
            } catch (Exception $e) {
                $this->failed($e->getMessage());
            }
        }

        if (!isset($_POST['userID'], $_POST['file'], $_POST['isEmptyContainer'])) {
            $this->failed('Please fill in all the fields');
        }

        if ($_POST['file'] != 'weight') {
            $this->failed('Something Goes Wrong');
        }

        try {
            $this->success($this->printService->slipHtml((string) $_POST['userID'], (string) $_POST['isEmptyContainer']));
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
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

    /**
     * One weighing record for the edit form (format MODAL, the default) or the row detail (format EXPANDABLE)
     */
    public function get() {
        if (!isset($_POST['userID'])) {
            $this->failed('Missing Attribute');
        }

        $format = ($_POST['format'] ?? '') !== '' ? (string) $_POST['format'] : 'MODAL';
        $type = ($_POST['type'] ?? '') !== '' ? (string) $_POST['type'] : 'Weight';

        try {
            $this->success($this->weightService->getWeight(trim($_POST['userID']), $format, $type));
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Save the weighing form (create, or update when id is posted), every weight type
     */
    public function save() {
        if (!isset($_POST['transactionId'], $_POST['transactionStatus'], $_POST['weightType'], $_POST['transactionDate'], $_POST['grossIncoming'], $_POST['grossIncomingDate'], $_POST['manualWeight'], $_POST['plantCode'], $_POST['plant'])) {
            $this->failed('Please fill in all the fields');
        }

        try {
            echo json_encode($this->weightService->save($_POST, $_SESSION['id']));
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Cancel weighing record(s): cancelReason and isEmptyContainer are required
     */
    public function delete() {
        echo json_encode($this->weightService->cancel($_POST));
        exit();
    }

    /**
     * Weighing screen list (DataTables) with the status counts for the summary cards
     */
    public function list() {
        echo json_encode($this->weightService->getList($_POST, $this->plants()));
        exit();
    }

    /**
     * Completed empty container list (DataTables)
     */
    public function listEmptyContainer() {
        echo json_encode($this->weightService->getEmptyContainerList($_POST));
        exit();
    }

    /**
     * Completed empty containers for a transaction status (posted as userID)
     */
    public function containers() {
        if (!isset($_POST['userID'])) {
            $this->failed('Missing Attribute');
        }

        try {
            $this->success($this->weightService->getContainers(trim($_POST['userID'])));
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * First weighing of a completed empty container by container no (posted as userID)
     */
    public function emptyContainer() {
        if (!isset($_POST['userID'])) {
            $this->failed('Missing Attribute');
        }

        try {
            $this->success($this->weightService->getEmptyContainer(trim($_POST['userID'])));
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Admins see every plant; everyone else only their assigned plants
     */
    private function plants() {
        if (in_array($_SESSION['roles'] ?? '', ['ADMIN', 'SADMIN'], true)) {
            return null;
        }

        return array_values((array) ($_SESSION['plant'] ?? []));
    }
}
?>
