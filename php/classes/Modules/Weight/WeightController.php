<?php
namespace App\Modules\Weight;

use App\Core\BaseController;

class WeightController extends BaseController
{
    private WeightService $service;
    private WeightPrintService $printService;

    public function __construct(WeightService $service, WeightPrintService $printService)
    {
        $this->service = $service;
        $this->printService = $printService;
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

    /**
     * Weighing screen list (DataTables) with the status counts for the summary cards
     */
    public function list(): array
    {
        return $this->service->getList($_POST, $this->plants(), $this->dataTableParams());
    }

    /**
     * Completed empty container list (DataTables)
     */
    public function listEmptyContainer(): array
    {
        return $this->service->getEmptyContainerList($_POST, $this->dataTableParams());
    }

    /**
     * One weighing record for the edit form (format MODAL, the default) or the row detail (format EXPANDABLE)
     */
    public function get(): array
    {
        if (!isset($_POST['userID'])) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        $format = ($_POST['format'] ?? '') !== '' ? (string)$_POST['format'] : 'MODAL';
        $type = ($_POST['type'] ?? '') !== '' ? (string)$_POST['type'] : 'Weight';

        return $this->service->getWeight((string)$_POST['userID'], $format, $type);
    }

    /**
     * Cancel one or more weighing records, or soft delete empty container records
     */
    public function delete(): array
    {
        return $this->service->cancel($_POST);
    }

    /**
     * Completed empty containers for a transaction status (posted as userID)
     */
    public function containers(): array
    {
        if (!isset($_POST['userID'])) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        return $this->service->getContainers((string)$_POST['userID']);
    }

    /**
     * First weighing of a completed empty container by container no (posted as userID)
     */
    public function emptyContainer(): array
    {
        if (!isset($_POST['userID'])) {
            return ['status' => 'failed', 'message' => 'Missing Attribute'];
        }

        return $this->service->getEmptyContainer((string)$_POST['userID']);
    }

    /**
     * Save the weighing form (create when no id is posted, otherwise update)
     */
    public function save(): array
    {
        return $this->service->save($_POST);
    }

    /**
     * Weighing slip HTML. prePrint picks the slip language (defaults to the user's language).
     * Only file = weight is supported, as the legacy print had no other working branch.
     */
    public function print(): array
    {
        if (!isset($_POST['userID'], $_POST['file'], $_POST['isEmptyContainer'])) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        if ($_POST['file'] != 'weight') {
            return ['status' => 'failed', 'message' => 'Something Goes Wrong'];
        }

        $language = isset($_POST['prePrint']) ? (string)$_POST['prePrint'] : (string)($_SESSION['language'] ?? '');

        return $this->printService->slipHtml((string)$_POST['userID'], (string)$_POST['isEmptyContainer'], (array)($_SESSION['languageArray'] ?? []), $language);
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
