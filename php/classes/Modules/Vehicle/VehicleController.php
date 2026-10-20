<?php
namespace App\Modules\Vehicle;

use App\Core\BaseController;

class VehicleController extends BaseController
{
    private const FIELDS = [
        'vehicleNo' => ['column' => 'veh_number', 'required' => true],
        'vehicleWeight' => ['column' => 'vehicle_weight'],
        'transporterCode' => ['column' => 'transporter_code'],
        'transporter' => ['column' => 'transporter_name'],
        'customerCode' => ['column' => 'customer_code'],
        'customer' => ['column' => 'customer_name'],
        'supplierCode' => ['column' => 'supplier_code'],
        'supplier' => ['column' => 'supplier_name']
    ];

    private VehicleService $service;

    public function __construct(VehicleService $service)
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
     * Create or update record
     */
    public function save(): array
    {
        if (!isset($_POST['vehicleNo'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $data = $this->collectFields(self::FIELDS);
        if ($data === null) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }
        $data['vehicle_weight'] = $data['vehicle_weight'] ?? 0;

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
