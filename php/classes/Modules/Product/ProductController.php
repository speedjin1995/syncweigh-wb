<?php
namespace App\Modules\Product;

use App\Core\BaseController;

class ProductController extends BaseController
{
    private const FIELDS = [
        'productCode' => ['column' => 'product_code'],
        'productName' => ['column' => 'name'],
        'description' => ['column' => 'description'],
        'varianceType' => ['column' => 'variance'],
        'high' => ['column' => 'high'],
        'low' => ['column' => 'low']
    ];

    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * DataTables server-side list
     */
    public function list(): array
    {
        $p = $this->dataTableParams();
        $result = $this->service->getList($p['start'], $p['length'], $p['orderColumn'], $p['orderDir'], $p['search']);

        return $this->dataTableResponse($p['draw'], $result);
    }

    /**
     * Get single record by ID
     */
    public function get(): array
    {
        if (!isset($_POST['id'])) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        return ['status' => 'success', 'message' => $this->service->getById($this->postId()) ?? []];
    }

    /**
     * Create or update record
     */
    public function save(): array
    {
        if (!isset($_POST['productCode'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $data = $this->collectFields(self::FIELDS);
        $id = $this->postId();

        return $id ? $this->service->update($id, $data) : $this->service->create($data);
    }

    /**
     * Soft delete single or multiple records
     */
    public function delete(): array
    {
        return $this->service->delete($this->postIds());
    }

    /**
     * Bulk upload from JSON body
     */
    public function upload(): array
    {
        $rows = $this->jsonBody();

        if ($rows === null) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        return $this->service->upload($rows);
    }
}
