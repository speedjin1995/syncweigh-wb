<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/ProductService.php';

class ProductController extends BaseController {

    private $productService;

    public function __construct($db) {
        parent::__construct($db);
        $this->productService = new ProductService($db, $this->username);
    }

    /**
     * Get all products (for DataTables)
     */
    public function getAll() {
        $result = $this->productService->getAll($_POST);
        echo json_encode($result);
        exit();
    }

    /**
     * Create new product
     */
    public function create() {
        try {
            $result = $this->productService->create($_POST);
            $this->success('Added Successfully!!', ['id' => $result['id']]);
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Update existing product
     */
    public function update() {
        $this->db->begin_transaction();
        try {
            $this->productService->update($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }

    /**
     * Delete (soft delete) product
     */
    public function delete() {
        $id = $this->getRequiredPost('id');

        try {
            $this->productService->delete($id);
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Delete (soft delete) selected products
     */
    public function deleteMulti() {
        try {
            $this->productService->deleteMany($this->getPost('userID'));
            $this->success('Deleted');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Import products from Excel rows sent as a JSON body
     */
    public function upload() {
        $rows = json_decode(file_get_contents('php://input'), true);

        if (empty($rows)) {
            $this->failed('Please fill in all the fields');
        }

        $this->db->begin_transaction();
        try {
            $errors = $this->productService->importRows($rows);
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
     * Get single product by ID
     */
    public function get() {
        $id = $this->getRequiredPost('id');

        try {
            $row = $this->productService->get($id);
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
     * Standard price and customer prices of a product (for the pricing modal)
     */
    public function getPricing() {
        $id = $this->getRequiredPost('id');

        try {
            $pricing = $this->productService->getPricing($id);
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
     * Save the standard price and customer prices of a product
     */
    public function savePricing() {
        $this->db->begin_transaction();
        try {
            $this->productService->savePricing($_POST);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            $this->failed($e->getMessage());
        }
        $this->success('Updated Successfully!!');
    }
}
?>
