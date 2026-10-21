<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/CustomerService.php';

class CustomerController extends BaseController {

    private $customerService;

    public function __construct($db) {
        parent::__construct($db);
        $this->customerService = new CustomerService($db, $this->username);
    }

    /**
     * Get all customers (for DataTables)
     */
    public function getAll() {
        $result = $this->customerService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new customer
     */
    public function create() {
        $this->db->begin_transaction();
        try {
            $result = $this->customerService->create($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Added Successfully!!', ['id' => $result['id']]);
    }

    /**
     * Update existing customer
     */
    public function update() {
        $this->db->begin_transaction();
        try {
            $this->customerService->update($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }

    /**
     * Delete (soft delete) customer
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->customerService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected customers
     */
    public function deleteMulti() {
        try {
            $this->customerService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import customers from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->customerService->importRows($rows);
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
     * Get single customer by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->customerService->get($id);
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
