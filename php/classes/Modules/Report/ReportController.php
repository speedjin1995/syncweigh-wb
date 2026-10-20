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
}
