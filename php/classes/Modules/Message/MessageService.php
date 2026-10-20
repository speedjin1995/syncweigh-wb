<?php
namespace App\Modules\Message;

use App\Core\BaseService;

/**
 * Translation resource keys (message_resource). Rows are hard deleted, as the legacy endpoint did.
 */
class MessageService extends BaseService
{
    private const SORT_COLUMNS = ['id', 'message_key_code', 'en', 'zh', 'my', 'ne'];

    private const FORM_COLUMNS = 'id, message_key_code, en, zh, my, ne';

    /**
     * Paginated message keys for DataTables
     */
    public function getList(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $totalRecords = $this->countRows('message_resource', '1=1', '', []);

        $where = '1=1';
        $params = [];
        $types = '';
        if ($search !== '') {
            $where = 'message_key_code LIKE ?';
            $params = ['%' . $search . '%'];
            $types = 's';
        }
        $totalFiltered = $this->countRows('message_resource', $where, $types, $params);

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $params[] = $start;
        $params[] = $length;
        $types .= 'ii';

        $rows = $this->fetchAll("SELECT " . self::FORM_COLUMNS . " FROM message_resource WHERE $where ORDER BY $sort $dir LIMIT ?, ?", $types, $params);

        $data = [];
        foreach ($rows as $i => $row) {
            $data[] = [
                'counter' => $i + 1,
                'id' => $row['id'],
                'message_key_code' => $row['message_key_code'],
                'en' => $row['en'],
                'zh' => $row['zh'],
                'my' => $row['my'],
                'ne' => $row['ne']
            ];
        }

        return ['totalRecords' => $totalRecords, 'totalFiltered' => $totalFiltered, 'data' => $data];
    }

    /**
     * Single message key by ID
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT " . self::FORM_COLUMNS . " FROM message_resource WHERE id = ?", 'i', [$id]);
    }

    public function save(int $id, array $data): array
    {
        if ($this->isDuplicateKey($data['message_key_code'], $id)) {
            return ['status' => 'failed', 'message' => 'Message code already exists'];
        }

        if ($id) {
            if (!$this->updateRow('message_resource', $data, 'id = ?', 'i', [$id])) {
                return ['status' => 'failed', 'message' => 'Failed to update record'];
            }

            return ['status' => 'success', 'message' => 'Updated Successfully!!'];
        }

        if (!$this->insertRow('message_resource', $data)) {
            return ['status' => 'failed', 'message' => 'Failed to add record'];
        }

        return ['status' => 'success', 'message' => 'Added Successfully!!'];
    }

    private function isDuplicateKey(string $key, int $excludeId): bool
    {
        return $this->countRows('message_resource', 'message_key_code = ? AND id != ?', 'si', [$key, $excludeId]) > 0;
    }

    /**
     * Hard delete, as the legacy endpoint did
     */
    public function delete(array $ids): array
    {
        $ids = $this->cleanIds($ids);

        if (empty($ids)) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        $sql = "DELETE FROM message_resource WHERE id IN (" . $this->placeholders(count($ids)) . ")";
        if (!$this->executeWrite($sql, str_repeat('i', count($ids)), $ids)) {
            return ['status' => 'failed', 'message' => 'Something went wrong'];
        }

        return ['status' => 'success', 'message' => 'Deleted Successfully'];
    }
}
