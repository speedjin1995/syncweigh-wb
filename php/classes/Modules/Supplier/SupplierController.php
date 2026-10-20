<?php
namespace App\Modules\Supplier;

use App\Core\BaseController;

class SupplierController extends BaseController
{
    private const FIELDS = [
        'supplierCode' => ['column' => 'supplier_code'],
        'companyRegNo' => ['column' => 'company_reg_no'],
        'newRegNo' => ['column' => 'new_reg_no'],
        'companyName' => ['column' => 'name'],
        'addressLine1' => ['column' => 'address_line_1'],
        'addressLine2' => ['column' => 'address_line_2'],
        'addressLine3' => ['column' => 'address_line_3'],
        'phoneNo' => ['column' => 'phone_no'],
        'faxNo' => ['column' => 'fax_no'],
        'contactName' => ['column' => 'contact_name'],
        'icNo' => ['column' => 'ic_no'],
        'tinNo' => ['column' => 'tin_no']
    ];

    private SupplierService $service;

    public function __construct(SupplierService $service)
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
        if (!isset($_POST['supplierCode'])) {
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
