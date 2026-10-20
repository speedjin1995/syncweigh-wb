<?php
namespace App\Modules\Weight;

use App\Core\BaseController;

class WeightController extends BaseController
{
    private WeightService $service;

    public function __construct(WeightService $service)
    {
        $this->service = $service;
    }

    /**
     * Excel download of the weighing records that match the report filters
     */
    public function exportExcel(): array
    {
        return [
            'fileName' => 'Weight-data_' . date('Y-m-d') . '.xls',
            'content' => $this->service->excelContent($this->input(), $this->plants())
        ];
    }

    /**
     * Printable HTML of the weighing records that match the report filters
     */
    public function exportPdf(): array
    {
        return ['status' => 'success', 'message' => $this->service->pdfHtml($this->input(), $this->plants())];
    }

    private function input(): array
    {
        return $_POST + $_GET;
    }

    /**
     * Admins see every plant; everyone else only their assigned plants
     */
    private function plants(): ?array
    {
        if (in_array($_SESSION['roles'], ['ADMIN', 'SADMIN'], true)) {
            return null;
        }

        return array_values((array)($_SESSION['plant'] ?? []));
    }
}
