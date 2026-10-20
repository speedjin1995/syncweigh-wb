<?php
namespace App\Modules\Report;

use App\Core\BaseController;

class ReportController extends BaseController
{
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    /**
     * DataTables server-side list with the summary counts for the report cards
     */
    public function list(): array
    {
        $p = $this->dataTableParams();

        // Admins see every plant; everyone else only their assigned plants
        $isAdmin = in_array($_SESSION['roles'], ['ADMIN', 'SADMIN'], true);
        $plants = $isAdmin ? null : array_values((array)($_SESSION['plant'] ?? []));

        $result = $this->service->getList($_POST, $plants, $p['start'], $p['length'], $p['orderColumn'], $p['orderDir'], $p['search']);

        return $this->dataTableResponse($p['draw'], $result) + [
            'salesTotal' => $result['salesTotal'],
            'purchaseTotal' => $result['purchaseTotal'],
            'localTotal' => $result['localTotal'],
            'miscTotal' => $result['miscTotal']
        ];
    }

    /**
     * Audit log rows and column names for the selected log type
     */
    public function auditLog(): array
    {
        $type = trim((string)($_POST['selectedValue'] ?? ''));

        try {
            $from = $this->auditLogDate('fromDateSearch', 'Y-m-d 00:00:00');
            $to = $this->auditLogDate('toDateSearch', 'Y-m-d 23:59:59');
        } catch (\Exception $e) {
            return ['status' => 'failed', 'message' => 'Invalid date'];
        }

        $result = $this->service->getAuditLog($type, $from, $to, $_POST);
        if ($result === null) {
            return ['status' => 'failed', 'message' => 'Invalid report type'];
        }

        return $result;
    }

    /**
     * Posted date in the given format, or null when empty
     */
    private function auditLogDate(string $key, string $format): ?string
    {
        $value = trim((string)($_POST[$key] ?? ''));

        return $value === '' ? null : (new \DateTime($value))->format($format);
    }
}
