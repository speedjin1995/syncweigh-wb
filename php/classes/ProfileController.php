<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/ProfileService.php';

class ProfileController extends BaseController {

    private $profileService;

    public function __construct($db) {
        parent::__construct($db);
        $this->profileService = new ProfileService($db, $this->username);
    }

    public function updateProfile() {
        $userName = $this->getRequiredPost('userName');
        $userEmail = $this->getRequiredPost('userEmail');
        $language = $this->getRequiredPost('language');
        try {
            $this->profileService->updateProfile($_SESSION['id'], $userName, $userEmail, $language);
            $_SESSION['language'] = $language;
            $this->success('Your Email / Username is updated successfully!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }

    public function changePassword() {
        $oldPassword = $this->getRequiredPost('oldPassword');
        $newPassword = $this->getRequiredPost('newPassword');
        $this->getRequiredPost('confirmPassword');
        try {
            $this->profileService->changePassword($_SESSION['id'], $oldPassword, $newPassword);
            $this->success('Update successfully!');
        } catch (Exception $e) {
            $this->failed($e->getMessage());
        }
    }
}
?>
