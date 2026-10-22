<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/VehicleService.php';

class VehicleController extends BaseController {

    private $vehicleService;

    public function __construct($db) {
        parent::__construct($db);
        $this->vehicleService = new VehicleService($db, $this->username);
    }

    /**
     * Create (no id posted) or update a vehicle
     */
    public function save() {
        $requiredPermission = empty($_POST['id']) ? 'create' : 'edit';
        if (!hasModulePermission('Master Data', 'Vehicles', [$requiredPermission])) {
            $this->failed('You do not have permission to do this');
        }

        if (!isset($_POST['vehicleNo'])) {
            $this->failed('Please fill in all the fields');
        }

        try {
            $this->vehicleService->save($_POST);
            $this->success(empty($_POST['id']) ? 'Added Successfully!!' : 'Updated Successfully!!');
        } catch (mysqli_sql_exception $e) {
            error_log('VehicleController - ' . $e->getMessage());
            $this->failed('Something went wrong');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Vehicle by id (default), plate lookup (type lookup) or weighing screen pull (type pullCustomer)
     */
    public function get() {
        if (!isset($_POST['userID'])) {
            $this->failed('Missing Attribute');
        }

        $type = empty($_POST['type']) ? null : trim($_POST['type']);

        try {
            $message = $this->vehicleService->get(trim($_POST['userID']), $type);

            if ($type == 'lookup' && !empty($message['error'])) {
                $this->response('error', $message['message']);
            }

            $this->success($message);
        } catch (mysqli_sql_exception $e) {
            error_log('VehicleController - ' . $e->getMessage());
            $this->failed('Something went wrong');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * DataTables list (raw DataTables response, no status key)
     */
    public function list() {
        echo json_encode($this->vehicleService->list($_POST));
        exit();
    }

    /**
     * Soft delete: one id, or several ids posted as an array with type MULTI
     */
    public function delete() {
        if (!isset($_POST['userID'])) {
            $this->failed('Please fill in all the fields');
        }

        $userID = $_POST['userID'];
        $isMulti = isset($_POST['type']) && $_POST['type'] == 'MULTI';
        $ids = $isMulti ? (is_array($userID) ? $userID : explode(',', $userID)) : [$userID];

        try {
            $this->vehicleService->delete($ids);
            $this->success('Deleted');
        } catch (mysqli_sql_exception $e) {
            error_log('VehicleController - ' . $e->getMessage());
            $this->failed('Something went wrong');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    /**
     * Excel import. Rows come as a JSON body, not form fields.
     */
    public function upload() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            $this->failed('Please fill in all the fields');
        }

        // Strict mode so a failed query on an import row surfaces as an exception
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $errors = $this->vehicleService->upload($data);

        if (!empty($errors)) {
            $this->response('error', $errors);
        }

        $this->success('Added Successfully!!');
    }

    /**
     * Weighing screen: password re-check, or the daily entry status for a vehicle
     */
    public function checkEntry() {
        if (isset($_POST['verifyPassword'])) {
            $this->success('', ['valid' => $this->vehicleService->verifyPassword($_SESSION['id'], $_POST['verifyPassword'])]);
        }

        if (empty($_POST['vehicleNo'])) {
            $this->failed('Please select a vehicle');
        }

        try {
            $status = $this->vehicleService->getEntryStatus(trim($_POST['vehicleNo']), $_POST['date'] ?? null);
            $this->success('', $status);
        } catch (mysqli_sql_exception $e) {
            error_log('VehicleController - ' . $e->getMessage());
            $this->failed('Something went wrong');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }
}
?>
