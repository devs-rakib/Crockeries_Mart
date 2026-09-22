<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\CSRF;
use App\Helpers\Mailer;

class ForgotPasswordController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->sendResetLink();
            return;
        }

        $data = [
            'pageTitle' => 'Forgot Password',
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/auth/forgot_password.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    private function sendResetLink(): void
    {
        if (!CSRF::verify()) {
            Response::json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['success' => false, 'message' => 'Please enter a valid email address']);
            return;
        }

        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            Response::json(['success' => true, 'message' => 'If an account with that email exists, a reset link has been sent.']);
            return;
        }

        $this->userModel->clearResetToken($email);
        $token = $this->userModel->setResetToken($email);

        if ($token) {
            Mailer::sendPasswordReset($email, $token);
        }

        Response::json([
            'success' => true,
            'message' => 'If an account with that email exists, a reset link has been sent.',
        ]);
    }

    public function showResetForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->resetPassword();
            return;
        }

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            Session::flash('error', 'Invalid reset link');
            Response::redirect(APP_URL . '/forgot-password');
            return;
        }

        $resetData = $this->userModel->getByResetToken($token);

        if (!$resetData) {
            Session::flash('error', 'This reset link is invalid or has expired. Please request a new one.');
            Response::redirect(APP_URL . '/forgot-password');
            return;
        }

        $data = [
            'pageTitle' => 'Reset Password',
            'token'     => $token,
            'email'     => $resetData['email'],
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/auth/reset_password.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    private function resetPassword(): void
    {
        if (!CSRF::verify()) {
            Response::json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $token    = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        if (empty($token)) {
            Response::json(['success' => false, 'message' => 'Invalid reset token']);
            return;
        }

        if (strlen($password) < 6) {
            Response::json(['success' => false, 'message' => 'Password must be at least 6 characters']);
            return;
        }

        if ($password !== $confirm) {
            Response::json(['success' => false, 'message' => 'Passwords do not match']);
            return;
        }

        $resetData = $this->userModel->getByResetToken($token);

        if (!$resetData) {
            Response::json(['success' => false, 'message' => 'This reset link is invalid or has expired.']);
            return;
        }

        $hashedPassword = \App\Helpers\Auth::hashPassword($password);
        $this->userModel->update($resetData['user_id'], ['password' => $hashedPassword]);
        $this->userModel->clearResetToken($resetData['email']);

        Response::json([
            'success'  => true,
            'message'  => 'Password reset successful! You can now login.',
            'redirect' => APP_URL . '/login',
        ]);
    }
}
