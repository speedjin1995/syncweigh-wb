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
     * Upcoming limit ranges (for DataTables)
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
     * Add or edit one planned schedule row from the form (id present = edit)
     */
    public function save() {
        $id = $this->getPost('id');

        if ($id !== null && !ctype_digit($id)) {
            $this->failed('Invalid planned schedule row');
        }

        $this->db->begin_transaction();
        try {
            $this->plannedScheduleService->saveRow($_POST, $id);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }

        $this->success($id === null ? 'Planned schedule added' : 'Planned schedule updated');
    }

    /**
     * Soft delete one planned schedule row
     */
    public function deactivate() {
        $id = $this->getRequiredPost('id');

        if (!ctype_digit($id)) {
            $this->failed('Invalid planned schedule row');
        }

        try {
            $this->plannedScheduleService->deactivate($id);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }

        $this->success('Planned schedule deleted');
    }

    /**
     * Soft delete several planned schedule rows (ids sent as an array)
     */
    public function deactivateMulti() {
        $ids = $this->getPost('ids');

        if (!is_array($ids) || count($ids) === 0) {
            $this->failed('Please select at least one planned schedule');
        }

        foreach ($ids as $id) {
            if (!ctype_digit((string)$id)) {
                $this->failed('Invalid planned schedule row');
            }
        }

        $this->db->begin_transaction();
        try {
            foreach ($ids as $id) {
                $this->plannedScheduleService->deactivate($id);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }

        $this->success(count($ids) . ' planned schedule(s) deleted');
    }

    /**
     * Change log rows of one planned schedule row (message = list of log rows)
     */
    public function getLogs() {
        $id = $this->getRequiredPost('id');

        if (!ctype_digit($id)) {
            $this->failed('Invalid planned schedule row');
        }

        try {
            $logs = $this->plannedScheduleService->getLogs($id);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }

        $this->success($logs);
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

        $this->success("{$result['vehicles']} vehicles and {$result['ranges']} date ranges saved");
    }
}
?>
