<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/RawMaterialService.php';

class RawMaterialController extends BaseController {

    private $rawMaterialService;

    public function __construct($db) {
        parent::__construct($db);
        $this->rawMaterialService = new RawMaterialService($db, $this->username);
    }

    /**
     * Get all raw materials (for DataTables)
     */
    public function getAll() {
        $result = $this->rawMaterialService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new raw material
     */
    public function create() {
        try {
            $result = $this->rawMaterialService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing raw material
     */
    public function update() {
        $this->db->begin_transaction();
        try {
            $this->rawMaterialService->update($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }

    /**
     * Delete (soft delete) raw material
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->rawMaterialService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected raw materials
     */
    public function deleteMulti() {
        try {
            $this->rawMaterialService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import raw materials from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->rawMaterialService->importRows($rows);
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
     * Get single raw material by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->rawMaterialService->get($id);
            if ($row) {
                $this->success($row);
            } else {
                $this->failed('Record not found');
            }
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Standard price and supplier prices of a raw material (for the pricing modal)
     */
    public function getPricing() {
        $id = $this->getRequiredPost('id');

        try {
            $pricing = $this->rawMaterialService->getPricing($id);
            if ($pricing) {
                $this->success($pricing);
            } else {
                $this->failed('Record not found');
            }
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Save the standard price and supplier prices of a raw material
     */
    public function savePricing() {
        $this->db->begin_transaction();
        try {
            $this->rawMaterialService->savePricing($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }
}
?>
