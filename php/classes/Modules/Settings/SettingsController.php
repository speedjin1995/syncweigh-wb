<?php
namespace App\Modules\Settings;

use App\Core\BaseController;

class SettingsController extends BaseController
{
    private const PROFILE_FIELDS = [
        'userEmail' => ['column' => 'useremail', 'required' => true],
        'language' => ['column' => 'languages', 'required' => true]
    ];

    private const COMPANY_FIELDS = [
        'companyRegNo' => ['column' => 'company_reg_no', 'required' => true],
        'companyName' => ['column' => 'name', 'required' => true],
        'companyAddress' => ['column' => 'address_line_1', 'required' => true],
        'companyAddress2' => ['column' => 'address_line_2'],
        'companyAddress3' => ['column' => 'address_line_3'],
        'companyPhone' => ['column' => 'phone_no', 'required' => true],
        'companyFax' => ['column' => 'fax_no']
    ];

    private const PORT_FIELDS = [
        'indicator' => ['column' => 'indicator', 'required' => true],
        'serialPort' => ['column' => 'com_port', 'required' => true],
        'serialPortBaudRate' => ['column' => 'bits_per_second', 'required' => true],
        'serialPortDataBits' => ['column' => 'data_bits', 'required' => true],
        'serialPortParity' => ['column' => 'parity', 'required' => true],
        'serialPortStopBits' => ['column' => 'stop_bits', 'required' => true]
    ];

    private SettingsService $service;

    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    /**
     * Form fields with HTML tags stripped, as the legacy FILTER_SANITIZE_STRING did
     */
    private function cleanFields(array $fields): ?array
    {
        $data = $this->collectFields($fields);

        return $data === null ? null : array_map(fn ($value) => $value === null ? null : strip_tags($value), $data);
    }

    public function profile(): array
    {
        $data = $this->cleanFields(self::PROFILE_FIELDS);
        if ($data === null) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        $result = $this->service->updateProfile((int)$_SESSION['id'], $data);
        if ($result['status'] === 'success') {
            $_SESSION['language'] = $data['languages'];
        }

        return $result;
    }

    /**
     * Passwords are read raw: no trim, so the value saved matches what was typed
     */
    public function password(): array
    {
        $oldPassword = (string)($_POST['oldPassword'] ?? '');
        $newPassword = (string)($_POST['newPassword'] ?? '');

        if ($oldPassword === '' || $newPassword === '' || (string)($_POST['confirmPassword'] ?? '') === '') {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        return $this->service->changePassword((int)$_SESSION['id'], $oldPassword, $newPassword);
    }

    public function company(): array
    {
        $data = $this->cleanFields(self::COMPANY_FIELDS);
        if ($data === null) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        return $this->service->updateCompany($data);
    }

    public function port(): array
    {
        $data = $this->cleanFields(self::PORT_FIELDS);
        if ($data === null) {
            return ['status' => 'failed', 'message' => 'Please fill in all the fields'];
        }

        return $this->service->updatePort((int)$_SESSION['id'], $data);
    }
}
