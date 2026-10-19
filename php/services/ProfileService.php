<?php
require_once __DIR__ . '/BaseService.php';

class ProfileService extends BaseService {

    public function updateProfile($userId, $username, $email, $language) {
        $stmt = $this->db->prepare("UPDATE Users SET username=?, useremail=?, languages=? WHERE id=?");
        if (!$stmt) throw new Exception('Failed to prepare statements!');
        $stmt->bind_param('ssss', $username, $email, $language, $userId);
        if (!$stmt->execute()) throw new Exception('Failed to update profile!');
        $stmt->close();
    }

    public function changePassword($userId, $oldPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT password FROM Users WHERE id = ?");
        if (!$stmt) throw new Exception('Data retrieve failed');
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) throw new Exception('Data retrieve failed');
        if (!password_verify($oldPassword, $row['password'])) throw new Exception('Old password is not matched');

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE Users SET password = ? WHERE id = ?");
        if (!$stmt) throw new Exception('Failed to update due to database error');
        $stmt->bind_param('ss', $hash, $userId);
        if (!$stmt->execute()) throw new Exception('Failed to update due to database error');
        $stmt->close();
    }
}
?>
