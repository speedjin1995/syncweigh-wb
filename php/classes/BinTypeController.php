<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/BinTypeService.php';

class BinTypeController extends BaseController {

    private $binTypeService;

    public function __construct($db) {
        parent::__construct($db);
        $this->binTypeService = new BinTypeService($db, $this->username);
    }

    public function handleList() {
        try {
            $list = $this->binTypeService->getList();
            echo json_encode(['status' => 'success', 'data' => $list]);
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Get all bin types (for DataTables)
     */
    public function getAll() {
        $result = $this->binTypeService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new bin type
     */
    public function create() {
        try {
            $result = $this->binTypeService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing bin type
     */
    public function update() {
        try {
            $this->binTypeService->update($_POST);
            $this->success('Updated Successfully!!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) bin type
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->binTypeService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Reactivate bin type
     */
    public function reactivate() {
        $id = $this->getRequiredPost('id');

        try {
            $this->binTypeService->reactivate($id);
            $this->success('Reactivated');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected bin types
     */
    public function deleteMulti() {
        try {
            $this->binTypeService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import bin types from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->binTypeService->importRows($rows);
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
     * Get single bin type by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->binTypeService->get($id);
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
