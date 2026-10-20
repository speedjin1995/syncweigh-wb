<?php
namespace App\Modules\User;

use App\Core\BaseController;

class UserController extends BaseController
{
    private UserService $service;

    public function __construct(UserService $service)
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
     * Create or update record. Plants are posted as an array and stored as a JSON list.
     */
    public function save(): array
    {
        if (!isset($_POST['employeeCode'], $_POST['username'], $_POST['useremail'], $_POST['roles'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $username = trim((string)$_POST['username']);
        $role = trim((string)$_POST['roles']);
        if ($username === '' || $role === '') {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $plants = isset($_POST['plantId']) && is_array($_POST['plantId']) ? array_values($_POST['plantId']) : [];

        $data = [
            'employee_code' => $this->optionalPost('employeeCode'),
            'username' => $username,
            'name' => $this->optionalPost('name'),
            'useremail' => $this->optionalPost('useremail'),
            'role' => $role,
            'plant_id' => json_encode($plants)
        ];

        $id = $this->postId();

        return $id ? $this->service->update($id, $data) : $this->service->create($data);
    }

    /**
     * Trimmed POST value, null when empty
     */
    private function optionalPost(string $key): ?string
    {
        $value = trim((string)($_POST[$key] ?? ''));

        return $value === '' ? null : $value;
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
