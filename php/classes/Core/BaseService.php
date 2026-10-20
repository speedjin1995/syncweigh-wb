<?php
namespace App\Core;

/**
 * Shared database helpers for all services.
 * One database per company, so there is no company column to scope by.
 */
abstract class BaseService
{
    // Columns holding each master data record's code / name in Weight and Weight_Container
    private const MASTER_DATA_COLUMNS = [
        'Customer' => ['code' => 'customer_code', 'name' => 'customer_name'],
        'Supplier' => ['code' => 'supplier_code', 'name' => 'supplier_name'],
        'Destination' => ['code' => 'destination_code', 'name' => 'destination'],
        'Product' => ['code' => 'product_code', 'name' => 'product_name'],
        'Raw Material' => ['code' => 'raw_mat_code', 'name' => 'raw_mat_name'],
        'Plant' => ['code' => 'plant_code', 'name' => 'plant_name']
    ];

    protected \mysqli $db;
    protected string $username;

    public function __construct(\mysqli $db, string $username)
    {
        $this->db = $db;
        $this->username = $username;
    }

    protected function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $stmt = $this->runQuery($sql, $types, $params);
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    protected function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $stmt = $this->runQuery($sql, $types, $params);
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    protected function countRows(string $from, string $where, string $types, array $params): int
    {
        $row = $this->fetchOne("SELECT COUNT(*) AS allcount FROM $from WHERE $where", $types, $params);

        return (int)($row['allcount'] ?? 0);
    }

    protected function executeWrite(string $sql, string $types, array $params): bool
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Insert column => value row, returns the new ID or 0 on failure
     */
    protected function insertRow(string $table, array $data): int
    {
        $columns = array_keys($data);
        $stmt = $this->db->prepare("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . $this->placeholders(count($columns)) . ")");
        if (!$stmt) {
            return 0;
        }
        $values = array_values($data);
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        $id = $stmt->execute() ? (int)$stmt->insert_id : 0;
        $stmt->close();

        return $id;
    }

    /**
     * Update column => value data on rows matching $where
     */
    protected function updateRow(string $table, array $data, string $where, string $types, array $params): bool
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = ?";
        }

        return $this->executeWrite(
            "UPDATE $table SET " . implode(', ', $sets) . " WHERE $where",
            str_repeat('s', count($data)) . $types,
            array_merge(array_values($data), $params)
        );
    }

    protected function cleanIds(array $ids): array
    {
        return array_values(array_filter(array_map('intval', $ids)));
    }

    protected function placeholders(int $count): string
    {
        return implode(',', array_fill(0, $count, '?'));
    }

    /**
     * When a master data code changes, update it in the weighing records that reference it
     */
    protected function updateMasterDataCodeValue(string $oldValue, string $newValue, string $module): void
    {
        $this->updateMasterDataReferences('code', $oldValue, $newValue, $module);
    }

    /**
     * When a master data name changes, update it in the weighing records that reference it
     */
    protected function updateMasterDataNameValue(string $oldValue, string $newValue, string $module): void
    {
        $this->updateMasterDataReferences('name', $oldValue, $newValue, $module);
    }

    private function updateMasterDataReferences(string $field, string $oldValue, string $newValue, string $module): void
    {
        if (!isset(self::MASTER_DATA_COLUMNS[$module])) {
            return;
        }

        $column = self::MASTER_DATA_COLUMNS[$module][$field];

        foreach (['Weight', 'Weight_Container'] as $table) {
            $stmt = $this->db->prepare("UPDATE $table SET $column = ? WHERE $column = ?");
            if (!$stmt) {
                throw new \Exception(static::class . ' - failed to prepare master data update');
            }
            $stmt->bind_param('ss', $newValue, $oldValue);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new \Exception(static::class . ' - failed to update master data references');
            }
            $stmt->close();
        }
    }

    private function runQuery(string $sql, string $types, array $params): \mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception(static::class . ' - failed to prepare query');
        }
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new \Exception(static::class . ' - failed to execute query');
        }

        return $stmt;
    }
}
