<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/CurrencyService.php';

class CurrencyController extends BaseController {

    private $currencyService;

    public function __construct($db) {
        parent::__construct($db);
        $this->currencyService = new CurrencyService($db, $this->username);
    }

    public function handleList() {
        try {
            $list = $this->currencyService->getList();
            echo json_encode(['status' => 'success', 'data' => $list]);
            exit();
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Get all currencies (for DataTables)
     */
    public function getAll() {
        $result = $this->currencyService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new currency
     */
    public function create() {
        try {
            $result = $this->currencyService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing currency
     */
    public function update() {
        try {
            $this->currencyService->update($_POST);
            $this->success('Updated Successfully!!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) currency
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->currencyService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }


    /**
     * Delete (soft delete) selected currencies
     */
    public function deleteMulti() {
        try {
            $this->currencyService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import currencies from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->currencyService->importRows($rows);
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
     * Get single currency by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->currencyService->get($id);
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
