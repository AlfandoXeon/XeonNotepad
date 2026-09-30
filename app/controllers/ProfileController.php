<?php
declare(strict_types=1);

/**
 * ProfileController — View and update user profile.
 * Supports: view profile info, change password.
 */
class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user      = $this->currentUser();
        $userModel = new User();
        $userData  = $userModel->findByEmailFull($user['email']);
        $noteCount = count((new Note())->getAllByUser($user['id']));
        $notes     = (new Note())->getAllByUser($user['id']);

        $this->view('main', 'profile/index', [
            'pageTitle'   => 'Profile — ' . APP_NAME,
            'userData'    => $userData,
            'noteCount'   => $noteCount,
            'notes'       => $notes,
            'currentUser' => $user,
            'csrfToken'   => $this->generateCsrf(),
            'activeNote'  => null,
            'hideSidebar' => true,
        ]);
    }

    public function changePassword(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $user            = $this->currentUser();
        $currentPassword = $_POST['current_password']  ?? '';
        $newPassword     = $_POST['new_password']       ?? '';
        $confirmPassword = $_POST['confirm_password']   ?? '';
        $errors          = [];

        $userModel = new User();
        $userData  = $userModel->findByEmailFull($user['email']);

        // Verify current password
        if (!$userModel->verifyPassword($currentPassword, $userData['password'] ?? '')) {
            $errors[] = 'Current password is incorrect.';
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        }
        if (!empty($currentPassword) && $newPassword === $currentPassword) {
            $errors[] = 'New password must be different from your current password.';
        }

        if (empty($errors)) {
            $userModel->updatePassword($user['id'], $newPassword);
            $_SESSION['flash'] = [
                'type'    => 'success',
                'title'   => 'Password updated',
                'message' => 'Your password has been changed successfully.',
            ];
            $this->redirect('/profile');
        }

        // Re-render with errors
        $noteCount = count((new Note())->getAllByUser($user['id']));
        $notes     = (new Note())->getAllByUser($user['id']);

        $this->view('main', 'profile/index', [
            'pageTitle'      => 'Profile — ' . APP_NAME,
            'userData'       => $userData,
            'noteCount'      => $noteCount,
            'notes'          => $notes,
            'currentUser'    => $user,
            'csrfToken'      => $this->generateCsrf(),
            'activeNote'     => null,
            'passwordErrors' => $errors,
        ]);
    }
}
