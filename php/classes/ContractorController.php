<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/ContractorService.php';

class ContractorController extends BaseController {

    private $contractorService;

    public function __construct($db) {
        parent::__construct($db);
        $this->contractorService = new ContractorService($db, $this->username);
    }

    public function handleList() {
        try {
            $list = $this->contractorService->getList();
            echo json_encode(['status' => 'success', 'data' => $list]);
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Get all contractors (for DataTables)
     */
    public function getAll() {
        $result = $this->contractorService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new contractor
     */
    public function create() {
        try {
            $result = $this->contractorService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing contractor
     */
    public function update() {
        try {
            $this->contractorService->update($_POST);
            $this->success('Updated Successfully!!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) contractor
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->contractorService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Reactivate contractor
     */
    public function reactivate() {
        $id = $this->getRequiredPost('id');

        try {
            $this->contractorService->reactivate($id);
            $this->success('Reactivated');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected contractors
     */
    public function deleteMulti() {
        try {
            $this->contractorService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import contractors from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->contractorService->importRows($rows);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }

        if (!empty($errors)) {
            echo json_encode(array("status" => "error", "message" => $errors));
            exit();
        }

        $this->success('Added Successfully!!');
    }

    /**
     * Get single contractor by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->contractorService->get($id);
            if ($row) {
                $this->success($row);
            } else {
                $this->failed('Record not found');
            }
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }
}
?>
