<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/SupplierService.php';

class SupplierController extends BaseController {

    private $supplierService;

    public function __construct($db) {
        parent::__construct($db);
        $this->supplierService = new SupplierService($db, $this->username);
    }

    /**
     * Get all suppliers (for DataTables)
     */
    public function getAll() {
        $result = $this->supplierService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new supplier
     */
    public function create() {
        $this->db->begin_transaction();
        try {
            $result = $this->supplierService->create($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Added Successfully!!', ['id' => $result['id']]);
    }

    /**
     * Update existing supplier
     */
    public function update() {
        $this->db->begin_transaction();
        try {
            $this->supplierService->update($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }

    /**
     * Delete (soft delete) supplier
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->supplierService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected suppliers
     */
    public function deleteMulti() {
        try {
            $this->supplierService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import suppliers from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->supplierService->importRows($rows);
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
     * Get single supplier by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->supplierService->get($id);
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
