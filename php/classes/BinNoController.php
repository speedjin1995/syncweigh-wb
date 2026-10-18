<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/BinNoService.php';

class BinNoController extends BaseController {

    private $binNoService;

    public function __construct($db) {
        parent::__construct($db);
        $this->binNoService = new BinNoService($db, $this->username);
    }

    public function handleList() {
        try {
            $list = $this->binNoService->getList();
            echo json_encode(['status' => 'success', 'data' => $list]);
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Get all bin numbers (for DataTables)
     */
    public function getAll() {
        $result = $this->binNoService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new bin no
     */
    public function create() {
        try {
            $result = $this->binNoService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing bin no
     */
    public function update() {
        try {
            $this->binNoService->update($_POST);
            $this->success('Updated Successfully!!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) bin no
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->binNoService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Reactivate bin no
     */
    public function reactivate() {
        $id = $this->getRequiredPost('id');

        try {
            $this->binNoService->reactivate($id);
            $this->success('Reactivated');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected bin numbers
     */
    public function deleteMulti() {
        try {
            $this->binNoService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import bin numbers from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->binNoService->importRows($rows);
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
     * Get single bin no by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->binNoService->get($id);
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
