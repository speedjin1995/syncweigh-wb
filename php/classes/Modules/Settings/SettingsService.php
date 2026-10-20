<?php
namespace App\Modules\Settings;

use App\Core\BaseService;

class SettingsService extends BaseService
{
    private const LANGUAGES = ['en', 'zh', 'my', 'ne'];

    /**
     * Update own email and language
     */
    public function updateProfile(int $userId, array $data): array
    {
        if (!in_array($data['languages'], self::LANGUAGES, true)) {
            return ['status' => 'failed', 'message' => 'Invalid language'];
        }

        if (!$this->updateRow('Users', $data, 'id = ?', 'i', [$userId])) {
            return ['status' => 'failed', 'message' => 'Failed to update profile'];
        }

        return ['status' => 'success', 'message' => 'Your Email / Username is updated successfully!'];
    }

    /**
     * Change own password after checking the old one
     */
    public function changePassword(int $userId, string $oldPassword, string $newPassword): array
    {
        $user = $this->fetchOne("SELECT password FROM Users WHERE id = ?", 'i', [$userId]);
        if ($user === null) {
            return ['status' => 'failed', 'message' => 'Data retrieve failed'];
        }

        if (!password_verify($oldPassword, $user['password'])) {
            return ['status' => 'failed', 'message' => 'Old password is not matched'];
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if (!$this->updateRow('Users', ['password' => $hash], 'id = ?', 'i', [$userId])) {
            return ['status' => 'failed', 'message' => 'Failed to update password'];
        }

        return ['status' => 'success', 'message' => 'Update successfully!'];
    }

    /**
     * Update the company profile (single row, id 1). Company_Log is written by TRG_UPD_COMPANY.
     */
    public function updateCompany(array $data): array
    {
        $today = date('Y-m-d H:i:s');

        if (!$this->updateRow('Company', $data + ['modified_date' => $today, 'modified_by' => $this->username], 'id = ?', 'i', [1])) {
            return ['status' => 'failed', 'message' => 'Failed to update company profile'];
        }

        return ['status' => 'success', 'message' => 'Your company profile is updated successfully!'];
    }

    /**
     * Update the serial port settings of the signed-in user's weighing indicator
     */
    public function updatePort(int $userId, array $data): array
    {
        if (!$this->updateRow('Port', $data, 'weighind_id = ?', 'i', [$userId])) {
            return ['status' => 'failed', 'message' => 'Something went wrong!'];
        }

        return ['status' => 'success', 'message' => 'Your port setup is updated successfully!'];
    }
}
