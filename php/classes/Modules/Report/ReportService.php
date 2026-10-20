<?php
namespace App\Modules\Report;

use App\Core\BaseService;

class ReportService extends BaseService
{
    // Sort columns the report table may use; anything else falls back to id
    private const SORT_COLUMNS = ['id', 'transaction_id', 'weight_type', 'transaction_status', 'transaction_date', 'container_no', 'seal_no', 'lorry_plate_no1', 'gross_weight1', 'gross_weight1_date', 'tare_weight1', 'tare_weight1_date', 'nett_weight1', 'lorry_plate_no2', 'gross_weight2', 'gross_weight2_date', 'tare_weight2', 'tare_weight2_date', 'nett_weight2'];

    /**
     * Paginated weight records for the report table, with the transaction type counts for the summary cards.
     * $plants is null when the user may see every plant.
     */
    public function getList(array $filters, ?array $plants, int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $base = ["status = '0'"];
        $baseParams = [];
        $baseTypes = '';

        if ($plants !== null) {
            $base[] = $plants === [] ? '1=0' : 'plant_code IN (' . $this->placeholders(count($plants)) . ')';
            array_push($baseParams, ...$plants);
            $baseTypes .= str_repeat('s', count($plants));
        }

        [$clauses, $types, $params] = $search !== '' ? $this->searchClause($search) : $this->filterClauses($filters);

        $whereBase = implode(' AND ', $base);
        $whereAll = implode(' AND ', array_merge($base, $clauses));

        $totalRecords = $this->countRows('Weight', $whereBase, $baseTypes, $baseParams);
        $totalFiltered = $this->countRows('Weight', $whereAll, $baseTypes . $types, array_merge($baseParams, $params));

        $sort = in_array($orderColumn, self::SORT_COLUMNS, true) ? $orderColumn : 'id';
        $dir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

        $rows = $this->fetchAll("SELECT * FROM Weight WHERE $whereAll ORDER BY $sort $dir LIMIT ?, ?", $baseTypes . $types . 'ii', array_merge($baseParams, $params, [$start, $length]));

        // Any status other than Sales, Purchase and Misc is counted as Local, as the legacy report did
        $counts = ['Dispatch' => 0, 'Receiving' => 0, 'Internal Transfer' => 0, 'Miscellaneous' => 0];
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row = $this->formatRow($row);
            $counts[$row['transaction_status']]++;
        }

