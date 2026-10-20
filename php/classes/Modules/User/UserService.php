<?php
namespace App\Modules\User;

use App\Core\BaseService;

class UserService extends BaseService
{
    private const SORT_COLUMNS = [
        'id' => 'Users.id',
        'employee_code' => 'Users.employee_code',
        'username' => 'Users.username',
        'useremail' => 'Users.useremail',
        'name' => 'Users.name',
        'role' => 'roles.role_name',
        'status' => 'Users.status'
    ];

    private string $role;

    /** @var string[] */
    private array $plantIds;

    public function __construct(\mysqli $db, string $username, string $role, array $plantIds)
    {
        parent::__construct($db, $username);
        $this->role = $role;
        $this->plantIds = array_values(array_map('strval', $plantIds));
    }

    private function isAdmin(): bool
    {
        return in_array($this->role, ['ADMIN', 'SADMIN'], true);
    }

    /**
     * Non-admins only see users assigned to one of their plants. A non-admin with no plant sees nobody.
     */
    private function applyPlantScope(string &$where, array &$params, string &$types): void
    {
        if ($this->isAdmin()) {
            return;
        }

        if (empty($this->plantIds)) {
            $where .= ' AND 1 = 0';
            return;
        }

        $where .= ' AND (' . implode(' OR ', array_fill(0, count($this->plantIds), 'JSON_CONTAINS(Users.plant_id, ?)')) . ')';
        foreach ($this->plantIds as $plantId) {
            $params[] = json_encode($plantId);
            $types .= 's';
        }
    }

    /**
     * Paginated active users for DataTables (SADMIN accounts are never listed)
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $base = "FROM Users JOIN roles ON Users.role = roles.role_code WHERE Users.status = '0' AND Users.role <> 'SADMIN'";

        $scope = '';
        $scopeParams = [];
        $scopeTypes = '';
        $this->applyPlantScope($scope, $scopeParams, $scopeTypes);

        $totalRecords = (int)($this->fetchOne("SELECT COUNT(*) AS allcount " . $base . $scope, $scopeTypes, $scopeParams)['allcount'] ?? 0);

        $where = $base . $scope;
        $params = $scopeParams;
        $types = $scopeTypes;
        if ($search !== '') {
            $where .= " AND (Users.username LIKE ? OR Users.useremail LIKE ? OR roles.role_name LIKE ?)";
            array_push($params, '%' . $search . '%', '%' . $search . '%', '%' . $search . '%');
            $types .= 'sss';
        }
        $totalFiltered = (int)($this->fetchOne("SELECT COUNT(*) AS allcount " . $where, $types, $params)['allcount'] ?? 0);

        $sort = self::SORT_COLUMNS[$orderColumn] ?? 'Users.id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT Users.id, Users.employee_code, Users.username, Users.useremail, Users.name, roles.role_name, Users.plant_id, Users.status " . $where . " ORDER BY Users.status ASC, $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row['id'],
                'employee_code' => $row['employee_code'],
                'username' => $row['username'],
                'name' => $row['name'] ?? '',
                'useremail' => $row['useremail'],
                'role' => $row['role_name'],
                'plant' => $this->plantNames($row['plant_id']),
                'status' => $row['status']
            ];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Plant names for a stored JSON list of plant IDs
     */
    private function plantNames(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }

        $names = [];
        foreach ((array)json_decode($json, true) as $plantId) {
            $row = $this->fetchOne("SELECT name FROM Plant WHERE id = ?", 's', [(string)$plantId]);
            $names[] = $row['name'] ?? '0';
        }

        return $names;
    }

    /**
     * Single user by ID (plant is the stored JSON list, as the edit form expects it)
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT id, employee_code, username, name, useremail, role AS role_code, plant_id AS plant FROM Users WHERE id = ?", 'i', [$id]);
    }

    public function create(array $data): array
    {
        if ($duplicate = $this->duplicateError($data, 0)) {
            return $duplicate;
        }

        $data['password'] = password_hash('123456', PASSWORD_DEFAULT);
        $data['token'] = bin2hex(random_bytes(50));
        $data['created_by'] = $this->username;
        $data['modified_by'] = $this->username;

        if (!$this->insertRow('Users', $data)) {
            return ['status' => 'failed', 'message' => 'Failed to add record'];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }

    /**
     * Update existing user. The password and created_by are not changed, as the legacy endpoint did not change them.
     */
    public function update(int $id, array $data): array
    {
        if ($duplicate = $this->duplicateError($data, $id)) {
            return $duplicate;
        }

        $data['modified_by'] = $this->username;

        if (!$this->updateRow('Users', $data, 'id = ?', 'i', [$id])) {
            return ['status' => 'failed', 'message' => 'Failed to update record'];
        }

        return ['status' => 'success', 'message' => 'Updated Successfully!!'];
    }

    /**
     * Username and employee code must be unique among active users
     */
    private function duplicateError(array $data, int $excludeId): ?array
    {
        if (!empty($data['username']) && $this->isDuplicate('username', $data['username'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Username already exists'];
        }

        if (!empty($data['employee_code']) && $this->isDuplicate('employee_code', $data['employee_code'], $excludeId)) {
            return ['status' => 'failed', 'message' => 'Employee code already exists'];
        }

        return null;
    }

    private function isDuplicate(string $column, string $value, int $excludeId): bool
    {
        return $this->countRows('Users', "$column = ? AND status = '0' AND id != ?", 'si', [$value, $excludeId]) > 0;
    }

    /**
     * Soft delete one or more users
     */
    public function delete(array $ids): array
    {
        $ids = $this->cleanIds($ids);

        if (empty($ids)) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $sql = "UPDATE Users SET status = '1' WHERE id IN (" . $this->placeholders(count($ids)) . ")";
        if (!$this->executeWrite($sql, str_repeat('i', count($ids)), $ids)) {
            return ['status' => 'failed', 'message' => 'Failed to delete record'];
        }

        return ['status' => 'success', 'message' => 'Deleted'];
    }

    /**
     * Bulk insert users from Excel. New users get the default password and a token, as the form does.
     */
    public function upload(array $rows): array
    {
        $errors = [];

        foreach ($rows as $row) {
            $text = fn(string $key): string => trim((string)($row[$key] ?? ''));

            $employeeCode = $text('EmployeeCode');
            if ($employeeCode === '') {
                continue;
            }

            if ($this->isDuplicate('employee_code', $employeeCode, 0)) {
                $errors[] = 'User: ' . $employeeCode . ' already exist in master data.';
                continue;
            }

            $this->insertRow('Users', [
                'employee_code' => $employeeCode,
                'username' => $text('Username'),
                'name' => $text('UserName'),
                'useremail' => $text('UserEmail'),
                'role' => $text('Role'),
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'token' => bin2hex(random_bytes(50)),
                'created_by' => $this->username,
                'modified_by' => $this->username
            ]);
        }

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => $errors];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }
}
