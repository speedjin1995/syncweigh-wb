<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/PlannedScheduleService.php';

class PlannedScheduleController extends BaseController {

    private $plannedScheduleService;

    public function __construct($db) {
        parent::__construct($db);
        $this->plannedScheduleService = new PlannedScheduleService($db, $this->username);
    }

    /**
     * Upcoming daily limits (for DataTables)
     */
    public function getAll() {
        try {
            $result = $this->plannedScheduleService->getAll($_POST);
            echo json_encode($result);
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import vehicle schedule rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $result = $this->plannedScheduleService->importRows($rows);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }

        if (!empty($result['errors'])) {
            echo json_encode(array("status" => "error", "message" => $result['errors']));
            exit();
        }

        $this->success("{$result['vehicles']} vehicles and {$result['days']} daily limits saved");
    }
}
?>