        return [
            'totalRecords' => $totalRecords,
            'totalFiltered' => $totalFiltered,
            'data' => $data,
            'salesTotal' => $counts['Dispatch'],
            'purchaseTotal' => $counts['Receiving'],
            'localTotal' => $counts['Internal Transfer'],
            'miscTotal' => $counts['Miscellaneous']
        ];
    }

    /**
     * Search box: matches transaction id or plate and ignores the other filters, as the legacy report did
     */
    private function searchClause(string $search): array
    {
        $like = '%' . $search . '%';

        return [["(transaction_id LIKE ? OR lorry_plate_no1 LIKE ?)"], 'ss', [$like, $like]];
    }

    /**
     * Filter panel: each set value adds one condition. Empty or '-' means no filter.
     */
    private function filterClauses(array $filters): array
    {
        $clauses = [];
        $types = '';
        $params = [];

        $add = function (string $sql, string $type, array $values) use (&$clauses, &$types, &$params) {
            $clauses[] = $sql;
            $types .= $type;
            array_push($params, ...$values);
        };

        $value = fn (string $key): string => trim((string)($filters[$key] ?? ''));
        $set = fn (string $key): bool => !in_array($value($key), ['', '-'], true);

        if ($set('fromDate')) {
            $date = \DateTime::createFromFormat('d-m-Y', $value('fromDate'));
            if ($date) {
                $add("transaction_date >= ?", 's', [$date->format('Y-m-d 00:00:00')]);
            }
        }

        if ($set('toDate')) {
            $date = \DateTime::createFromFormat('d-m-Y', $value('toDate'));
            if ($date) {
                $add("transaction_date <= ?", 's', [$date->format('Y-m-d 23:59:59')]);
            }
        }

        $equals = [
            'transactionStatus' => 'transaction_status',
            'customer' => 'customer_code',
            'supplier' => 'supplier_code',
            'customerType' => 'customer_type',
            'weightType' => 'weight_type',
            'product' => 'product_code',
            'rawMaterial' => 'raw_mat_code',
            'destination' => 'destination',
            'plant' => 'plant_code'
        ];
        foreach ($equals as $key => $column) {
            if ($set($key)) {
                $add("$column = ?", 's', [$value($key)]);
            }
        }

        if ($set('vehicle')) {
            $add("lorry_plate_no1 LIKE ?", 's', ['%' . $value('vehicle') . '%']);
        }

        if ($set('invDelPo')) {
            $like = '%' . $value('invDelPo') . '%';
            $add("(purchase_order LIKE ? OR invoice_no LIKE ? OR delivery_no LIKE ?)", 'sss', [$like, $like, $like]);
        }

        // Only Complete and Cancelled are filters; no status (or '-') means complete records only
        if ($set('status')) {
            if ($value('status') === 'Complete') {
                $clauses[] = "is_complete = 'Y'";
            } elseif ($value('status') === 'Cancelled') {
                $clauses[] = "is_cancel = 'Y'";
            }
        } else {
            $clauses[] = "is_complete = 'Y'";
        }

        return [$clauses, $types, $params];
    }

    /**
     * Response row with the labels and fields the report table uses
     */
    private function formatRow(array $row): array
    {
        $statusLabels = ['Sales' => 'Dispatch', 'Purchase' => 'Receiving', 'Misc' => 'Miscellaneous'];
        $transactionStatus = $statusLabels[$row['transaction_status']] ?? 'Internal Transfer';

        $weightTypeLabels = ['Container' => 'Primer Mover', 'Empty Container' => 'Primer Mover + Container', 'Different Container' => 'Primer Mover + Different Bins'];
        $weightType = $weightTypeLabels[$row['weight_type']] ?? $row['weight_type'];

        $isPurchaseOrLocal = $row['transaction_status'] === 'Purchase' || $row['transaction_status'] === 'Local';

        return [
            'id' => $row['id'],
            'transaction_id' => $row['transaction_id'],
            'transaction_status' => $transactionStatus,
            'weight_type' => $weightType,
            'transaction_date' => $row['transaction_date'],
            'lorry_plate_no1' => $row['lorry_plate_no1'],
            'lorry_plate_no2' => $row['lorry_plate_no2'],
            'supplier_weight' => $row['supplier_weight'],
            'customer_code' => $row['customer_code'],
            'customer_name' => $row['customer_name'],
            'supplier_code' => $row['supplier_code'],
            'supplier_name' => $row['supplier_name'],
            'customer' => $isPurchaseOrLocal ? $row['supplier_name'] : $row['customer_name'],
            'product_code' => $isPurchaseOrLocal ? $row['raw_mat_code'] : $row['product_code'],
            'product_name' => $isPurchaseOrLocal ? $row['raw_mat_name'] : $row['product_name'],
            'container_no' => $row['container_no'],
            'seal_no' => $row['seal_no'],
            'invoice_no' => $row['invoice_no'],
            'purchase_order' => $row['purchase_order'],
            'delivery_no' => $row['delivery_no'],
            'transporter_code' => $row['transporter_code'],
            'transporter' => $row['transporter'],
            'destination_code' => $row['destination_code'],
            'destination' => $row['destination'],
            'remarks' => $row['remarks'],
            'gross_weight1' => $row['gross_weight1'],
            'gross_weight1_date' => $row['gross_weight1_date'],
            'tare_weight1' => $row['tare_weight1'],
            'tare_weight1_date' => $row['tare_weight1_date'],
            'nett_weight1' => $row['nett_weight1'],
            'gross_weight2' => $row['gross_weight2'],
            'gross_weight2_date' => $row['gross_weight2_date'],
            'tare_weight2' => $row['tare_weight2'],
            'tare_weight2_date' => $row['tare_weight2_date'],
            'nett_weight2' => $row['nett_weight2'],
            'final_weight' => $row['final_weight'],
            'weight_different' => $row['weight_different'],
            'is_complete' => $row['is_complete'],
            'is_cancel' => $row['is_cancel'],
            'manual_weight' => $row['manual_weight'],
            'indicator_id' => $row['indicator_id'],
            'weighbridge_id' => $row['weighbridge_id'],
            'created_date' => $row['created_date'],
            'created_by' => $row['created_by'],
            'modified_date' => $row['modified_date'],
            'modified_by' => $row['modified_by'],
            'indicator_id_2' => $row['indicator_id_2'],
            'product_description' => $row['product_description']
        ];
    }
}
